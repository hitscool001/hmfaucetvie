<?php
defined('BASEPATH') or exit('No direct script access allowed');
class M_Suspect extends CI_Model
{
    public function getSuspectedIds()
    {
        return $this->db->query("SELECT DISTINCT id FROM suspects ORDER BY id DESC")->result_array();
    }

    public function getUserBySuspectId($id)
    {
        return $this->db->query("SELECT users.*, users.id AS user_id FROM users JOIN suspects_users ON users.id = suspects_users.user_id WHERE suspect_id = " . $id)->result_array();
    }

    public function getSuspectByIds($ids)
    {
        return $this->db->query('SELECT DISTINCT suspect_id, COUNT(*) as cnt FROM `suspects_users` WHERE user_id IN (' . $ids . ') GROUP BY suspect_id')->result_array();
    }

    public function getSuspectUserByEmailDomain($domain)
    {
        return $this->db->query('SELECT id FROM users WHERE email LIKE "%@' . $domain . '"')->result_array();
    }

    public function insertSuspectEmail($userId)
    {
        $insert = [
            'user_id' => $userId,
            'create_time' => time()
        ];
        $this->db->insert('not_allowed_emails', $insert);
    }
}
