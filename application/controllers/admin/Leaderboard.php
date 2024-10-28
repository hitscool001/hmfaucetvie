<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leaderboard extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_leaderboard');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->data['page'] = 'Leaderboard settings';
        $this->data['leaderboardSettings'] = $this->m_leaderboard->getSettings();
        $this->render('leaderboard', $this->data);
    }

    public function update()
    {
        $this->data['leaderboardSettings'] = $this->m_leaderboard->getSettings();
        $setting = [
            'activity_contest_reward', 'activity_contest_requirement', 'referral_contest_reward',
            'referral_contest_requirement', 'faucet_contest_reward', 'faucet_contest_requirement',
            'shortlink_contest_reward', 'shortlink_contest_requirement', 'offerwall_contest_reward',
            'offerwall_contest_requirement', 'offerwall_contest_min_value'
        ];

        foreach ($setting as $key) {
            $newValue = $this->input->post($key);
            if (isset($_POST[$key]) && $_POST[$key] != $this->data['leaderboardSettings'][$key]) {
                $this->db->set('value', $newValue);
                $this->db->where('name', $key);
                $this->db->update('setting_leaderboard');
            }
        }
        $this->session->set_flashdata('message', faucet_alert('success', 'Updated'));
        return redirect(site_url('/admin/leaderboard'));
    }
}
