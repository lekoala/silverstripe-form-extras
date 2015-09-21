<div id="{$ID}Config" style="display:none;">$buildJsonOpts</div>
<table id="$ID" class="<% if $extraClass %> $extraClass<% end_if %>"></table>
<% if TotalRow %>
<div class="appendGridTotalRow">
	<label>$TotalRow.Label <input type="text" name="$TotalRow.Name" id="{$ID}TotalRow" readonly /></label>
	<div style="clear:both;"></div>
</div>
<% end_if %>