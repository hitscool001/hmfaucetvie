<?php
defined('BASEPATH') or exit('No direct script access allowed');
class M_Coinflip extends CI_Model
{

    public function insertHistory($userId, $coin, $result, $betAmount, $profit)
    {
        $insert = array(
            'user_id' => $userId,
            'coin' => $coin,
            'result' => $result,
            'bet_amount' => $betAmount,
            'profit' => $profit,
            'claim_time' => time()
        );
        $this->db->insert('coinflip_history', $insert);
        return $this->db->insert_id();
    }

    public function getHistory($userId)
    {
        $this->db->order_by('id', "desc")->limit(10);
        return $this->db->get_where('coinflip_history', ['user_id' => $userId])->result_array();
    }

    public function addBalance($userId, $amount)
    {
        $this->db->where('id', $userId);
        $this->db->set('balance', 'balance+' . $amount, FALSE);
        $this->db->set('total_earned', 'total_earned+' . $amount, FALSE);
        $this->db->set('last_active', time());
        $this->db->update('users');
    }

    public function reduceBalance($userId, $amount)
    {
        $this->db->where('id', $userId);
        $this->db->set('balance', 'balance-' . $amount, FALSE);
        $this->db->set('total_earned', 'total_earned-' . $amount, FALSE);
        $this->db->set('last_active', time());
        $this->db->update('users');
    }
}
