<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends Member_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(['m_dashboard', 'm_hidden', 'm_bonus', 'm_active']);
		$this->load->helper(['string', 'coinbase_helper']);
		$this->load->library('form_validation');
	}
	public function index()
	{
		$this->data['page'] = 'Dashboard';

		$this->data['referralCount'] = $this->m_dashboard->get_ref($this->data['user']['id']);
		$this->data['methods'] = $this->db->get_where('currencies', ['status' => 1])->result_array();
		$this->data['bonusAvailable'] = $this->m_bonus->checkHistory($this->data['user']['id']);
		$this->data['bonus'] = min($this->data['settings']['max_bonus'], $this->data['settings']['level_bonus'] * $this->data['user']['level']);
		$this->data['captcha_display'] = get_captcha($this->data['settings'], base_url(), 'faucet_captcha');
		$this->render('dashboard', $this->data);
	}

	public function withdraw()
	{

		#Check captcha
		$captcha = $this->input->post('captcha');
		$checkCaptcha = false;
		setcookie('captcha', $captcha, time() + 86400 * 10);
		switch ($captcha) {
			case "recaptchav3":
				$checkCaptcha = verifyRecaptchaV3($this->input->post('recaptchav3'), $this->data['settings']['recaptcha_v3_secret_key']);
				break;
			case "recaptchav2":
				$checkCaptcha = verifyRecaptchaV2($this->input->post('g-recaptcha-response'), $this->data['settings']['recaptcha_v2_secret_key']);
				break;
			case "solvemedia":
				$checkCaptcha = verifySolvemedia($this->data['settings']['v_key'], $this->data['settings']['h_key'], $this->input->ip_address(), $this->input->post('adcopy_challenge'), $this->input->post('adcopy_response'));
				break;
			case "hcaptcha":
				$checkCaptcha = verifyHcaptcha($this->input->post('h-captcha-response'), $this->data['settings']['hcaptcha_secret_key'], $this->input->ip_address());
				break;
		}
		if (!$checkCaptcha) {
			if ($this->data['user']['fail'] >= $this->data['settings']['captcha_fail_limit'] - 1) {
				$this->m_core->insertCheatLog($this->data['user']['id'], 'Too many wrong captcha.', 0);
				$this->m_core->lockUser($this->data['user']['id']);
				return redirect(site_url('locked'));
			} else {
				$this->m_core->wrongCaptcha($this->data['user']['id']);
			}
			$this->session->set_flashdata('message', faucet_alert('danger', 'Invalid Captcha'));
			return redirect(site_url('dashboard'));
		}

		$this->form_validation->set_rules('amount', 'Amount', 'trim|required|is_numeric|greater_than[0]');
		$this->form_validation->set_rules('method', 'Method', 'trim|required|integer');
		$this->form_validation->set_rules('wallet', 'Wallet', 'trim|required|max_length[60]');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('error', validation_errors()));
			return redirect(site_url('/dashboard'));
		}
		if (!preg_match("/^[a-zA-Z0-9-]+$/", $this->input->post('wallet')) && !filter_var($this->input->post('wallet'), FILTER_VALIDATE_EMAIL)) {
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('error', 'Invalid Wallet'));
			return redirect(site_url('/dashboard'));
		}
		$usdAmount = format_money($this->input->post('amount') * $this->data['settings']['currency_rate']);
		if ($this->data['user']['verified'] == 0) {
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('warning', 'Please verify your email'));
			return redirect(site_url('/dashboard'));
		}

		if ($usdAmount > $this->data['user']['balance']) {
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('warning', '???'));
			return redirect(site_url('/dashboard'));
		}

		$todayWithdrawAmount = $this->m_dashboard->getTotalWithdrawToday($this->data['user']['id']);
		if ($todayWithdrawAmount + $usdAmount > $this->data['settings']['withdraw_limit']) {
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('error', 'Daily withdraw limit reached!'));
			return redirect(site_url('/dashboard'));
		}
		$wallet = $this->db->escape_str($this->input->post('wallet'));
		$sameWallet = $this->m_dashboard->checkSameWallet($this->data['user']['id'], $wallet);
		if ($sameWallet) {
			foreach ($sameWallet as $cheater) {
				$this->m_core->updateStatus($cheater['user_id'], 'banned');
				$this->m_core->insertCheatLog($cheater['user_id'], "Same wallet to other account: " . $wallet, $this->input->ip_address());
			}
			$this->m_core->updateStatus($this->data['user']['id'], 'banned');
			$this->m_core->insertCheatLog($this->data['user']['id'],  "Same wallet to other account: " . $wallet, $this->input->ip_address());
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('error', 'You are banned because of cheating'));
			return redirect(site_url('/dashboard'));
		}

		$method = $this->m_core->getCurrency($this->input->post('method'));
		if (!$method || $method['status'] == 0) {
			return redirect(site_url('dashboard'));
		}

		$satoshiAmount = floor($usdAmount * 100000000 / $method['price']);

		if ($usdAmount == 0 || $usdAmount < $method['minimum_withdrawal']) {
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('warning', 'Minimum withdrawal is ' . currencyDisplay($method['minimum_withdrawal'], $this->data['settings'])));
			return redirect(site_url('/dashboard'));
		}

		$checkSub = $this->m_hidden->suspectWithdraw();
		if ($checkSub) {
			foreach ($checkSub as $id) {
				$this->m_core->updateStatus($id['id'], 'suspect');
				$this->m_core->insertCheatLog($id['id'],  "Suspect IP", getSub($this->input->ip_address()));
			}
			$this->m_dashboard->reduceBalance($this->data['user']['id'], $usdAmount);
			$this->m_dashboard->insert_history($this->data['user']['id'], 0, $method['id'], $wallet, $usdAmount);
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', 'You have sent a withdrawal request successfully!'));
			return redirect(site_url('/dashboard'));
		}

		if ($this->data['user']['status'] == 'suspect') {
			$this->m_dashboard->reduceBalance($this->data['user']['id'], $usdAmount);
			$this->m_dashboard->insert_history($this->data['user']['id'], 0, $method['id'], $wallet, $usdAmount);
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', 'You have sent a withdrawal request successfully!'));
			return redirect(site_url('/dashboard'));
		}

		if ($method['wallet'] == 'faucetpay') {
			$hash = $this->data['user']['fp_hash'];
			if ($this->data['user']['fp_hash'] == 'none') {
				$check = fpCheck($wallet, $method['api'], $method['code']);
				if ($check['status'] !== 200) {
					$this->session->set_flashdata('sweet_message', faucet_sweet_alert('warning', 'Your address is not linked to your FaucetPay account'));
					return redirect(site_url('/dashboard'));
				} else {
					$hash = $check['payout_user_hash'];
					$this->m_dashboard->updateFPHash($this->data['user']['id'], $check['payout_user_hash']);
				}
			}
			$checkHash = $this->m_hidden->checkFpHash($hash);
			if ($checkHash) {
				foreach ($checkHash as $id) {
					$this->m_core->updateStatus($id['id'], 'banned');
					$this->m_core->insertCheatLog($id['id'], "Same FaucetPay to other account", $this->input->ip_address());
				}
				return redirect(site_url('/dashboard'));
			}
			if ($this->data['settings']['instant_withdraw'] == 'on') {
				$result = faucetpay($wallet, $this->input->ip_address(), $satoshiAmount, $method['api'], $method['code'], $referral = false);
				if ($result["status"] == 200) {
					$this->m_dashboard->reduceBalance($this->data['user']['id'], $usdAmount);
					$this->m_dashboard->insert_history($this->data['user']['id'], 1, $method['id'], $wallet, $usdAmount);
					$this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', currencyDisplay($usdAmount, $this->data['settings']) . ' has been sent to your account!'));
					if ($this->data['user']['fp_hash'] == 'none') {
						$this->m_dashboard->updateFPHash($this->data['user']['id'], $result['payout_user_hash']);
					}
				} else {
					$this->session->set_flashdata('sweet_message', faucet_sweet_alert('warning', $result['message']));
				}
			} else {
				$this->m_dashboard->reduceBalance($this->data['user']['id'], $usdAmount);
				$this->m_dashboard->insert_history($this->data['user']['id'], 0, $method['id'], $wallet, $usdAmount);
				$this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', 'You have sent a withdrawal request successfully!'));
			}
		} else if ($method['wallet'] == 'coinbase') {
			if ($this->data['settings']['instant_withdraw'] == 'on') {
				$result = coinBaseSendPayment($wallet, $method['code'], $satoshiAmount / 100000000, $method['account_number'], $method['api'], $method['token']);
				if ($result["success"]) {
					$this->m_dashboard->reduceBalance($this->data['user']['id'], $usdAmount);
					$this->m_dashboard->insert_history($this->data['user']['id'], 1, $method['id'], $wallet, $usdAmount);
					$this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', currencyDisplay($usdAmount, $this->data['settings']) . ' has been sent to your account!'));
				} else {
					$this->session->set_flashdata('sweet_message', faucet_sweet_alert('warning', $result['message']));
				}
			} else {
				$this->m_dashboard->reduceBalance($this->data['user']['id'], $usdAmount);
				$this->m_dashboard->insert_history($this->data['user']['id'], 0, $method['id'], $wallet, $usdAmount);
				$this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', 'You have sent a withdrawal request successfully!'));
			}
		} else if ($method['wallet'] == 'payeer') {
			if ($this->data['settings']['instant_withdraw'] == 'on') {
				include APPPATH . 'third_party/payeer.php';
				$payeer = new CPayeer($method['account_number'], $method['api_id'], $method['api']);
				if ($payeer->isAuth()) {
					$arTransfer = $payeer->transfer(array(
						'curIn' => 'USD',
						'sum' => $usdAmount,
						'curOut' => $method['code'],
						'to' => $wallet,
						'comment' => 'Payment from' . $this->data['settings']['name']
					));
					if (empty($arTransfer['errors'])) {
						$this->m_dashboard->reduceBalance($this->data['user']['id'], $usdAmount);
						$this->m_dashboard->insert_history($this->data['user']['id'], 1, $method['id'], $wallet, $usdAmount);
						$this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', currencyDisplay($usdAmount, $this->data['settings']) . ' has been sent to your account!'));
					} else {
						$this->session->set_flashdata('sweet_message', faucet_sweet_alert('warning', 'Error! Please try again (' . $arTransfer["errors"][0] . ')'));
					}
				} else {
					$this->session->set_flashdata('sweet_message', faucet_sweet_alert('warning', 'Error! Please try again (' . $payeer->getErrors()[0] . ')'));
				}
			} else {
				$this->m_dashboard->reduceBalance($this->data['user']['id'], $usdAmount);
				$this->m_dashboard->insert_history($this->data['user']['id'], 0, $method['id'], $wallet, $usdAmount);
				$this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', 'Sent withdraw request'));
			}
		} else if ($method['wallet'] == 'custom') {
			$this->m_dashboard->reduceBalance($this->data['user']['id'], $usdAmount);
			$this->m_dashboard->insert_history($this->data['user']['id'], 0, $method['id'], $wallet, $usdAmount);
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', 'You have sent a withdrawal request successfully!'));
		}
		if ($wallet != $this->data['user']['wallet']) {
			$this->m_dashboard->update_wallet($this->data['user']['id'], $this->db->escape_str($wallet));
		}
		return redirect(site_url('/dashboard'));
	}

	public function read_notifications()
	{
		$this->m_dashboard->readAllNotifications($this->data['user']['id']);
	}

	public function resend()
	{
		if ($this->data['user']['verified'] == 1) {
			return redirect(site_url('/dashboard'));
		}
		$oldActive = $this->m_active->getActiveByUserId($this->data['user']['id']);
		if ($oldActive && time() - $oldActive['create_time'] < 300) {
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('error', 'You can request once every 5 minutes only'));
			return redirect(site_url('/dashboard'));
		}
		$siteName = $this->data['settings']['name'];
		$secret = random_string('alnum', 30);
		$activeUrl = site_url('/active/' . $secret);
		$message = <<<EOT
			<table class="body-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: transparent; margin: 0;">
			<tbody><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
			<td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
			<td class="container" width="600" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
				<div class="content" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
					<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope="" itemtype="http://schema.org/ConfirmAction" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; margin: 0; border: none;">
						<tbody><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
							<td class="content-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; color: #495057; font-size: 14px; vertical-align: top; margin: 0;padding: 30px; box-shadow: 0 0.75rem 1.5rem rgba(18,38,63,.03); ;border-radius: 7px; background-color: #fff;" valign="top">
								<meta itemprop="name" content="Confirm Email" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
								<table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
									<tbody><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
										<td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
											Welcome to $siteName. Please confirm your email address by clicking the link below.
										</td>
									</tr>
									<tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
										<td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
											You won't be able to withdraw until you have verified your account.
										</td>
									</tr>
									<tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
										<td class="content-block" itemprop="handler" itemscope="" itemtype="http://schema.org/HttpActionHandler" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
											<a href="$activeUrl" itemprop="url" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #34c38f; margin: 0; border-color: #34c38f; border-style: solid; border-width: 8px 16px;">Confirm email address</a>
										</td>
									</tr>
									<tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
										<td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
											<b>$siteName</b>
											<p>Support Team</p>
										</td>
									</tr>
								</tbody>
								</table>
							</td>
						</tr>
					</tbody></table>
				</div>
			</td>
		</tr>
	</tbody></table>
EOT;

		if (sendMail($this->data['user']['email'], 'Active your account', $message, $this->data['settings'])) {
			$this->m_active->insertActive($this->data['user']['id'], $secret);
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', 'Email sent'));
		} else {
			$this->session->set_flashdata('sweet_message', faucet_sweet_alert('error', 'Failed to sent email'));
		}
		return redirect(site_url('/dashboard'));
	}
}
