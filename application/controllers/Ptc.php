<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ptc extends Member_Controller
{
	public function __construct()
	{
		parent::__construct();
		if ($this->data['settings']['ptc_status'] != 'on') {
			return redirect(site_url('dashboard'));
		}
		$this->load->model('m_ptc');
	}

	public function index()
	{
		$this->data['page'] = 'Paid To Click';


		$this->data['totalReward'] = 0;

		$this->data['ptcAds'] = $this->m_ptc->availableAds($this->data['user']['id']);
		$this->data['totalAds'] = count($this->data['ptcAds']);
		$this->data['totalTime'] = 0;

		foreach ($this->data['ptcAds'] as $ad) {
			$this->data['totalReward'] += $ad['reward'];
			$this->data['totalTime'] += $ad['timer'];
		}
		$this->render('ptc', $this->data);
	}

	public function view($id = 0)
	{
		if (!is_numeric($id)) {
			$this->session->set_flashdata('message', faucet_alert('danger', 'Invalid Ad'));
			return redirect(site_url('/ptc'));
		}

		$this->data['ads'] = $this->m_ptc->get_ads_from_id($id);

		if (!$this->data['ads']) {
			$this->session->set_flashdata('message', faucet_alert('danger', 'Invalid Ad'));
			return redirect(site_url('/ptc'));
		}

		#Captcha
		$this->data['captcha_display'] = get_captcha($this->data['settings'], base_url(), 'ptc_captcha');
		$this->session->set_userdata(array('start_view' => time()));
		$this->load->view('user_template/ptc_view_ad', $this->data);
	}

	public function verify($id = 0)
	{
		$this->load->helper('string');

		$startTime = $this->session->start_view;
		$this->session->unset_userdata('start_view');

		// is id mumeric
		if (!is_numeric($id)) {
			$this->session->set_flashdata('message', faucet_alert('danger', 'Invalid Click'));
			return redirect(site_url('/ptc'));
		}

		$ad = $this->m_ptc->get_ads_from_id($id);

		// does ad exist and view time valid
		if (!$ad || time() - $startTime < $ad['timer']) {
			$this->session->set_flashdata('message', faucet_alert('danger', 'Invalid Click'));
			return redirect(site_url('/ptc'));
		}

		if ($ad['views'] >= $ad['total_view']) {
			$this->session->set_flashdata('message', faucet_alert('danger', 'This Ad has reached maximum views'));
			return redirect(site_url('/ptc'));
		}

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
			return redirect(site_url('ptc'));
		}

		$check = $this->m_ptc->verify($this->data['user']['id'], $id);

		if (!$check) {
			$this->session->set_flashdata('message', faucet_alert('danger', 'Invalid Ad'));
			return redirect(site_url('/ptc'));
		}

		$this->m_core->rewardUser($this->data['user'], 'ptc', $ad['reward'], 0, $this->data['settings']['ptc_exp_reward'], 0, $this->data['settings']['referral']);

		$this->m_ptc->addView($ad['id']);
		if ($ad['views'] + 1 == $ad['total_view']) {
			$this->m_ptc->completed($ad['id']);
		}
		$this->m_ptc->insert_history($this->data['user']['id'], $ad['id'], $ad['reward']);
		if ($this->data['user']['fail'] > 0) {
			$this->m_core->resetFail($this->data['user']['id']);
		}
		$this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', currencyDisplay($ad['reward'], $this->data['settings']) . ' has been added to your balance'));
		return redirect(site_url('/ptc'));
	}
}
