-- Create custom table to store DM details for activity types
DROP TABLE IF EXISTS `veda_civicrm_activity_type_dotmailer_subscription_settings`;
CREATE TABLE IF NOT EXISTS `veda_civicrm_activity_type_dotmailer_subscription_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Default MySQL primary key',
  `activity_type_id` int(10) unsigned NOT NULL COMMENT 'Table that this extends',
  `dotmailer_address_book_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dotmailer_campaign_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_activity_type_id` (`activity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;