-- drop custom value table
DROP TABLE IF EXISTS civicrm_value_dotmailer_subscription;

-- drop custom set and their fields
DELETE FROM `civicrm_custom_group` WHERE table_name = 'civicrm_value_dotmailer_subscription';

-- drop custom value table
DROP TABLE IF EXISTS veda_civicrm_dotmailer_subscription_settings;

-- Delete dotmailer settings
DELETE FROM `civicrm_setting` WHERE group_name = 'Dotmailer Preferences';

-- Delete sync from scheduled jobs
DELETE FROM `civicrm_job` WHERE api_entity = 'Dotmailer' AND api_action = 'sync';