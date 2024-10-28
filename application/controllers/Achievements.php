<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Achievements extends Member_Controller
{
  public function __construct()
  {
    parent::__construct();
    if ($this->data['settings']['achievement_status'] != 'on') {
      return redirect(site_url('dashboard'));
    }
    $this->load->model('m_achievements');
  }

  public function index()
  {
    $this->data['page'] = 'Achievements';
    $this->data['achievements'] = $this->m_achievements->getAchievements($this->data['user']['id']);

    $this->data['categoried'] = [];
    for ($i = 0; $i < count($this->data['achievements']); ++$i) {
      if ($this->data['achievements'][$i]['type'] == 0) {
        $this->data['achievements'][$i]['name'] = 'faucet';
        $this->data['achievements'][$i]['completed'] = $this->data['user']['today_faucet'];
        $this->data['achievements'][$i]['description'] = 'Complete ' . $this->data['achievements'][$i]['condition'] . ' faucet claims';
      } else if ($this->data['achievements'][$i]['type'] == 1) {
        $this->data['achievements'][$i]['name'] = 'shortlink';
        $this->data['achievements'][$i]['completed'] = $this->m_achievements->checkLink($this->data['user']['id']);
        $this->data['achievements'][$i]['description'] = 'Complete ' . $this->data['achievements'][$i]['condition'] . ' shortlinks';
      } else if ($this->data['achievements'][$i]['type'] == 2) {
        $this->data['achievements'][$i]['name'] = 'ptc';
        $this->data['achievements'][$i]['completed'] = $this->m_achievements->checkPtc($this->data['user']['id']);
        $this->data['achievements'][$i]['description'] = 'View ' . $this->data['achievements'][$i]['condition'] . ' PTC ads';
      } else if ($this->data['achievements'][$i]['type'] == 3) {
        $this->data['achievements'][$i]['name'] = 'lottery';
        $this->data['achievements'][$i]['completed'] = $this->m_achievements->checkLottery($this->data['user']['id']);
        $this->data['achievements'][$i]['description'] = 'Buy ' . $this->data['achievements'][$i]['condition'] . ' lotteries';
      } else if ($this->data['achievements'][$i]['type'] == 4) {
        $this->data['achievements'][$i]['name'] = 'offerwall';
        $this->data['achievements'][$i]['completed'] = $this->m_achievements->checkOfferwall($this->data['user']['id']);
        $this->data['achievements'][$i]['description'] = 'Make ' . $this->data['achievements'][$i]['condition'] . ' offerwall claims';
      }

      $this->data['achievements'][$i]['progress'] = min(100, $this->data['achievements'][$i]['completed'] * 100 / $this->data['achievements'][$i]['condition']);
    }

    $this->render('achievements', $this->data);
  }

  public function claim($id = 0)
  {

    if (!is_numeric($id)) {
      return redirect('achievements');
    }

    $achievement = $this->m_achievements->getAchievementFromId($id);
    if (!$achievement) {
      return redirect('achievements');
    }

    if (!$this->m_achievements->checkHistory($id, $this->data['user']['id'])) {
      return redirect('achievements');
    }

    if ($achievement['type'] == 0) {
      if ($achievement['condition'] > $this->data['user']['today_faucet']) {
        return redirect(site_url('achievements'));
      }
    } else if ($achievement['type'] == 1) {
      if ($achievement['condition'] > $this->m_achievements->checkLink($this->data['user']['id'])) {
        return redirect(site_url('achievements'));
      }
    } else if ($achievement['type'] == 2) {
      if ($achievement['condition'] > $this->m_achievements->checkPtc($this->data['user']['id'])) {
        return redirect(site_url('achievements'));
      }
    } else if ($achievement['type'] == 3) {
      if ($achievement['condition'] > $this->m_achievements->checkLottery($this->data['user']['id'])) {
        return redirect(site_url('achievements'));
      }
    } else if ($achievement['type'] == 4) {
      if ($achievement['condition'] > $this->m_achievements->checkOfferwall($this->data['user']['id'])) {
        return redirect(site_url('achievements'));
      }
    }


    $this->m_core->rewardUser($this->data['user'], 'achievement', $achievement['reward_usd'], $achievement['reward_energy'], $this->data['settings']['achievement_exp_reward'], 0, $this->data['settings']['referral']);
    $this->m_achievements->insertHistory($this->data['user']['id'], $achievement['id'], $achievement['reward_usd']);
    $this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', currencyDisplay($achievement['reward_usd'], $this->data['settings']) . ' and ' . $achievement['reward_energy'] . ' engergy have been added to your balance'));
    return redirect(site_url('/achievements'));
  }
}
