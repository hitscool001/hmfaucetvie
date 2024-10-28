<?php

defined('BASEPATH') or exit('No direct script access allowed');
class M_Auth extends CI_Model
{
	public function multiple_account()
	{
		return false;
		$past = time() - 3600;
		$ip_address = $this->input->ip_address();
		$checkUser = $this->db->get_where('ip_addresses', array('ip_address' => $ip_address, 'last_use>' => $past))->num_rows();
		if ($checkUser > 0) {
			return true;
		}
		return false;
	}
	public function isCheater()
	{
		$past = time() - 86400;
		$ip_address = $this->input->ip_address();
		$check_user = $this->db->select('id')->from('users')->where(['ip_address' => $ip_address, 'last_active>' => $past])->get();
		if ($check_user->num_rows() > 1) {
			return $check_user->result_array();
		}
		return false;
	}
	public function register($email, $username, $password, $active_keys, $isocode, $country, $ref, $referralSource, $status = 'active')
	{
		$user = array(
			'email' => $email,
			'username' => $username,
			'password' => $password,
			'ip_address' => $this->input->ip_address(),
			'isocode' => $isocode,
			'country' => $country,
			'referred_by' => $ref,
			'referral_source' => $referralSource,
			'last_active' => time(),
			'joined' => time(),
			'last_firewall' => time(),
			'status' => $status
		);
		$this->db->insert('users', $user);
	}

	public function updateUser($id)
	{
		$this->db->where('id', $id);
		$this->db->set('ip_address', $this->input->ip_address());
		$this->db->set('last_active', time());
		$this->db->set('last_firewall', time());
		$this->db->update('users');
	}

	public function login($email, $password)
	{
		$check = $this->db->get_where('users', array('email' => $email));
		if (!$check->num_rows()) {
			return false;
		}

		$user = $check->result_array()[0];
		if ($password == $user['password']) {
			$this->updateUser($user['id']);
			return $user;
		}
		return false;
	}

	public function valid_referral($id)
	{
		$check = $this->db->get_where('users', array('id' => $id))->num_rows();
		return $check == 1;
	}

	public function get_user_from_token($token)
	{
		$user = $this->db->get_where('users', array('secret' => $token));
		if ($user->num_rows() == 0) {
			return false;
		}
		return $user->result_array()[0];
	}

	public function update_password($user_id, $password)
	{
		$this->db->where('id', $user_id);
		$this->db->set('password', $password);
		$this->db->set('secret', random_string('alnum', 30));
		$this->db->update('users');
	}



	public function insertForgotPassword($userId, $code)
	{
		$user = array(
			'user_id' => $userId,
			'code' => $code,
			'create_time' => time()
		);
		$this->db->insert('forgot_password', $user);
	}

	public function checkForgotPassword($code)
	{
		$check = $this->db->get_where('forgot_password', array('code' => $code));
		if ($check->num_rows() == 0) {
			return false;
		}
		$active = $check->result_array()[0];
		return $active;
	}

	public function getForgotPasswordByUserId($userId)
	{
		$check = $this->db->get_where('forgot_password', array('user_id' => $userId));
		if (!$check->num_rows()) {
			return false;
		}
		$active = $check->result_array()[0];
		return $active;
	}

	public function deleteForgotPassword($userId)
	{
		$this->db->delete('forgot_password', ['user_id' => $userId]);
	}
}
