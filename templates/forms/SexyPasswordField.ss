
<% if enableShowHide %>
<div class="sp-field-holder">
	<input $AttributesHTML /> <label><input type="checkbox" class="sp-checkbox" /> <% _t('SexyPasswordField.SHOW','Show') %></label>
</div>
<% else %>
<input $AttributesHTML />
<% end_if %>
<% if showPasswordConstraints %>
$Restrictions
<% end_if %>