<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bonus extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('m_bonus');
    }

    public function index()
    {
        $this->data['page'] = 'Bonus Settings';
        $this->data['bonuses'] = $this->m_bonus->get();
        $this->render('bonus', $this->data);
    }
    public function add()
    {
        $usd = $this->db->escape_str($this->input->post('usd'));
        $energy = $this->db->escape_str($this->input->post('energy'));
        $exp = $this->db->escape_str($this->input->post('exp'));
        $lottery = $this->db->escape_str($this->input->post('lottery'));
        $this->m_bonus->add($usd, $energy, $exp, $lottery);
        redirect(site_url('admin/bonus'));
    }
    public function update($id)
    {
        $usd = $this->db->escape_str($this->input->post('usd'));
        $energy = $this->db->escape_str($this->input->post('energy'));
        $exp = $this->db->escape_str($this->input->post('exp'));
        $lottery = $this->db->escape_str($this->input->post('lottery'));
        $this->m_bonus->update($id, $usd, $exp, $energy, $lottery);
        redirect(site_url('admin/bonus'));
    }
    public function delete($id = 0)
    {
        $this->db->delete('daily_bonuses', array('id' => $id));
        redirect(site_url('admin/bonus'));
    }
}
