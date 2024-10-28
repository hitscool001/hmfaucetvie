<?php
defined('BASEPATH') or exit('No direct script access allowed');
class M_Active extends CI_Model
{
	public function getUser($activeKey)
	{
		$user = $this->db->get_where('users', array('secret' => $activeKey));
		if ($user->num_rows() == 0) {
			return false;
		}
		return $user->result_array()[0];
	}
	public function active($userId)
	{
		$this->db->where('id', $userId);
		$this->db->set('verified', 1);
		$this->db->update('users');
	}

	public function updateReferralCount($userId)
	{
		$this->db->where('id', $userId);
		$this->db->set('ref_count', 'ref_count+1', FALSE);
		$this->db->update('users');
	}

	public function insertActive($userId, $code)
	{
		$user = array(
			'user_id' => $userId,
			'code' => $code,
			'create_time' => time()
		);
		$this->db->insert('actives', $user);
	}

	public function checkActive($code)
	{
		$check = $this->db->get_where('actives', array('code' => $code));
		if (!$check->num_rows()) {
			return false;
		}
		$active = $check->result_array()[0];
		return $active;
	}

	public function getActiveByUserId($userId)
	{
		$check = $this->db->get_where('actives', array('user_id' => $userId));
		if (!$check->num_rows()) {
			return false;
		}
		$active = $check->result_array()[0];
		return $active;
	}
}
