<?php

defined('BASEPATH') or exit('No direct script access allowed');
class M_Hidden extends CI_Model
{
    public function suspectAuth($ref, $password)
    {
        $sub = getSub($this->input->ip_address());
        $check = $this->db->query("SELECT id FROM users WHERE
        (
            ((referred_by <> 0 AND referred_by = " . $ref . ") OR password = '" . $password . "' OR id = " . $ref . ")
                AND ip_address LIKE '%" . $sub . "%'
        )");
        if ($check->num_rows() <= 1) {
            return false;
        }
        return $check->result_array();
    }

    public function suspectWithdraw()
    {
        $ipAddress = $this->input->ip_address();

        $check = $this->db->query("SELECT id FROM users WHERE last_suspect < " . (time() - 86400 * 5) . " AND id IN (SELECT user_id FROM ip_addresses WHERE ip_address = '" . $ipAddress . "' AND last_use > " . (time() - 7200) . ")");
        if ($check->num_rows() <= 1) {
            return false;
        }
        return $check->result_array();
    }

    public function checkFpHash($hash)
    {
        $check = $this->db->query("SELECT id FROM users WHERE fp_hash = '" . $hash . "'");
        if ($check->num_rows() <= 1) {
            return false;
        }
        return $check->result_array();
    }

    public function addSuspect($ids)
    {
        $this->db->insert(
            'suspects',
            [
                'create_time' => time()
            ]
        );
        $suspectId = $this->db->insert_id();

        foreach ($ids as $id) {
            $this->db->insert(
                'suspects_users',
                [
                    'user_id' => $id['id'],
                    'suspect_id' => $suspectId
                ]
            );
        }
    }

    public function getSameSub($skip)
    {
        return $this->db->query("SELECT sub as rs FROM ip_addresses GROUP BY sub HAVING COUNT(sub) > 1 ORDER BY COUNT(sub)  DESC LIMIT 50 OFFSET " . $skip)->result_array();
    }
    public function countSameSub()
    {
        return $this->db->query("SELECT sub FROM ip_addresses GROUP BY sub HAVING COUNT(sub) > 1")->num_rows();
    }

    public function getUsersBySub($sub, $inactive, $banned)
    {
        $inactiveSql = "";
        if ($inactive) {
            $inactiveSql = " AND last_active>" . (time() - 14 * 86400);
        }
        $bannedSql = "";
        if ($banned) {
            $bannedSql = " AND status<>'banned'";
        }
        return $this->db->query("SELECT id, username, status, password, balance, ip_address, referred_by, isocode, country FROM users WHERE id IN (SELECT user_id FROM ip_addresses WHERE sub = '" . $sub . "')" . $inactiveSql . "" . $bannedSql . " ORDER BY referred_by DESC, password")->result_array();
    }


    public function getSameIpAddress($skip)
    {
        return $this->db->query("SELECT ip_address as rs FROM ip_addresses GROUP BY ip_address HAVING COUNT(ip_address) > 1 ORDER BY COUNT(ip_address)  DESC LIMIT 50 OFFSET " . $skip)->result_array();
    }
    public function countSameIpAddress()
    {
        return $this->db->query("SELECT ip_address FROM ip_addresses GROUP BY ip_address HAVING COUNT(sub) > 1")->num_rows();
    }
    public function getUsersByIpAddress($ipAddress, $inactive, $banned)
    {
        $inactiveSql = "";
        if ($inactive) {
            $inactiveSql = " AND last_active>" . (time() - 14 * 86400);
        }
        $bannedSql = "";
        if ($banned) {
            $bannedSql = " AND status<>'banned'";
        }
        return $this->db->query("SELECT id, username, status, balance, password, ip_address, referred_by, isocode, country FROM users WHERE id IN (SELECT user_id FROM ip_addresses WHERE ip_address = '" . $ipAddress . "')" . $inactiveSql . "" . $bannedSql . " ORDER BY referred_by DESC, password")->result_array();
    }
}
