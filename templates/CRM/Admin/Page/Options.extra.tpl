{if $displayDotmailerDetails eq 1}
	<table id="dotmailer_settings" style="display:none;">
	<tr class="crm-admin-options-form-block-dotmailer">
		<td class="label" colspan="2">
		<div id="help">
	      	{ts}Subscribe to Dotmailer when a contact is involved in this activity type (Example: activity of this type created for the contact).{/ts}
	     </div>
		</td>
	</tr>
	<tr class="rm-admin-options-form-block_address_book" id="dotmailer_address_book_tr">
	    <td class="label">{$form.dotmailer_address_book.label}</td>
	    <td class="html-adjust">{$form.dotmailer_address_book.html}<br />
	    	<span class="description">
	    		{ts}Select the Dotmailer address book to add the contact to.{/ts}
	    	</span>	
	    </td>
	</tr>
	<tr class="rm-admin-options-form-block_dotmailer_campaign" id="dotmailer_campaign_tr">
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
		    cj("#is_active").parent().parent().parent().after(dotmailer_settings);
		
	});
	</script>
	{/literal}
{/if}	