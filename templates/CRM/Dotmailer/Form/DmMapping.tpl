<div class="crm-block crm-form-block crm-dotmailer-mapping-form-block">
  {if $action eq '1' OR $action eq '2'}
  <div id="help">
    {ts}Add mapping between CiviCRM activity type & campaign with Dotmailer address book & campaign.{/ts}
  </div>  

  <table class="form-layout-compressed">
    <tr class="crm-dotmailer-mapping-activity_type_id-block">
      <td class="label" width="40%">{$form.activity_type_id.label}</td>
      <td>{$form.activity_type_id.html}<br/>
        <span class="description">{ts}Contact will be added to Dotmailer if an activity of this type is created for the contact.{/ts}</span>
      </td>
    </tr>
    <tr class="crm-dotmailer-mapping-campaign_id-block">
      <td class="label">{$form.campaign_id.label}</td>
      <td>{$form.campaign_id.html}<br/>
        <span class="description">{ts}Campaign linked with the activity created.{/ts}</span>
      </td>
    </tr> 
    <tr class="crm-dotmailer-mapping-dotmailer_address_book_id-block">
      <td class="label">{$form.dotmailer_address_book_id.label}</td>
      <td>{$form.dotmailer_address_book_id.html}<br/>
        <span class="description">
          {ts}Select the Dotmailer address book to add the contact to.{/ts}
        </span> 
      </td>
    </tr> 
    <tr class="crm-dotmailer-mapping-dotmailer_campaign_id-block">
      <td class="label">{$form.dotmailer_campaign_id.label}</td>
      <td>{$form.dotmailer_campaign_id.html}<br/>
        <span class="description">
          {ts}You can optionally add the contact to a Dotmailer campaign.{/ts}
        <br />            
          {ts}<strong>IMPORTANT:</strong> Please make sure you select a standard campaign here and not triggered campaign, as contacts cannot be added to triggered campaigns.{/ts}
        </span>
      </td>
    </tr> 
  </table>

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl"}
  </div>
  {else if $action eq '8'}
    <h3>Delete Dotmailer mapping for {$activity_type_label} & {$campaign_label}</h3>
    <div class="crm-dotmailer-mapping-form-block-delete messages status">
        <div class="crm-content">
            <div class="icon inform-icon"></div> &nbsp;
            {ts}WARNING: This operation cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
        </div>
    </div>
    {assign var=id value=$id}
    <div class="crm-submit-buttons">
        <a class="button" href="{crmURL p='civicrm/dotmailer/settings/dmmapping' q="action=force_delete&id=$id&reset=1"}"><span><div class="icon delete-icon"></div>{ts}Delete{/ts}</span></a>        
        <a class="button" href="{crmURL p='civicrm/dotmailer/settings' q="reset=1"}"><span>{ts}Cancel{/ts}</span></a>
    </div>    
    </div>
  {/if}
</div>