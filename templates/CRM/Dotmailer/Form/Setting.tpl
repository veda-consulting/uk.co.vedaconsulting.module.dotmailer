<div class="crm-block crm-form-block crm-dotmailer-setting-form-block">
  <div class="crm-accordion-wrapper crm-accordion_dotmailer_setting--accordion crm-accordion-open">
    <div class="crm-accordion-header">
      {ts}Dotmailer - API User settings{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">

      <div id="help">
      	{ts}To start using the API you'll need to create an API managed user in Dotmailer(<a title='Open in new window' target='_blank' href='http://www.dotmailer.com/api'>more info</a>) and save the API user details below.{/ts}
      	<br />
      	{ts}{/ts}
      </div>	

      <table class="form-layout-compressed">
    	  <tr class="crm-dotmailer-setting-api_email_address-block">
          <td class="label">{$form.api_email_address.label}</td>
          <td>{$form.api_email_address.html}<br/>
      	    <!--<span class="description">{ts}Email address of API User{/ts}</span>-->
          </td>
          </tr>
          <tr class="crm-dotmailer-setting-api_password-block">
          <td class="label">{$form.api_password.label}</td>
          <td>{$form.api_password.html}<br/>
      	    <!--<span class="description">{ts}Password of API User{/ts}</span>-->
          </td>
        </tr> 
      </table>
    </div>
  </div>
  <br />
  <div class="crm-accordion-wrapper crm-accordion_dotmailer_new_contact_setting--accordion crm-accordion-open">
    <div class="crm-accordion-header">
      {ts}Dotmailer - New contact settings{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">

      <div id="help">
      	{ts}These settings will be used when a contact is created/updated in Dotmailer and added to address book from CiviCRM via API.{/ts}
      </div>	

      <table class="form-layout-compressed">
    	  <tr class="crm-dotmailer-setting-api_audience_type-block">
          <td class="label">{$form.api_audience_type.label}</td>
          <td>{$form.api_audience_type.html}</td>
          </tr>
          <tr class="crm-dotmailer-setting-api_opt_in_type-block">
          <td class="label">{$form.api_opt_in_type.label}</td>
          <td>{$form.api_opt_in_type.html}</td>
          </tr>
          <tr class="crm-dotmailer-setting-api_email_type-block">
          <td class="label">{$form.api_email_type.label}</td>
          <td>{$form.api_email_type.html}</td>
          </tr>
          <tr class="crm-dotmailer-setting-api_audience_type-block">
          <td class="label">{$form.api_notes.label}</td>
          <td>{$form.api_notes.html}</td>
          </tr>

        </tr> 
      </table>
    </div>
  </div>
  <br />
  <div class="crm-accordion-wrapper crm-accordion_dotmailer_activity_setting--accordion crm-accordion-open">
    <div class="crm-accordion-header">
      {ts}CiviCRM - Activity settings{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">

      <div id="help">
        {ts}Process dotmailer subscription only for the below activity types, if linked to a campaign.{/ts}
      </div>  

      <table class="form-layout-compressed">
        <tr class="crm-dotmailer-setting-activity_types-block">
          <td class="label">{$form.activity_types.label}</td>
          <td>{$form.activity_types.html}</td>
        </tr> 
      </table>
    </div>
  </div>
  {if $dmCiviCRMFieldMapping}
  <br />
  <div class="crm-accordion-wrapper crm-accordion_dotmailer_field_mapping--accordion crm-accordion-closed collapsed">
    <div class="crm-accordion-header">
      {ts}CiviCRM & Dotmailer - Field Mappings{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body closed">

      <div id="help">
        {ts}CiviCRM & Dotmailer field mapping for sending additional information to Dotmailer custom fields.{/ts}
      </div>

      <table class="selector row-highlight">
        <thead class="sticky">
        <tr>
         <th scope="col">{ts}CiviCRM{/ts}</th>
         <th scope="col">{ts}Dotmailer{/ts}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$dmCiviCRMFieldMapping key=entity item=row}
        {foreach from=$row key=civifield item=dmfield}
        <tr>
          <td>{$entity}: {$civifield}</td>
          <td>{$dmfield}</td>
        </tr>
        {/foreach}
        {/foreach}
        </tbody>
      </table>

      <b>NOTE:</b> You can add more field mapping in $GLOBALS["DotMailerCiviCRMDataFieldsMapping"] array in the extension's main php file (uk.co.vedaconsulting.module.dotmailer/dotmailer.php)
    </div>
  </div>
  {/if}

    <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl"}
    </div>
  </div>
</div>