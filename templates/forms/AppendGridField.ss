<table id="$ID" class="<% if $extraClass %> $extraClass<% end_if %>"></table>
<% if TotalRow %>
<div class="appendGridTotalRow">
	<label class="right">$TotalRow.Label <input type="text" name="$TotalRow.Name" id="{$ID}TotalRow" /></label>
	<div style="clear:both;"></div>
</div>
<% end_if %>