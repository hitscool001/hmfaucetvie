<?php
defined('BASEPATH') or exit('No direct script access allowed');
class M_Bonus extends CI_Model
{

    public function get()
    {
        return $this->db->get('daily_bonuses')->result_array();
    }

    public function add($usd, $exp, $energy, $lottery)
    {
        $insert = array(
            'usd' => $usd,
            'exp' => $exp,
            'energy' => $energy,
            'lottery' => $lottery,
        );
        $this->db->insert('daily_bonuses', $insert);
    }

    public function update($id, $usd, $exp, $energy, $lottery)
    {
        $this->db->where('id', $id);
        $this->db->set('usd', $usd);
        $this->db->set('exp', $exp);
        $this->db->set('energy', $energy);
        $this->db->set('lottery', $lottery);
        $this->db->update('daily_bonuses');
    }

    public function getRandomBonus()
    {
        return $this->db->query("SELECT * FROM daily_bonuses ORDER BY RAND() LIMIT 1")->result_array();
    }

    public function insertHistory($userId, $bonusId)
    {
        $insert = array(
            'user_id' => $userId,
            'bonus_id' => $bonusId,
            'claim_time' => time()
        );
        $this->db->insert('daily_bonus_history', $insert);
    }

    public function checkHistory($userId)
    {
        $todayMidnight = strtotime('today midnight');
        $check = $this->db->query('SELECT COUNT(*) AS cnt FROM daily_bonus_history WHERE user_id=' . $userId . ' AND claim_time > ' . $todayMidnight . ' LIMIT 1')->result_array()[0]['cnt'];
        return $check == 0;
    }
}
