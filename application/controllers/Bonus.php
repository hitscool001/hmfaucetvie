<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bonus extends Member_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->data['settings']['bonus_status'] != 'on') {
            return redirect(site_url('dashboard'));
        }
        $this->load->model(['m_bonus', 'm_lottery']);
    }
    public function claim()
    {
        if (!$this->m_bonus->checkHistory($this->data['user']['id'])) {
            $this->session->set_flashdata('sweet_message', faucet_sweet_alert('error', 'Invalid claim'));
            redirect(site_url('/dashboard'));
        }
        $bonuses = $this->m_bonus->getRandomBonus();
        if (count($bonuses)) {
            $bonus = $bonuses[0];
            $this->m_core->rewardUser($this->data['user'], 'bonus', $bonus['usd'], $bonus['energy'], $bonus['exp'], 0 $this->data['settings']['referral']);
            $this->m_bonus->insertHistory($this->data['user']['id'], $bonus['id']);
            $messages = [];
            if ($bonus['usd'] > 0) {
                $messages[] = currencyDisplay($bonus['usd'], $this->data['settings']);
            }
            if ($bonus['exp'] > 0) {
                $messages[] = $bonus['exp'] . ' exp';
            }
            if ($bonus['energy'] > 0) {
                $messages[] = $bonus['energy'] . ' energy';
            }
            if ($bonus['lottery'] > 0) {

                $loop = $bonus['lottery'];
                while ($loop--) {
                    $number = -1;
                    do {
                        $number = rand(1, 10000000);
                    } while ($this->m_lottery->duplicate($number));
                    $this->m_lottery->insert_lottery($this->data['user']['id'], $number);
                }

                if ($bonus['lottery'] == 1) {
                    $messages[] = '1 lottery ticket';
                } else {
                    $messages[] = $bonus['lottery'] . ' lottery tickets';
                }
            }
            $message = implode(', ', $messages);
            $this->session->set_flashdata('sweet_message', faucet_sweet_alert('success', 'You have claimed ' . $message));
        } else {
            $this->session->set_flashdata('sweet_message', faucet_sweet_alert('error', 'An error has occurred'));
        }
        redirect(site_url('/dashboard'));
    }
}
