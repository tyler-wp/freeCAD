DELETE FROM on_duty;

ALTER TABLE `on_duty` ADD `timestamp` TIMESTAMP NOT NULL AFTER `uid`;

ALTER TABLE `vehicles` ADD `vehicle_isStolen` ENUM('true','false') NOT NULL DEFAULT 'false' AFTER `vehicle_ownername`;

ALTER TABLE `vehicles` ADD `vehicle_isImpounded` ENUM('true','false') NOT NULL DEFAULT 'false' AFTER `vehicle_isStolen`;

ALTER TABLE `vehicles` ADD `vehicle_impoundedTill` TEXT NULL DEFAULT NULL AFTER `vehicle_isImpounded`;

ALTER TABLE `vehicles` ADD `vehicle_impoundedCount` INT(11) NOT NULL DEFAULT '0' AFTER `vehicle_impoundedTill`;