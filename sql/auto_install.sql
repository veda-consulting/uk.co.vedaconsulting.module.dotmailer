-- Create custom table to store DM details for activity types
DROP TABLE IF EXISTS `veda_civicrm_dotmailer_subscription_settings`;
CREATE TABLE IF NOT EXISTS `veda_civicrm_dotmailer_subscription_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Default MySQL primary key',
  `activity_type_id` int(10) unsigned NOT NULL COMMENT 'Activity Type ID',
  `campaign_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dotmailer_address_book_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dotmailer_campaign_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;