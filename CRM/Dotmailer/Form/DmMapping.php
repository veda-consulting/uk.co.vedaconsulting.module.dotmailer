<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Dotmailer_Form_DmMapping extends CRM_Core_Form {

  public function setDefaultValues() {
    $defaults = array();
    return $defaults;
  }

  function buildQuickForm() {

    $action = CRM_Utils_Array::value('action', $_REQUEST, '');
    if (empty($action)) {
      $action = CRM_Utils_Array::value('action', $_POST, '');
    }   
    
    $id = CRM_Utils_Request::retrieve( 'id', 'Integer', $this );
    if (empty($id)) {
      $id = CRM_Utils_Array::value('id', $_POST, '');
    }    

    if ($action == 'update') {

      CRM_Utils_System::setTitle('Edit Dotmailer Mapping');

      $dmMappingDetails = CRM_Dotmailer_Utils::getDotmailerMappingDetails($id);
      $defaults = $dmMappingDetails[$id];
      $this->setDefaults( $defaults );

    } elseif ($action == 'add') {

      CRM_Utils_System::setTitle('Add Dotmailer Mapping');

    } elseif ($action == 'delete') {

      $this->assign('id', $id );
      $dmMappingDetails = CRM_Dotmailer_Utils::getDotmailerMappingDetails($id);

      $this->assign('activity_type_label', $dmMappingDetails[$id]['activity_type_label']);
      $this->assign('campaign_label', $dmMappingDetails[$id]['campaign_label']);

      CRM_Utils_System::setTitle( 'Delete Dotmailer mapping' );

    } elseif ($action == 'force_delete' & !empty($id)) {

      $sql = "DELETE FROM ".DOTMAILER_SETTINGS_TABLE_NAME." WHERE id = {$id}";
      CRM_Core_DAO::executeQuery($sql);
      
      $session = CRM_Core_Session::singleton( );
      $message = ts('Dotmailer mapping deleted');
      CRM_Core_Session::setStatus($message, 'Dotmailer mapping', 'success');
      CRM_Utils_System::redirect(CRM_Utils_System::url( 'civicrm/dotmailer/settings', 'reset=1'));
      CRM_Utils_System::civiExit();

    }

    // Activity types
    $activityTypes = CRM_Dotmailer_Utils::getActivityTypes();
    $this->add('select', 'activity_type_id', ts('CiviCRM Activity Type'), array('' => '- select -') + $activityTypes, TRUE );

    // Active campaigns
    $allActiveCampaigns = CRM_Campaign_BAO_Campaign::getCampaigns(NULL, NULL, TRUE, FALSE);
    $this->add('select', 'campaign_id', ts('CiviCRM Campaign'), array('' => '- select -') + $allActiveCampaigns, TRUE );

    // Get list of Dotmailer Address Books 
    $dmAddressBooks = civicrm_api('Dotmailer', 'getaddressbooks', array('version' => 3));
    $this->add('select', 'dotmailer_address_book_id', ts('Dotmailer Address Book'), array('' => '- select -') + $dmAddressBooks['values'], TRUE );

    // Get list of Campaigns
    $dmCampaigns = civicrm_api('Dotmailer', 'getcampaigns', array('version' => 3));
    $this->add('select', 'dotmailer_campaign_id', ts('Dotmailer Campaign'), array('' => '- select -') + $dmCampaigns['values'], FALSE );
    
    $this->addElement('hidden', 'action', $action );
    $this->addElement('hidden', 'id', $id );
    
    $this->addFormRule(array( 'CRM_Dotmailer_Form_DmMapping', 'formRule'));

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Save'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  /**
   * global validation rules for the form
   *
   * @param array $fields posted values of the form
   *
   * @return array list of errors to be posted back to the form
   * @static
   * @access public
   */
  static function formRule($values) {
    $errors = array( );
    if ($values['action'] == 'add') {
      $sql = "SELECT * FROM ".DOTMAILER_SETTINGS_TABLE_NAME." WHERE activity_type_id = %1 AND campaign_id = %2";
      $dao = CRM_Core_DAO::executeQuery($sql , array( 
                                              1 => array( $values['activity_type_id'], 'Integer' ),
                                              2 => array( $values['campaign_id'], 'Integer' ),
                                          ));
      if ($dao->fetch()) {
        $errors['activity_type_id'] = ts("Dotmailer mapping already added for activity type and campaign.");
      }
    }                   
    if ($values['action'] == 'update') {
      $sql = "SELECT * FROM ".DOTMAILER_SETTINGS_TABLE_NAME." WHERE activity_type_id = %1 AND campaign_id = %2 AND id != %3";
      $dao = CRM_Core_DAO::executeQuery($sql , array( 
                                                1 => array( $values['activity_type_id'], 'Integer' ),
                                                2 => array( $values['campaign_id'], 'Integer' ),
                                                3 => array( $values['id'], 'Integer' ),
                                            ));
      if ($dao->fetch()) {
        $errors['activity_type_id'] = ts("Dotmailer mapping already added for activity type and campaign");
      }
    }                                     
    return $errors;
  }

  function postProcess() {
    $params = $this->exportValues();

    $dmCampaign = 'NULL';
    if (!empty($params['dotmailer_campaign_id'])) {
      $dmCampaign = $params['dotmailer_campaign_id'];
    }

    $sqlParams[1] = array($params['activity_type_id'], 'String');
    $sqlParams[2] = array($params['campaign_id'], 'String');
    $sqlParams[3] = array($params['dotmailer_address_book_id'], 'String');
    $sqlParams[4] = array($dmCampaign, 'String');

    if ($params['action'] == 'add') {

      $sql = "INSERT INTO ".DOTMAILER_SETTINGS_TABLE_NAME." SET activity_type_id = %1, campaign_id = %2, dotmailer_address_book_id = %3, dotmailer_campaign_id = %4";
      CRM_Core_DAO::executeQuery($sql, $sqlParams);

      $status = ts('Dotmailer mapping added.');
    } elseif ($params['action'] == 'update') {

      $sqlParams[5] = array($params['id'], 'Integer');
      $sql = "UPDATE ".DOTMAILER_SETTINGS_TABLE_NAME." SET activity_type_id = %1, campaign_id = %2, dotmailer_address_book_id = %3, dotmailer_campaign_id = %4 WHERE id = %5";
      CRM_Core_DAO::executeQuery($sql, $sqlParams);

      $status = ts('Dotmailer mapping updated.');
    }



    $message = ts('Dotmailer mapping saved.');
    CRM_Core_Session::setStatus($message, 'Dotmailer mapping', 'success');
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/dotmailer/settings', 'reset=1'));
    CRM_Utils_System::civiExit();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
