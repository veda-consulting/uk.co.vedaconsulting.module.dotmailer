<?php

class CRM_Dotmailer_Utils {

  static function dotmailer() {
    $apiEmail   = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP, 'api_email_address');
    $apiPassword   = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP, 'api_password');
    $dmObj = new DotMailer($apiEmail, $apiPassword);
    return $dmObj;
  }

  /*
   * Function for processing dotmailer subscription based on CiviCRM Activity
   */
  static function processDotmailerSubscription($activityId) {

    // Check if API user details are set
    $apiEmail   = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP, 'api_email_address');
    $apiPassword   = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP, 'api_password');
    if (empty($apiEmail) || empty($apiPassword)) {
      return;
    }

	  if (empty($activityId)) {
	    return;
	  }

    // Get activity details
    $activityDetails = civicrm_api3('Activity', 'getsingle', array(
      'id' => $activityId,
    ));

    // Return, if activity is not linked with any campaign
    if (empty($activityDetails['activity_type_id'])) {
       return;
    }

    if (!isset($activityDetails['campaign_id'])) {
      $activityDetails['campaign_id'] = 'NULL';
    }

    // Check if we need to process this activity type
    /*$activityTypesToProcess = array();
    $activityTypes = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP,
      'activity_types', NULL, FALSE
    );
    $activityTypesToProcess = @unserialize($activityTypes);
    if (!in_array($activityDetails['activity_type_id'], $activityTypesToProcess)) {
      return;
    }*/

    // Get Dotmailer subscription settings for the ACTIVITY TYPE & Campaign
    $dmSubscriptionSettings = CRM_Dotmailer_Utils::getDotmailerMappingDetailSingle($activityDetails['activity_type_id'], $activityDetails['campaign_id']);
    //if (empty($dmSubscriptionSettings)) {
      // Get Dotmailer subscription settings for the campaign
      //$dmSubscriptionSettings = CRM_Dotmailer_Utils::getDotmailerDetailsForCampaign($activityDetails['campaign_id']);
    //}
    
    if (empty($dmSubscriptionSettings->dotmailer_address_book_id) && empty($dmSubscriptionSettings->dotmailer_campaign_id)) {
	    return;
	  }
	  //CRM_Core_Error::debug_var('dmSubscriptionSettings', $dmSubscriptionSettings);

	  // Try to get contactId from activity, if not passed
	  /*if (empty($contactId)) {
	    // Get contact id for activity
	  	$contactId = CRM_Dotmailer_Utils::getContactIdForActivity($activityId);
	  }*/

    $contactId = CRM_Dotmailer_Utils::getContactIdForActivity($activityId);

    //CRM_Core_Error::debug_var('contactId', $contactId);

	  // Return, if contact cant be found
	  if (empty($contactId)) {
	  	return;
	  }	

	  // Get contact details
  	$contactDetails = civicrm_api3('Contact', 'getsingle', array(
      'contact_id' => $contactId,
    ));

    //CRM_Core_Error::debug_var('Contact Details', $result);

    // Dont subscribe to dotmailer, if email is empty
    if (empty($contactDetails['email'])) {
    	return;
    }

    $params = array(
      'version' => 3,
      'sequential' => 1,
    );

    $dotmailerAddressBookName = $dotmailerCampaignName = '';

    $dotmailer = CRM_Dotmailer_Utils::dotmailer();
  	// Add to address book, if set
    if (!empty($dmSubscriptionSettings->dotmailer_address_book_id)) {

      $audienceType = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP, 'api_audience_type');
      if (empty($audienceType)) {
        $audienceType = 'B2B';
      }
      
      $optInType = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP, 'api_opt_in_type');
      if (empty($optInType)) {
        $optInType = 'Single';
      }

      $emailType = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP, 'api_email_type');
      if (empty($emailType)) {
        $emailType = 'Html';
      }

      $notes = CRM_Core_BAO_Setting::getItem(CRM_Dotmailer_Form_Setting::DOTMAILER_SETTING_GROUP, 'api_notes');
      if (empty($notes)) {
        $notes = 'Added from CiviCRM via API';
      }

			$contact = array(
	      'Email' => $contactDetails['email'],
	      'AudienceType' => $audienceType,
	      'OptInType' => $optInType,
	      'EmailType' => $emailType,
	      'Notes' => $notes,
    	);			      

    	$fields = array(
	      'FIRSTNAME' => $contactDetails['first_name'],
	      'LASTNAME' => $contactDetails['last_name'],
	      'FULLNAME' => $contactDetails['display_name'],
	      'GENDER' => $contactDetails['gender'],
	      'POSTCODE' => $contactDetails['postal_code'],
    	);

      // Populate data fields for Dotmailer with CiviCRM field values
      // CRM_Dotmailer_Utils::populateDotmailerDataFields($fields, $activityId);

      try {
				// Add the contact to address book using API
			  $apiResult = $dotmailer->AddContactToAddressBook($contact, $fields, $dmSubscriptionSettings->dotmailer_address_book_id);

			  //CRM_Core_Error::debug_var('apiResultAddressBook', $apiResult);

			  if (!empty($apiResult->ID)) {
				  // Get the address book name to save against activity
				  $dmAddressBooks = civicrm_api('Dotmailer', 'getaddressbooks', $params);
				  $dotmailerAddressBookName = $dmSubscriptionSettings->dotmailer_address_book_id.' - '.$dmAddressBooks['values'][$dmSubscriptionSettings->dotmailer_address_book_id];
				}
			} catch (Exception $e) {
		    CRM_Core_Error::debug_log_message( 'Dotmailer API - Not able to add contact to address book'.print_r($e, true));
		  }
    }

    // Send campaign to contact, if set
    if (!empty($dmSubscriptionSettings->dotmailer_campaign_id)) {

    	// Get the contact in Dotmailer
    	$foundContact = $dotmailer->GetContactByEmail($contactDetails['email']);
      
      try {
      	// Send the campaign for contact using API
    		$apiResult = $dotmailer->SendCampaignToContact($dmSubscriptionSettings->dotmailer_campaign_id, $foundContact->ID, date('Y-m-d\TH:i:s'));
    		
    		//CRM_Core_Error::debug_var('apiResultCampaign', $apiResult);

    		if ($apiResult == 1) {
    			// Get campaign name to save against activity
	    		$dmCampaigns = civicrm_api('Dotmailer', 'getcampaigns', $params);
	    		$dotmailerCampaignName = $dmSubscriptionSettings->dotmailer_campaign_id.' - '.$dmCampaigns['values'][$dmSubscriptionSettings->dotmailer_campaign_id];
    		}

    	} catch (Exception $e) {
		    CRM_Core_Error::debug_log_message( 'Dotmailer API - Not able to send campaign to contact'.print_r($e, true));
		  } 
    }

	  // Update activity custom fields
	  $query = "REPLACE INTO civicrm_value_dotmailer_subscription SET entity_id = %1";
    $params[1] = array($activityId, 'String');

    if (!empty($dotmailerAddressBookName)) {
      $query .= ", dotmailer_address_book = %2";
      $params[2] = array($dotmailerAddressBookName, 'String');
    }

    if (!empty($dotmailerCampaignName)) {
      $query .= ", dotmailer_campaign = %3";
      $params[3] = array($dotmailerCampaignName, 'String');
    }
    CRM_Core_DAO::executeQuery($query, $params);
  }


  static function populateDotmailerDataFields(&$fields, $activityId) {
    if (!empty($GLOBALS["DotMailerCiviCRMDataFieldsMapping"])) {
      foreach($GLOBALS["DotMailerCiviCRMDataFieldsMapping"] as $entity => $mapping) {
        switch ($entity) {
          case 'contact':
            $contactId = CRM_Dotmailer_Utils::getContactIdForActivity($activityId);
            $entityId = $params['id'] = $contactId;
            break;

          case 'activity':
            $entityId = $params['id'] = $activityId;
            break;  
          
          default:
            # code...
            break;
        }

        foreach($mapping as $CiviField => $DotmailerField) {
          $params['return.'.$CiviField] = 1;
        }

        $entityDetails = civicrm_api3($entity, 'get', $params);
        $entityValues = $entityDetails['values'][$entityId];

        foreach($mapping as $CiviField => $DotmailerField) {
          $fields[$DotmailerField] = $entityValues[$CiviField];
        }
      }
    }
  }

  /*
   * Function to get dotmailer mapping for activity type and campaign
   */
  static function getDotmailerMappingDetails($id = NULL) {
    $whereClauses = $params = $result = array();
    $whereClauses[] = ' (1) ';
    $whereClause = '';

    if (!empty($id)) {
      $whereClauses[] = "id = %1";
      $params[1] = array($id , 'String');
    }

    if (!empty($whereClauses)) {
      $whereClause = @implode(' AND ', $whereClauses);
    }

    $activityTypes = CRM_Dotmailer_Utils::getActivityTypes();
    $allActiveCampaigns = CRM_Campaign_BAO_Campaign::getCampaigns(NULL, NULL, TRUE, FALSE);
    $dmAddressBooks = civicrm_api('Dotmailer', 'getaddressbooks', array('version' => 3));
    $dmCampaigns = civicrm_api('Dotmailer', 'getcampaigns', array('version' => 3));

    $query  = "SELECT * FROM ".DOTMAILER_SETTINGS_TABLE_NAME." WHERE {$whereClause}";
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while($dao->fetch()) {
      $result[$dao->id] = $dao->toArray();
      $result[$dao->id]['activity_type_label'] = $activityTypes[$dao->activity_type_id];
      if (!empty($dao->campaign_id) && $dao->campaign_id != 'NULL') {
        $result[$dao->id]['campaign_label'] = $allActiveCampaigns[$dao->campaign_id];
      }
      $result[$dao->id]['dotmailer_address_book_label'] = $dmAddressBooks['values'][$dao->dotmailer_address_book_id];
      if (!empty($dao->dotmailer_campaign_id) && $dao->dotmailer_campaign_id != 'NULL') {
        $result[$dao->id]['dotmailer_campaign_label'] = $dmCampaigns['values'][$dao->dotmailer_campaign_id];
      }
    }

    return $result;
  }

  /*
   * Function to get dotmailer mapping for activity type and campaign
   */
  static function getDotmailerMappingDetailSingle($activityTypeId, $campaignId) {

    if (empty($activityTypeId) || empty($campaignId)) {
      return;
    }

    $query  = "SELECT * FROM ".DOTMAILER_SETTINGS_TABLE_NAME." WHERE activity_type_id = %1 AND campaign_id = %2";
    $params[1] = array($activityTypeId , 'String');
    $params[2] = array($campaignId , 'String');
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    if($dao->fetch()) {
      return $dao;
    }

    return NULL;
  }


  /*
   * Function to get dotmailer subscription details for CiviCRM Campaign
   */
  static function getDotmailerDetailsForCampaign($campaignId) {
  	if (empty($campaignId)) {
  		return;
  	}

  	$whereClause = "entity_id = %1";
  	$query  = "
      SELECT  *
      FROM civicrm_value_dotmailer_subscription_settings 
      WHERE $whereClause";
    $params = 
        array(
          '1' => array($campaignId , 'String'),
        );
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    if ($dao->fetch()) {
      return $dao;
    } else {
    	return NULL;
    }
  }

  /*
   * Function to get dotmailer subscription details for CiviCRM activity type
   */
  static function getDotmailerDetailsForActivityType($activityTypeId) {
    if (empty($activityTypeId)) {
      return;
    }

    $whereClause = "activity_type_id = %1";
    $query  = "
      SELECT  *
      FROM veda_civicrm_activity_type_dotmailer_subscription_settings 
      WHERE $whereClause";
    $params = 
        array(
          '1' => array($activityTypeId , 'String'),
        );
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    if ($dao->fetch()) {
      return $dao;
    } else {
      return NULL;
    }
  }

  /*
   * Function to get 'With Contact' for activity
   */
  static function getContactIdForActivity($activityId) {
  	if (empty($activityId)) {
  		return;
  	}

  	$result = civicrm_api3('ActivityContact', 'get', array(
      'activity_id' => $activityId,
      'record_type_id' => 3,
    ));

  	if (empty($result['values'])) {
  		return;
  	}

    foreach($result['values'] as $key => $value) {
    	return $value['contact_id'];
    }
  }

  /*
   * Function to get related activity for Contribution
   * as its not an independant activity record
   */
  static function getActivityDetailsForContribution($contributionId) {
  	if (empty($contributionId)) {
  		return;
  	}

  	$query = "
      SELECT * 
      FROM civicrm_activity 
      WHERE source_record_id = %1 AND activity_type_id = 6";
    $params = array(
          '1' => array($contributionId , 'String'),
        );
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    if ($dao->fetch()) {
    	return $dao;
  	}
  }

  /*
   * Function to create custom activity for contribution creation
   */
  static function createActivityForContribution($contributionObj) {
    if (empty($contributionObj)) {
      return;
    }

    // Get activity type id
    $activityTypeDetails = self::getActivityTypeForContributionCreation();

    // Create activity
    $params = array(
      'activity_type_id' => $activityTypeDetails['value'],
      'subject' => $activityTypeDetails['description'],
      'activity_date_time' => date("Y-m-d H:i:s"),
      'status_id' => '2',
      'is_test' => '0',
      'is_auto' => '0',
      'is_current_revision' => '1',
      'is_deleted' => '0',
      'source_contact_id' => $contributionObj->contact_id,
      'target_contact_id' => $contributionObj->contact_id,
      'campaign_id' => $contributionObj->campaign_id,
    );
    $result = civicrm_api3('Activity', 'create', $params);
  }

  /*
   * Function to get activity type for contribution created activity
   */
  static function getActivityTypeForContributionCreation() {
    // Get activity type id
    $ogResult = civicrm_api3('OptionGroup', 'getsingle', array(
      'name' => "activity_type",
    ));
    $ovResult = civicrm_api3('OptionValue', 'getsingle', array(
      'option_group_id' => $ogResult['id'],
      'name' => DOTMAILER_CONTRIBUTION_ACTIVITY_TYPE_NAME,
    ));
    
    return array('value' => $ovResult['value'], 'description' => $ovResult['description']);
  }


  /*
   * Function to get activity types list
   * TODO: Use core function instead of this function
   */
  static function getActivityTypes() {
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

  /*
   * Function to process Dotmailer push for unprocessed activities
   */
  static function processSync($params) {
    $limit = CRM_Utils_Array::value( 'limit', $params, 50);

    $dmMappings = CRM_Dotmailer_Utils::getDotmailerMappingDetails();
    if (empty($dmMappings)) {
      return;
    }

    $activityTypes = $campaigns = array();
    foreach($dmMappings as $key => $value) {
      $activityTypes[] = $value['activity_type_id'];
      $campaigns[] = $value['campaign_id'];
    }

    $activityTypeStr = implode(', ', $activityTypes);
    $campaignsStr = implode(', ', $campaigns);

    $query = "SELECT id from civicrm_activity 
        WHERE id NOT IN (SELECT entity_id FROM civicrm_value_dotmailer_subscription)
        AND activity_type_id IN ({$activityTypeStr}) AND campaign_id IN ({$campaignsStr})
        LIMIT {$limit}";
    $dao = CRM_Core_DAO::executeQuery($query);
    while ($dao->fetch()) {
      self::processDotmailerSubscription($dao->id);
    }
  }

}
