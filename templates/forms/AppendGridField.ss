<div id="{$ID}Config" style="display:none;">$buildJsonOpts</div>
<table id="$ID" class="<% if $extraClass %> $extraClass<% end_if %>"></table>
<% if TotalRow %>
<% loop TotalRow %>
<div class="appendGridTotalRow">
	<label>$Label <input type="text" name="$.Name" id="{$Up.ID}_{$Name}" readonly /></label>
	<div style="clear:both;"></div>
</div>
<% end_loop %>
<% end_if %>