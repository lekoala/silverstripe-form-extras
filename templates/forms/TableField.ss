<input type="hidden" name="$Name" id="$ID" value='$Value' />
<table class="ss-gridfield-table">
	<thead>
		<tr>
			<% loop ColumnsList %>
			<th class="main tablefield-header-$Key">$Header</th>
			<% end_loop %>
			<th class="main"></th>
		</tr>
	</thead>
	<tbody>
		<!-- this is filled in by js -->
	</tbody>
	<tfoot>
		<tr>
			<% loop ColumnsList %>
			<th>
				<% if Values %>
				<select name="$Key" data-required="$Required">
					<% loop Values %>
					<option value="$Name">$Value</option>
					<% end_loop %>
				</select>
				<% else %>
				<input type="$Type" name="$Key" data-required="$Required" />
				<% end_if %>
			</th>
			<% end_loop %>
			<th>
				<button class="tablefield-btn-add">+</button>
			</th>
		</tr>
	</tfoot>
</table>