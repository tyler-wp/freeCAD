CREATE TABLE `companies` (
    `c_id` int(11) NOT NULL AUTO_INCREMENT,
    `c_name` varchar(64) NOT NULL,
    `c_owner` varchar(64) NOT NULL,
    `created_on` text NOT NULL,
    PRIMARY KEY (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `on_duty` ADD `uid` INT(11) NOT NULL AFTER `status`;

ALTER TABLE `settings` ADD `civ_char_limit` INT(11) NOT NULL DEFAULT '3' AFTER `group_banGroup`;

CREATE TABLE `shift_logs` (
  `id` int(11) NOT NULL,
  `i_id` int(11) NOT NULL,
  `s_start` text NOT NULL,
  `s_end` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `shift_logs`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `shift_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users` ADD `theme` VARCHAR(64) NOT NULL DEFAULT 'default' AFTER `ban_reason`;