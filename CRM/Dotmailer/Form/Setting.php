<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Dotmailer_Form_Setting extends CRM_Core_Form {

  const DOTMAILER_SETTING_GROUP = 'Dotmailer Preferences';

  public function setDefaultValues() {
    $defaults = array();

    $defaults['api_email_address'] = CRM_Core_BAO_Setting::getItem(self::DOTMAILER_SETTING_GROUP,
      'api_email_address', NULL, FALSE
    );
    $defaults['api_password'] = CRM_Core_BAO_Setting::getItem(self::DOTMAILER_SETTING_GROUP,
      'api_password', NULL, FALSE
    );
    $defaults['api_audience_type'] = CRM_Core_BAO_Setting::getItem(self::DOTMAILER_SETTING_GROUP,
      'api_audience_type', NULL, FALSE
    );
    $defaults['api_opt_in_type'] = CRM_Core_BAO_Setting::getItem(self::DOTMAILER_SETTING_GROUP,
      'api_opt_in_type', NULL, FALSE
    );
    $defaults['api_email_type'] = CRM_Core_BAO_Setting::getItem(self::DOTMAILER_SETTING_GROUP,
      'api_email_type', NULL, FALSE
    );
    $defaults['api_notes'] = CRM_Core_BAO_Setting::getItem(self::DOTMAILER_SETTING_GROUP,
      'api_notes', NULL, FALSE
    );

    $activityTypes = CRM_Core_BAO_Setting::getItem(self::DOTMAILER_SETTING_GROUP,
      'activity_types', NULL, FALSE
    );
    if (!empty($activityTypes)) {
      $defaults['activity_types'] = unserialize($activityTypes);
    }
    
    return $defaults;
  }

  function buildQuickForm() {

    // add form elements

    // Add the API Email element
    $this->addElement('text', 'api_email_address', ts('API Email Address'), array('size' => 48, TRUE));
    $this->addElement('text', 'api_password', ts('API Password'), array('size' => 48, TRUE));
    $this->add('select', 'api_audience_type', ts('Audience Type'), array('' => '- select -') + $GLOBALS["DotMailerAudienceType"], FALSE );
    $this->add('select', 'api_opt_in_type', ts('Opt In Type'), array('' => '- select -') + $GLOBALS["DotMailerOptInType"], FALSE );
    $this->add('select', 'api_email_type', ts('Email Type'), array('' => '- select -') + $GLOBALS["DotMailerEmailType"], FALSE );
    $this->add('textarea', 'api_notes', ts('Notes'), array("rows" => 4, "cols" => 60));

    $apiActivityTypes = &$this->addElement('advmultiselect', 'activity_types',
      ts('Activity Types') . ' ', $this->getActivityTypes(),
      array(
        'size' => 5,
        'style' => 'width:200px',
        'class' => 'advmultiselect',
      )
    );

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Save'),
        'isDefault' => TRUE,
      ),
    ));

    if (!empty($GLOBALS["DotMailerCiviCRMDataFieldsMapping"])) {
      $this->assign('dmCiviCRMFieldMapping',$GLOBALS["DotMailerCiviCRMDataFieldsMapping"]);
    }    

    // Get dotmailer custom fields
    /*$params = array(
      'version' => 3,
      'sequential' => 1,
    );
    $dmDataFields = civicrm_api('Dotmailer', 'getdatafields', $params);*/

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  function postProcess() {
    $params = $this->exportValues();

    // Save the API Key & Save the Security Key
    //if (CRM_Utils_Array::value('api_email_address', $params) || CRM_Utils_Array::value('api_password', $params)) {
      CRM_Core_BAO_Setting::setItem($params['api_email_address'],
        self::DOTMAILER_SETTING_GROUP,
        'api_email_address'
      );
      
      CRM_Core_BAO_Setting::setItem($params['api_password'],
        self::DOTMAILER_SETTING_GROUP,
        'api_password'
      );
    //}    

    CRM_Core_BAO_Setting::setItem($params['api_audience_type'],
      self::DOTMAILER_SETTING_GROUP,
      'api_audience_type'
    );

    CRM_Core_BAO_Setting::setItem($params['api_opt_in_type'],
      self::DOTMAILER_SETTING_GROUP,
      'api_opt_in_type'
    );

    CRM_Core_BAO_Setting::setItem($params['api_email_type'],
      self::DOTMAILER_SETTING_GROUP,
      'api_email_type'
    );

    CRM_Core_BAO_Setting::setItem($params['api_notes'],
      self::DOTMAILER_SETTING_GROUP,
      'api_notes'
    );

    $activityTypeStr = @serialize($params['activity_types']);
    CRM_Core_BAO_Setting::setItem($activityTypeStr,
      self::DOTMAILER_SETTING_GROUP,
      'activity_types'
    );
       
    $message = ts('Settings saved.');
    CRM_Core_Session::setStatus($message, 'Dotmailer', 'success');
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

  function getActivityTypes() {
    $ogResult = civicrm_api3('OptionGroup', 'getsingle', array(
      'name' => "activity_type",
    ));
    $ovResult = civicrm_api3('OptionValue', 'get', array(
      'option_group_id' => $ogResult['id'],
      'rowCount' => 0,
    ));

    $activityTypes = array();
    foreach($ovResult['values'] as $key => $value) {
      $activityTypes[$value['value']] = $value['label'];
    }

    return $activityTypes;
  }
}
