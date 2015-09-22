-- drop custom value table
DROP TABLE IF EXISTS civicrm_value_dotmailer_subscription;

-- drop custom set and their fields
DELETE FROM `civicrm_custom_group` WHERE table_name = 'civicrm_value_dotmailer_subscription';

-- drop custom value table
DROP TABLE IF EXISTS civicrm_value_dotmailer_subscription_settings;

-- drop custom set and their fields
DELETE FROM `civicrm_custom_group` WHERE table_name = 'civicrm_value_dotmailer_subscription_settings';

-- drop custom value table
DROP TABLE IF EXISTS veda_civicrm_activity_type_dotmailer_subscription_settings;