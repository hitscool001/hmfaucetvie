<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Coupon extends Member_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['m_coupon']);
        $this->load->library('form_validation');
    }
    public function index()
    {
        $this->data['page'] = 'Coupon';
        $this->data['captchaDisplay'] = get_captcha($this->data['settings'], base_url(), 'faucet_captcha');
        $this->render('coupon', $this->data);
    }

    public function check()
    {
        $this->form_validation->set_rules('code', 'Coupon code', 'trim|required|min_length[1]|max_length[100]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid code',
            ]);
        }
        $code = $this->db->escape_str($this->input->post('code'));
        $coupon = $this->m_coupon->getCoupon($code);

        if (!$coupon) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid code',
            ]);
            die();
        }

        if ($this->m_coupon->checkCouponHistory($this->data['user']['id'], $coupon['id'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'You have already redeemed this coupon',
            ]);
            die();
        }
        if (($coupon['number_of_use'] > 0 && $coupon['used'] >= $coupon['number_of_use']) || ($coupon['expired_at'] > 0 && time() > $coupon['expired_at'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'This coupon code is expired',
            ]);
            die();
        }
        echo json_encode([
            'status' => 'success',
            'message' => 'You get ' . $coupon['advertising_discount'] . '% discounted',
        ]);
    }

    public function redeem()
    {
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
            $this->session->set_flashdata('message', faucet_alert('danger', 'Invalid captcha'));
            return redirect(site_url('coupon'));
        }

        $this->form_validation->set_rules('code', 'Coupon code', 'trim|required|min_length[1]|max_length[100]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', faucet_alert('danger', validation_errors()));
            return redirect(site_url('coupon'));
        }
        $code = $this->db->escape_str($this->input->post('code'));
        $coupon = $this->m_coupon->getCoupon($code);

        if (!$coupon) {
            $this->session->set_flashdata('message', faucet_alert('danger', 'Invalid code'));
            return redirect(site_url('coupon'));
        }

        if ($this->m_coupon->checkCouponHistory($this->data['user']['id'], $coupon['id'])) {
            $this->session->set_flashdata('message', faucet_alert('danger', 'You have already redeemed this coupon'));
            return redirect(site_url('coupon'));
        }

        if (($coupon['number_of_use'] > 0 && $coupon['used'] >= $coupon['number_of_use']) || ($coupon['expired_at'] > 0 && time() > $coupon['expired_at'])) {
            $this->session->set_flashdata('message', faucet_alert('danger', 'This coupon code is expired'));
            return redirect(site_url('coupon'));
        }

        $this->m_coupon->increaseUsed($coupon['id']);
        $this->m_coupon->insertHistory($this->data['user']['id'], $coupon['id']);
        $this->m_core->rewardUser($this->data['user'], 'coupon', $coupon['balance_reward'], $coupon['energy_reward'], 0, $coupon['dep_balance_reward'], $this->data['settings']['referral']);

        $rewards = [];
        if ($coupon['balance_reward'] > 0) {
            array_push($rewards, currencyDisplay($coupon['balance_reward'], $this->data['settings']));
        }
        if ($coupon['dep_balance_reward'] > 0) {
            array_push($rewards, currencyDisplay($coupon['dep_balance_reward'], $this->data['settings']));
        }
        if ($coupon['energy_reward'] > 0) {
            array_push($rewards, $coupon['energy_reward']);
        }

        $this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', implode(', ', $rewards) . ' have been added to your account'));
        redirect(site_url('coupon'));
    }
}
