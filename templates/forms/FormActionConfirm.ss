<% if $UseButtonTag %>
	<button $AttributesHTML onclick="return confirm('$ConfirmText')">
		<% if $ButtonContent %>$ButtonContent<% else %>$Title<% end_if %>
	</button>
<% else %>
	<input $AttributesHTML onclick="return confirm('$ConfirmText')"/>
<% end_if %>
