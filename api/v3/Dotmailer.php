<?php

/**
 * Dotmailer API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
 
/**
 * Dotmailer Get Address Books API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */ 
function civicrm_api3_dotmailer_getaddressbooks($params) {
  // Check if API user details are set
  $apiEmail   = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP, 'api_email_address');
  $apiPassword   = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP, 'api_password');
  if (empty($apiEmail) || empty($apiPassword)) {
    return;
  }

  $dotmailer = CRM_Dotmailer_Utils::dotmailer();

  // Get list of address books
  try {
    $results = $dotmailer->ListAddressBooks();
  } catch (Exception $e) {
    return array();
  }

  $addressBooks = array();
  foreach($results as $addressbook) {
    $addressBooks[$addressbook->ID] = $addressbook->Name;
  }

  return civicrm_api3_create_success($addressBooks);
}  

/**
 * Dotmailer Get Campaigns API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */ 
function civicrm_api3_dotmailer_getcampaigns($params) {
  // Check if API user details are set
  $apiEmail   = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP, 'api_email_address');
  $apiPassword   = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP, 'api_password');
  if (empty($apiEmail) || empty($apiPassword)) {
    return;
  }
  
  $dotmailer = CRM_Dotmailer_Utils::dotmailer();

  // Get list of campaigns
  try {
    $results = $dotmailer->ListCampaigns(1000, 0);
  } catch (Exception $e) {
    return array();
  }
  
  $campaigns = array();
  foreach($results as $campaign) {
    $campaigns[$campaign->Id] = $campaign->Name;
  }

  return civicrm_api3_create_success($campaigns);
}

/**
 * Dotmailer Get Data Fields API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */ 
function civicrm_api3_dotmailer_getdatafields($params) {
  $dotmailer = CRM_Dotmailer_Utils::dotmailer();

  // Get list of data fields
  try {
    $results = $dotmailer->ContactDataFields();
  } catch (Exception $e) {
    return array();
  }
  
  $dataFields = array();
  foreach($results as $campaign) {
    $dataFields[$campaign->Id] = $campaign->Name;
  }

  return civicrm_api3_create_success($campaigns);
}

/**
 * CiviCRM to Mailchimp Sync
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */ 
function civicrm_api3_dotmailer_sync($params) {
  $result = array();
  $result = CRM_Dotmailer_Utils::processSync($params);
  
  return civicrm_api3_create_success();
}
