<?php
defined('BASEPATH') or exit('No direct script access allowed');
class M_Core extends CI_Model
{
	public function getSettings()
	{
		$settings = $this->db->get('settings')->result_array();
		foreach ($settings as $value) {
			$result[$value['name']] = $value['value'];
		}
		return $result;
	}
	public function rewardUser($user, $type, $usd = false, $energy = false, $exp = false, $depBalance = false, $referralReward = 0)
	{
		$referredUserId = 0;
		$this->db->where('id', $user['id']);
		if ($usd) {
			$this->db->set('balance', 'balance+' . $usd, FALSE);
			$this->db->set('total_earned', 'total_earned+' . $usd, FALSE);
		}
		if ($energy) {
			$this->db->set('energy', 'energy+' . $energy, FALSE);
		}
		if ($exp) {
			$this->db->set('exp', 'exp+' . $exp, FALSE);
			if ($user['exp'] + $exp >= ($user['level'] + 1) * 100) {
				if ($user['level'] == MIN_REFERRAL_LEVEL - 1 && $user['referred_by'] != 0) {
					$referredUserId = $user['referred_by'];
				}
				$this->db->set('level', 'level+1', FALSE);
			}
		}
		if ($depBalance) {
			$this->db->set('dep_balance', 'dep_balance+' . $depBalance, FALSE);
		}

		if ($type == 'faucet') {
			$this->db->set('faucet_count', 'faucet_count+1', FALSE);
			$this->db->set('faucet_count_tmp', 'faucet_count_tmp+1', FALSE);
			$this->db->set('today_faucet', 'today_faucet+1', FALSE);
			$this->db->set('last_claim', time());
		} else if ($type === 'link') {
			$this->db->set('shortlink_count_tmp', 'shortlink_count_tmp+1', FALSE);
			$this->db->set('shortlink_count', 'shortlink_count+1', FALSE);
		}

		$this->db->set('last_active', time());
		$this->db->update('users');

		if ($referredUserId != 0) {
			$this->db->where('id', $referredUserId);
			$this->db->set('ref_count', 'ref_count+1', FALSE);
			$this->db->update('users');
		}

		if ($user['referred_by'] != 0) {
			$amount = $usd * $referralReward / 100;
			if ($amount > 0) {
				$this->update_referral($user['referred_by'], $amount);
			}
		}
	}
	public function countLinkHistory($user_id)
	{
		$past = time() - 86400;
		$ip_address = $this->input->ip_address();
		return $this->db->query("SELECT COUNT(link_id) as cnt FROM link_history WHERE claim_time>$past AND (ip_address='$ip_address' OR user_id=$user_id)")->result_array()[0]['cnt'];
	}
	public function countAllLinksView()
	{
		return $this->db->query("SELECT IFNULL(SUM(view_per_day), 0) as cnt FROM links")->result_array()[0]['cnt'];
	}
	public function countAvailableAds($user_id)
	{
		$past = time() - 86400;
		$ip_address = $this->input->ip_address();
		return $this->db->query("SELECT COUNT(*) AS cnt FROM ptc_ads WHERE views < total_view AND status = 'active' AND id NOT IN (SELECT ad_id FROM ptc_history WHERE claim_time>$past AND (ip_address='$ip_address' OR user_id=$user_id))")->result_array()[0]['cnt'];
	}
	public function countAvailableTasks($user_id)
	{
		return $this->db->query("SELECT COUNT(*) AS cnt FROM tasks WHERE id NOT IN (SELECT task_id FROM task_history WHERE user_id = " . $user_id . ") AND id NOT IN (SELECT task_id FROM task_submission WHERE user_id = " . $user_id . ")")->result_array()[0]['cnt'];
	}
	public function getUserFromId($id)
	{
		$user = $this->db->get_where('users', array('id' => $id));
		if ($user->num_rows() == 0) {
			return false;
		}
		return $user->result_array()[0];
	}
	public function get_user_from_email($email)
	{
		$user = $this->db->get_where('users', array('email' => $email));
		if ($user->num_rows() == 0) {
			return false;
		}
		return $user->result_array()[0];
	}

	public function update_referral($id, $amount)
	{
		$this->db->where('id', $id);
		$this->db->set('balance', 'balance+' . $amount, FALSE);
		$this->db->set('total_earned', 'total_earned+' . $amount, FALSE);
		$this->db->update('users');
	}

	public function lastActive($userId)
	{
		$user = $this->db->query("SELECT last_active FROM users WHERE id = " . $userId);
		if ($user->num_rows() == 0) {
			return 0;
		}
		return $user->result_array()[0]['last_active'];
	}

	public function updateIsocode($id, $isocode, $country)
	{
		$this->db->where('id', $id);
		$this->db->set('isocode', $isocode);
		$this->db->set('country', $country);
		$this->db->update('users');
	}

	public function ban($id, $reason)
	{
		$this->db->where('id', $id);
		$this->db->set('status', $reason);
		$this->db->update('users');
	}
	public function banIp($ip, $reason)
	{
		$this->db->where('ip_address', $ip);
		$this->db->set('status', $reason);
		$this->db->update('users');
	}

	public function claim_fail($id, $status)
	{
		if (is_numeric($status)) {
			if ($status >= 4) {
				$this->db->set('status', 'Cheating');
			} else {
				$this->db->set('status', $status + 1);
			}
			$this->db->where('id', $id);
			$this->db->update('users');
		}
	}

	public function newIp()
	{
		$ipAddress = $this->input->ip_address();
		$check = $this->db->query("SELECT COUNT(*) as cnt FROM ip_addresses WHERE ip_address='" . $ipAddress . "'")->result_array()[0]['cnt'];
		return ($check == 0);
	}

	public function newIpUser($userId)
	{
		$ipAddress = $this->input->ip_address();
		$check = $this->db->query("SELECT COUNT(*) as cnt FROM ip_addresses WHERE ip_address='" . $ipAddress . "' AND user_id = " . $userId)->result_array()[0]['cnt'];
		return ($check == 0);
	}

	public function insertNewIp($userId)
	{
		$sub = getSub($this->input->ip_address());
		$insert = [
			'user_id' => $userId,
			'ip_address' => $this->input->ip_address(),
			'last_use' => time(),
			'sub' => $sub
		];
		$this->db->insert('ip_addresses', $insert);
	}

	public function updateIpLastUse($userId)
	{
		$this->db->set('last_use', time());
		$this->db->where('user_id', $userId);
		$this->db->where('ip_address', $this->input->ip_address());
		$this->db->update('ip_addresses');
	}

	public function getPages()
	{
		return $this->db->query("SELECT title, slug FROM pages WHERE priority <> 0 ORDER BY priority DESC")->result_array();
	}

	public function getCurrency($id)
	{
		$currency = $this->db->get_where('currencies', array('id' => $id));
		if ($currency->num_rows() == 0) {
			return false;
		}
		return $currency->result_array()[0];
	}

	public function insertCheatLog($userId, $log, $ipAddress = false)
	{
		if (!$ipAddress) {
			$ipAddress = $this->input->ip_address();
		}
		$insert = [
			'user_id' => $userId,
			'log' => $log,
			'ip_address' => $ipAddress,
			'create_time' => time()
		];
		$this->db->insert('cheat_logs', $insert);
		return $this->db->insert_id();
	}
	public function addExp($userId, $amount)
	{
		$this->db->set('exp', 'exp+' . $amount, FALSE);
		$this->db->set('claims', 'claims+' . $amount, FALSE);
		$this->db->where('id', $userId);
		$this->db->update('users');
	}
	public function levelUp($userId)
	{
		$this->db->set('level', 'level+1', FALSE);
		$this->db->where('id', $userId);
		$this->db->update('users');
	}
	public function countLottery()
	{
		return $this->db->query("SELECT COUNT(*) as cnt FROM lotteries")->result_array()[0]['cnt'];
	}
	public function getCurrencies()
	{
		return $this->db->get('currencies')->result_array();
	}
	public function firewallLock($userId)
	{
		$this->db->set('status', 'firewall');
		$this->db->where('id', $userId);
		$this->db->update('users');
	}
	public function unlockFirewall($userId)
	{
		$this->db->set('status', 'ok');
		$this->db->set('last_firewall', time());
		$this->db->where('id', $userId);
		$this->db->update('users');
	}
	public function wrongCaptcha($userId)
	{
		$this->db->set('fail', 'fail+1', FALSE);
		$this->db->where('id', $userId);
		$this->db->update('users');
	}
	public function resetFail($userId)
	{
		$this->db->set('fail', '0');
		$this->db->where('id', $userId);
		$this->db->update('users');
	}
	public function addNotification($userId, $content, $type)
	{
		$insert = [
			'user_id' => $userId,
			'content' => $content,
			'type' => $type,
			'create_time' => time()
		];
		$this->db->insert('notifications', $insert);
	}
	public function getNotifications($userId)
	{
		$this->db->order_by('id', 'desc')->limit(20);
		return $this->db->get_where('notifications', ['user_id' => $userId])->result_array();
	}
	public function countUnreadNotifications($userId)
	{
		return $this->db->query("SELECT COUNT(*) as cnt FROM notifications WHERE status = 0 AND user_id = " . $userId)->result_array()[0]['cnt'];
	}
	public function updateStatus($id, $status)
	{
		if ($status == 'suspect') {
			$this->db->where(['last_suspect<' => time() - 86400 * 5, 'id' => $id]);
		} else {
			$this->db->where('id', $id);
		}
		$this->db->where('status<>', 'banned');
		$this->db->set('status', $status);
		$this->db->update('users');

		if ($status == 'banned') {
			$this->db->delete('withdraw_history', ['type' => 0, 'user_id' => $id]);
		}
	}
	public function lockUser($id)
	{
		$this->db->where('id', $id);
		$this->db->set('locked_until', time() + 3600);
		$this->db->set('fail', 0);
		$this->db->update('users');
	}
	public function addOtherLog($userId, $name, $amount)
	{
		$insert = [
			'user_id' => $userId,
			'name' => $name,
			'amount' => $amount,
			'claim_time' => time()
		];
		$this->db->insert('other_history', $insert);
	}
}
