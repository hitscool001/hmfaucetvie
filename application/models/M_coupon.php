<?php
defined('BASEPATH') or exit('No direct script access allowed');
class M_Coupon extends CI_Model
{
    public function getCoupon($code)
    {
        $coupon = $this->db->get_where('coupons', array('code' => $code));
        if ($coupon->num_rows() == 0) {
            return false;
        }
        return $coupon->result_array()[0];
    }

    public function checkCouponHistory($userId, $couponId)
    {
        $coupon = $this->db->get_where('coupon_history', array('coupon_id' => $couponId, 'user_id' => $userId));
        if ($coupon->num_rows() == 0) {
            return false;
        }
        return $coupon->result_array()[0];
    }

    public function getCoupons()
    {
        return $this->db->get('coupons')->result_array();
    }

    public function add($code, $balanceReward, $depBalanceReward, $energyReward, $advertisingDiscount, $numberOfUse, $expiredIn)
    {
        $insert = array(
            'code' => $code,
            'balance_reward' => $balanceReward,
            'dep_balance_reward' => $depBalanceReward,
            'energy_reward' => $energyReward,
            'advertising_discount' => $advertisingDiscount,
            'number_of_use' => $numberOfUse,
            'expired_at' => $expiredIn == 0 ? 0 : time() + 86400 * $expiredIn,
        );
        $this->db->insert('coupons', $insert);
    }
    public function update($id, $code, $balanceReward, $depBalanceReward, $energyReward, $advertisingDiscount, $numberOfUse)
    {
        $this->db->where('id', $id);
        $this->db->set('code', $code);
        $this->db->set('balance_reward', $balanceReward);
        $this->db->set('dep_balance_reward', $depBalanceReward);
        $this->db->set('energy_reward', $energyReward);
        $this->db->set('advertising_discount', $advertisingDiscount);
        $this->db->set('number_of_use', $numberOfUse);
        $this->db->update('coupons');
    }
    public function increaseUsed($id)
    {
        $this->db->where('id', $id);
        $this->db->set('used', 'used+1', FALSE);
        $this->db->update('coupons');
    }

    public function insertHistory($userId, $couponId)
    {
        $insert = array(
            'user_id' => $userId,
            'coupon_id' => $couponId,
            'claim_time' => time()
        );
        $this->db->insert('coupon_history', $insert);
    }
}
