<% if enableShowHide %>
<div class="sp-field-holder">
	<input $AttributesHTML data-rules='$getRulesJson' /> <label><input type="checkbox" class="sp-checkbox" /> <% _t('SexyPasswordField.SHOW','Show') %></label>
</div>
<% else %>
<input $AttributesHTML data-rules='$getRulesJson'  />
<% end_if %>
<% if showPasswordConstraints %>
$Restrictions
<% end_if %>