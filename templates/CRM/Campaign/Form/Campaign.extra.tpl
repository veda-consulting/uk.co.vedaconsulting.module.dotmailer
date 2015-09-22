{if $displayDotmailerDetails eq 1}
	<table id="dotmailer_settings" style="display:none;">
	<tr>
		<td colspan="2">
		<div id="help">
	      	{ts}Subscribe to Dotmailer when a contact is involved in this campaign (Example: donates to this campaign).{/ts}
	    </div>
		</td>
	</tr>
	<tr class="custom_field-row dotmailer_address_book" id="dotmailer_address_book_tr">
	    <td class="label">{$form.dotmailer_address_book.label}</td>
	    <td class="html-adjust">{$form.dotmailer_address_book.html}<br />
	    	<span class="description">
	    		{ts}Select the Dotmailer address book to add the contact to.{/ts}
	    	</span>	
	    </td>
	</tr>
	<tr class="custom_field-row dotmailer_campaign" id="dotmailer_campaign_tr">
	    <td class="label">{$form.dotmailer_campaign.label}</td>
	    <td class="html-adjust">{$form.dotmailer_campaign.html}<br />
	    	<span class="description">
	    		{ts}You can optionally add the contact to a Dotmailer campaign.{/ts}
				<br />		    		
	    		{ts}<strong>IMPORTANT:</strong> Please make sure you select a standard campaign here and not triggered campaign, as contacts cannot be added to triggered campaigns.{/ts}
	    	</span>
	    </td>
	</tr>
	</table>

	{literal}
	<script>
	cj( document ).ready(function() {
		var dotmailer_settings = cj('#dotmailer_settings').html();
	    dotmailer_settings = dotmailer_settings.replace("<tbody>", "");
	    dotmailer_settings = dotmailer_settings.replace("</tbody>", "");
	    cj("input[data-crm-custom='Dotmailer_Subscription_Settings:Dotmailer_Address_Book']").parent().parent().after(dotmailer_settings);

	    cj("input[data-crm-custom='Dotmailer_Subscription_Settings:Dotmailer_Address_Book']").parent().parent().hide();
	    cj("input[data-crm-custom='Dotmailer_Subscription_Settings:Dotmailer_Campaign']").parent().parent().hide();
	    
	    {/literal}{if $action eq 2}{literal}
	    	var defaultAddressBookId = cj("input[data-crm-custom='Dotmailer_Subscription_Settings:Dotmailer_Address_Book']").val();
	    	var defaultCampaignId = cj("input[data-crm-custom='Dotmailer_Subscription_Settings:Dotmailer_Campaign']").val();
			cj("#dotmailer_address_book").val(defaultAddressBookId);
			cj("#dotmailer_campaign").val(defaultCampaignId);
	    {/literal}{/if}{literal}

	    cj("#dotmailer_address_book").change(function() {
	      var addressBookId = cj(this).val();
	      cj("input[data-crm-custom='Dotmailer_Subscription_Settings:Dotmailer_Address_Book']").val(addressBookId);
	    });

	    cj("#dotmailer_campaign").change(function() {
	      var campaignId = cj(this).val();
	      cj("input[data-crm-custom='Dotmailer_Subscription_Settings:Dotmailer_Campaign']").val(campaignId);
	    });

	});
	</script>
	{/literal}
{/if}	