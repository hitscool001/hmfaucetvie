<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Coupon extends Admin_Controller
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
        $this->data['coupons'] = $this->m_coupon->getCoupons();
        $this->render('coupon', $this->data);
    }

    public function add()
    {
        $this->form_validation->set_rules('code', 'Code', 'trim|required|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('balance_reward', 'Balance reward', 'trim|required|numeric');
        $this->form_validation->set_rules('dep_balance_reward', 'Deposit balance reward', 'trim|required|numeric');
        $this->form_validation->set_rules('energy_reward', 'Energy reward', 'trim|required|greater_than[-1]|numeric');
        $this->form_validation->set_rules('advertising_discount', 'Advertising discount', 'trim|required|greater_than[-1]|integer');
        $this->form_validation->set_rules('number_of_use', 'Number of use', 'trim|required|greater_than[-1]|integer');
        $this->form_validation->set_rules('expired_in', 'Expired in', 'trim|required|greater_than[-1]|integer');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', faucet_alert('danger', validation_errors()));
            return redirect(site_url('/admin/coupon'));
        }
        $code = $this->db->escape_str($this->input->post('code'));
        $balanceReward = $this->db->escape_str($this->input->post('balance_reward'));
        $depBalanceReward = $this->db->escape_str($this->input->post('dep_balance_reward'));
        $energyReward = $this->db->escape_str($this->input->post('energy_reward'));
        $advertisingDiscount = $this->db->escape_str($this->input->post('advertising_discount'));
        $numberOfUse = $this->db->escape_str($this->input->post('number_of_use'));
        $expiredIn = $this->db->escape_str($this->input->post('expired_in'));
        $this->m_coupon->add($code, $balanceReward, $depBalanceReward, $energyReward, $advertisingDiscount, $numberOfUse, $expiredIn);
        redirect(site_url('/admin/coupon'));
    }

    public function update($id)
    {
        $this->form_validation->set_rules('code', 'Code', 'trim|required|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('balance_reward', 'Balance reward', 'trim|required|numeric');
        $this->form_validation->set_rules('dep_balance_reward', 'Deposit balance reward', 'trim|required|numeric');
        $this->form_validation->set_rules('energy_reward', 'Energy reward', 'trim|required|greater_than[-1]|numeric');
        $this->form_validation->set_rules('advertising_discount', 'Advertising discount', 'trim|required|greater_than[-1]|integer');
        $this->form_validation->set_rules('number_of_use', 'Number of use', 'trim|required|greater_than[-1]|integer');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', faucet_alert('danger', validation_errors()));
            return redirect(site_url('/admin/coupon'));
        }
        $code = $this->db->escape_str($this->input->post('code'));
        $balanceReward = $this->db->escape_str($this->input->post('balance_reward'));
        $depBalanceReward = $this->db->escape_str($this->input->post('dep_balance_reward'));
        $energyReward = $this->db->escape_str($this->input->post('energy_reward'));
        $advertisingDiscount = $this->db->escape_str($this->input->post('advertising_discount'));
        $numberOfUse = $this->db->escape_str($this->input->post('number_of_use'));
        $id = $this->db->escape_str($id);
        $this->m_coupon->update($id, $code, $balanceReward, $depBalanceReward, $energyReward, $advertisingDiscount, $numberOfUse);
        redirect(site_url('/admin/coupon'));
    }

    public function delete($id)
    {
        $id = $this->db->escape_str($id);
        $this->db->delete('coupons', array('id' => $id));
        redirect(site_url('/admin/coupon'));
    }
}
