<input type="hidden" name="$Name" value='$Value' />
<table class="ss-gridfield-table">
	<thead>
		<tr>
			<% loop HeadersList %>
			<th class="main tablefield-header-$Name">$Name</th>
			<% end_loop %>
			<th class="main"></th>
		</tr>
	</thead>
	<tbody>
		<!-- this is filled in by js -->
	</tbody>
	<tfoot>
		<tr>
			<% loop HeadersList %>
			<th>
				<% if Values %>
				<select name="$Name">
					<% loop Values %>
					<option value="$Name">$Value</option>
					<% end_loop %>
				</select>
				<% else %>
				<input type="text" name="$Name" />
				<% end_if %>
			</th>
			<% end_loop %>
			<th>
				<button class="tablefield-btn-add">+</button>
			</th>
		</tr>
	</tfoot>
</table>