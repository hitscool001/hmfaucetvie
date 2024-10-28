<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leaderboard extends Member_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_leaderboard');
	}
	public function index()
	{
		$this->data['page'] = 'Leaderboard';
		$this->data['leaderboardSettings'] = $this->m_leaderboard->getSettings();

		$leaderboardInfo = $this->cache->get('leaderboard_info');
		if (!$leaderboardInfo) {
			$leaderboardInfo = [
				'topLevel' => $this->m_leaderboard->getTopLevel(),
				'topClaimer' => $this->m_leaderboard->getTopClaimer($this->data['settings']['admin_username'], $this->data['leaderboardSettings']['activity_contest_requirement']),
				'topReferral' => $this->m_leaderboard->getTopReferral($this->data['settings']['admin_username'], $this->data['leaderboardSettings']['referral_contest_requirement']),
				'topFaucet' => $this->m_leaderboard->getTopFaucet($this->data['settings']['admin_username'], $this->data['leaderboardSettings']['faucet_contest_requirement']),
				'topShortlink' => $this->m_leaderboard->getTopShortlink($this->data['settings']['admin_username'], $this->data['leaderboardSettings']['shortlink_contest_requirement']),
				'topOfferwall' => $this->m_leaderboard->getTopOfferwall($this->data['settings']['admin_username'], $this->data['leaderboardSettings']['offerwall_contest_requirement'])
			];
			$this->cache->save('leaderboard_info', $leaderboardInfo, 600);
		}

		$this->data = array_merge($this->data, $leaderboardInfo);
		$this->render('leaderboard', $this->data);
	}
}
