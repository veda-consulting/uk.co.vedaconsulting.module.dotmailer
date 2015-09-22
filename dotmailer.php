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
$GLOBALS["DotMailerCiviCRMDataFieldsMapping"] = array(
    'contact' => array(
        'custom_27' => 'FIRSTDONATIONDATE',
        'custom_28' => 'LASTDONATIONDATE',
        'custom_29' => 'CIVICONTRIBUTIONS',
        'custom_30' => 'CIVIGROUP',
      ),
    );

define ('DOTMAILER_CONTRIBUTION_ACTIVITY_TYPE_NAME' , 'Contribution Created');

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function dotmailer_civicrm_config(&$config) {
  _dotmailer_civix_civicrm_config($config);

  $value = CRM_Dotmailer_Utils::getActivityTypeForContributionCreation();
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
 * Implementation of hook_civicrm_buildForm
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function dotmailer_civicrm_buildForm($formName, &$form) {

  if ($formName == 'CRM_Admin_Form_Options') {
    $gName = $form->getVar('_gName');
    if ($gName != 'activity_type') {
      return;
    }

    // Set defaults
    $activityTypeId = $form->getVar('_id');
    if (!empty($activityTypeId)) {
      $defaults = array();
      $dmSettings = CRM_Dotmailer_Utils::getDotmailerDetailsForActivityType($activityTypeId);  
      $defaults['dotmailer_address_book'] = $dmSettings->dotmailer_address_book_id;
      $defaults['dotmailer_campaign'] = $dmSettings->dotmailer_campaign_id;

      $form->setDefaults($defaults);
    }

    // Add dotmailer fields to form
    _dotmailer_civicrm_buildForm_add_dotmailer_fields_to_form($form);
  }

  if ($formName == 'CRM_Campaign_Form_Campaign') {

    // jQuery Dialog dont have action value set for ADD
    $action = $form->getAction();
    if (empty($action)) {
      $action = 1;
    }
    if ($action == CRM_Core_Action::ADD OR $action == CRM_Core_Action::UPDATE) {
      // Display the list of Dotmailer Address Books only when custom field is loaded
      if (empty($form->_groupTree)) {
        return;
      }

      // Add dotmailer fields to form
      _dotmailer_civicrm_buildForm_add_dotmailer_fields_to_form($form);
    }
  }
}

/*
 * Function to add Dotmailed fields to form
 */
function _dotmailer_civicrm_buildForm_add_dotmailer_fields_to_form(&$form) {
  $dmAddressBooks = $dmCampaigns = array();
  $params = array(
    'version' => 3,
    'sequential' => 1,
  );

  // Get list of Dotmailer Address Books 
  $dmAddressBooks = civicrm_api('Dotmailer', 'getaddressbooks', $params);
  // Add form elements
  if(!$dmAddressBooks['is_error']){
    $form->add('select', 'dotmailer_address_book', ts('Dotmailer Address Book'), array('' => '- select -') + $dmAddressBooks['values'], FALSE );
  }

  // Get list of Campaigns
  $dmCampaigns = civicrm_api('Dotmailer', 'getcampaigns', $params);
  // Add form elements
  if(!$dmCampaigns['is_error']){
    $form->add('select', 'dotmailer_campaign', ts('Dotmailer Campaign'), array('' => '- select -') + $dmCampaigns['values'], FALSE );
  }

  $form->assign('displayDotmailerDetails', 1);
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
  if ($op == 'create' && $objectName == 'Contribution' && !empty($objectRef->campaign_id) && $objectRef->campaign_id != 'null') {
    CRM_Dotmailer_Utils::createActivityForContribution($objectRef);
  }
}

/**
 * Implementation of hook_civicrm_postProcess
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postProcess
 */
function dotmailer_civicrm_postProcess( $formName, &$form ) {
  // HACK: Check if activity exists for contribition during Online contribution in postProcess
  // as hook_civicrm_post does not call Activity -> Create
  // also hook_civicrm_post (Contribition -> Create) cant be used 
  // as activity will not be created at that point
  /*if ($formName = 'CRM_Contribute_Form_Contribution_Confirm' AND !empty($form->_contributionID)) {
    $activityDetails = CRM_Dotmailer_Utils::getActivityDetailsForContribution($form->_contributionID);
    if (!empty($activityDetails->campaign_id)) {
      CRM_Dotmailer_Utils::processDotmailerSubscription($activityDetails->id, $activityDetails->campaign_id, $form->_contactID);
    }
  }*/

  if ($formName == 'CRM_Admin_Form_Options') {
    $gName = $form->getVar('_gName');
    if ($gName != 'activity_type') {
      return;
    }

    $opValueId      = $form->getVar('_id');
    $activityTypeId = $form->_defaultValues['value'];
    
    if ( empty($activityTypeId) && !empty($opValueId)) {
      $activityTypeId = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue', $opValueId, 'value', 'id');
    }
    
    if (empty($activityTypeId)) {
      return;
    }

    $addressBookId = 'NULL';
    $campaignId = 'NULL';

    if (!empty($form->_submitValues['dotmailer_address_book'])) {
      $addressBookId = $form->_submitValues['dotmailer_address_book'];
    }

    if (!empty($form->_submitValues['dotmailer_campaign'])) {
      $campaignId = $form->_submitValues['dotmailer_campaign'];
    }

    $sql = "REPLACE INTO veda_civicrm_activity_type_dotmailer_subscription_settings
           SET activity_type_id = %1,
           dotmailer_address_book_id = %2,
           dotmailer_campaign_id = %3";
    $params = array(
      '1' => array($activityTypeId, 'Integer'),
      '2' => array($addressBookId, 'String'),
      '3' => array($campaignId, 'String'),
    );
    $dao = CRM_Core_DAO::executeQuery($sql, $params);
  }  
}
