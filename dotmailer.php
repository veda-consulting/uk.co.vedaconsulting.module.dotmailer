<?php

require_once 'dotmailer.civix.php';
require_once 'vendor/dotmailer/DotMailer.php';

// Dotmailer - options
// FIXME: Move this list to option values
$GLOBALS["DotMailerAudienceType"] = array(
      'Unknown' => 'Unknown',
      'B2C' => 'B2C',
      'B2B' => 'B2B',
      'B2M' => 'B2M',
      );

$GLOBALS["DotMailerOptInType"] = array(
      'Unknown' => 'Unknown',
      'Single' => 'Single',
      'Double' => 'Double',
      'VerifiedDouble' => 'VerifiedDouble',
      );

$GLOBALS["DotMailerEmailType"] = array(
      'PlainText' => 'PlainText',
      'Html' => 'Html',
      );

/* CiviCRM fields and Dotmailer fields mapping
 *
 * Structure:
 *    'entity' => array(
 *      'CiviCRM field 1' => 'Dotmailer field 1',
 *      'CiviCRM field 2' => 'Dotmailer field 2',
 *      'CiviCRM field 3' => 'Dotmailer field 3',
 *    )
 *
 * CiviCRM Fields examples (Refer to API return fields):
 * Core fields
 *    street_address
 *    city
 *    created_date
 * Custom fields
 *    custom_1
 *    custom_2 
 */
define ('DOTMAILER_PROCESS_CUSTOM_DATA_FIELDS', 1); // 1 (Yes) or 0 (No)
$GLOBALS["DotMailerCiviCRMDataFieldsMapping"] = array(
    'contact' => array(
        'custom_27' => 'FIRSTDONATIONDATE',
        'custom_28' => 'LASTDONATIONDATE',
        'custom_29' => 'CIVICONTRIBUTIONS',
        'custom_30' => 'CIVIGROUP',
      ),
    );

define ('DOTMAILER_CONTRIBUTION_ACTIVITY_TYPE_NAME' , 'Contribution Created');
define ('DOTMAILER_RECURRING_CONTRIBUTION_ACTIVITY_TYPE_NAME' , 'Recurring Contribution Created');
define ('DOTMAILER_SETTINGS_TABLE_NAME' , 'veda_civicrm_dotmailer_subscription_settings');
define ('DOTMAILER_ACTIVITY_RELATED_CONTRIBUTION_TABLE_NAME' , 'civicrm_value_related_contribution');

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function dotmailer_civicrm_config(&$config) {
  _dotmailer_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function dotmailer_civicrm_xmlMenu(&$files) {
  _dotmailer_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function dotmailer_civicrm_install() {

  // Create a sync job
  $params = array(
    'sequential' => 1,
    'name'          => 'Send to Dotmailer',
    'description'   => 'Add contacts to Dotmailer address book.',
    'run_frequency' => 'Daily',
    'api_entity'    => 'Dotmailer',
    'api_action'    => 'sync',
    'is_active'     => 0,
  );
  $result = civicrm_api3('job', 'create', $params);

  _dotmailer_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function dotmailer_civicrm_uninstall() {
  _dotmailer_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function dotmailer_civicrm_enable() {
  _dotmailer_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function dotmailer_civicrm_disable() {
  _dotmailer_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function dotmailer_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _dotmailer_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function dotmailer_civicrm_managed(&$entities) {
  _dotmailer_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function dotmailer_civicrm_caseTypes(&$caseTypes) {
  _dotmailer_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function dotmailer_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _dotmailer_civix_civicrm_alterSettingsFolders($metaDataFolders);
}


/**
 * Implementation of hook_civicrm_navigationMenu
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_permission
 */
function dotmailer_civicrm_navigationMenu(&$params){
  $parentId   = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Mailings', 'id', 'name');
  $dmSettings = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Dotmailer_Settings', 'id', 'name');
  $maxId      = max(array_keys($params));
  $dmMaxId    = empty($dmSettings) ? $maxId+1 : $dmSettings;

  $params[$parentId]['child'][$dmMaxId] = array(
        'attributes' => array(
          'label'     => ts('Dotmailer Settings'),
          'name'      => 'Dotmailer_Settings',
          'url'       => CRM_Utils_System::url('civicrm/dotmailer/settings', 'reset=1', TRUE),
          'active'    => 1,
          'parentID'  => $parentId,
          'operator'  => NULL,
          'navID'     => $dmMaxId,
          'permission'=> 'administer CiviCRM',
        ),
  );
}

/**
 * Implementation of hook_civicrm_post
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function dotmailer_civicrm_post( $op, $objectName, $objectId, &$objectRef ) {
  // Backoffice - Create activity
  if ($op == 'create' && $objectName == 'Activity') {

    // Proceed with Dotmailer subscription
    CRM_Dotmailer_Utils::processDotmailerSubscription($objectId);
  }

  // Create bespoke activity when a contribution is created
  // so that we can process dotmailer subscription from activity
  //if ($op == 'create' && $objectName == 'Contribution' && !empty($objectRef->campaign_id) && $objectRef->campaign_id != 'null') {
  if ($op == 'create' && $objectName == 'Contribution') {
    CRM_Dotmailer_Utils::createActivityForContribution($objectRef);
  }
}