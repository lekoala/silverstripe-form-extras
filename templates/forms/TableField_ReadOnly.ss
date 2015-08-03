<table class="ss-gridfield-table">
	<thead>
		<tr>
			<% loop ColumnsList %>
			<th class="main tablefield-header-$Key">$Header</th>
			<% end_loop %>
		</tr>
	</thead>
	<tbody>
		<% loop DataList %>
		<tr>
			<% loop Rows %>
			<td>$Value</td>
			<% end_loop %>
		</tr>
		<% end_loop %>
	</tbody>
</table>