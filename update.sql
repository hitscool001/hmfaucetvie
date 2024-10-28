ALTER TABLE `users` ADD COLUMN `bonus_claim` int(32) DEFAULT 0;
CREATE TABLE `daily_bonuses` (
  `id` int(32) UNSIGNED NOT NULL,
  `usd` decimal(10,6) DEFAULT 0.000000,
  `exp` int(32) NOT NULL DEFAULT 0,
  `energy` int(32) NOT NULL DEFAULT 0,
  `lottery` int(32) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `daily_bonuses` ADD PRIMARY KEY (`id`);
  ALTER TABLE `daily_bonuses` MODIFY `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT;

  CREATE TABLE `daily_bonus_history` (
  `id` int(32) UNSIGNED NOT NULL,
  `user_id` int(32) UNSIGNED NOT NULL,
  `bonus_id` int(32) UNSIGNED NOT NULL,
  `claim_time` int(32) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `daily_bonus_history` ADD PRIMARY KEY (`id`);
  ALTER TABLE `daily_bonus_history` MODIFY `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `currencies` ADD COLUMN `balance` decimal(16,8) DEFAULT 0;

CREATE TABLE `setting_leaderboard` (
  `id` int(32) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `setting_leaderboard`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `setting_leaderboard`
  MODIFY `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
INSERT INTO `setting_leaderboard` VALUES 
(NULL,'leaderboard_date', '0'),
(NULL,'activity_contest_reward', ''),
(NULL,'activity_contest_requirement', '1'),
(NULL,'referral_contest_reward', ''),
(NULL,'referral_contest_requirement', '1'),
(NULL,'faucet_contest_reward', ''),
(NULL,'faucet_contest_requirement', '1'),
(NULL,'shortlink_contest_reward', ''),
(NULL,'shortlink_contest_requirement', '1'),
(NULL,'offerwall_contest_reward', ''),
(NULL,'offerwall_contest_requirement', '1'),
(NULL,'offerwall_contest_min_value', '0.01');


  CREATE TABLE `coupons` (
  `id` int(32) UNSIGNED NOT NULL,
  `code` varchar(100) UNIQUE NOT NULL,
  `balance_reward` decimal(10,6) DEFAULT 0.000000,
  `dep_balance_reward` decimal(10,6) DEFAULT 0.000000,
  `energy_reward` int(32) UNSIGNED NOT NULL,
  `advertising_discount` int(32) UNSIGNED NOT NULL,
  `used` int(32) UNSIGNED DEFAULT 0,
  `number_of_use` int(32) UNSIGNED DEFAULT 0,
  `expired_at` int(32) UNSIGNED DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `coupons` ADD PRIMARY KEY (`id`);
  ALTER TABLE `coupons` MODIFY `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT;

    CREATE TABLE `coupon_history` (
  `id` int(32) UNSIGNED NOT NULL,
  `coupon_id` int(32) UNSIGNED NOT NULL,
  `user_id` int(32) UNSIGNED NOT NULL,
  `claim_time` int(32) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `coupon_history` ADD PRIMARY KEY (`id`);
  ALTER TABLE `coupon_history` MODIFY `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT;

  INSERT INTO `settings` (`name`, `value`) VALUES
('trusted_mails', 'gmail.com,hotmail.com');

CREATE TABLE `actives` (
  `id` int(32) UNSIGNED NOT NULL,
  `user_id` int(32) UNSIGNED NOT NULL,
  `code` varchar(100) UNIQUE NOT NULL,
  `create_time` int(32) UNSIGNED DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `actives` ADD PRIMARY KEY (`id`);
ALTER TABLE `actives` MODIFY `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `forgot_password` (
  `id` int(32) UNSIGNED NOT NULL,
  `user_id` int(32) UNSIGNED NOT NULL,
  `code` varchar(100) UNIQUE NOT NULL,
  `create_time` int(32) UNSIGNED DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `forgot_password` ADD PRIMARY KEY (`id`);
ALTER TABLE `forgot_password` MODIFY `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT INTO `settings` VALUES (NULL, 'coupon_top_ad', ''),
(NULL, 'coupon_footer_ad', ''),(NULL, 'coupon_bottom_ad', ''),(NULL, 'coupon_status', 'off');

ALTER TABLE `users` ADD COLUMN `locked_until` int(32) DEFAULT 0;

INSERT INTO `settings` VALUES 
(NULL,'currency_name_singular', 'token'),
(NULL,'currency_name_plural', 'tokens');

INSERT INTO `settings` VALUES 
(NULL,'withdraw_captcha', 'recaptchav2');

  CREATE TABLE `not_allowed_emails` (
  `id` int(32) UNSIGNED NOT NULL,
  `user_id` int(32) UNSIGNED NOT NULL,
  `create_time` int(32) UNSIGNED DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `not_allowed_emails` ADD PRIMARY KEY (`id`);
  ALTER TABLE `not_allowed_emails` MODIFY `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `suspects` (
  `id` int(32) UNSIGNED NOT NULL,
  `create_time` int(32) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `suspects`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `suspects`
  MODIFY `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `suspects_users` (
  `id` int(32) UNSIGNED NOT NULL,
  `user_id` int(32) UNSIGNED NOT NULL,
  `suspect_id` int(32) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `suspects_users`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `suspects_users`
  MODIFY `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT INTO `settings` VALUES (NULL, 'bonus_status', 'on');


ALTER TABLE `users` DROP COLUMN `secret`;
ALTER TABLE `users` DROP COLUMN `token`;