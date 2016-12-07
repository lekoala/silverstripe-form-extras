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
        <% if SubColumn %>
        <% if $SubcolumnsHaveValues %>
        <tr class="tablefield-sub-columns">
            <td colspan="$ColSpan">
                <% loop Rows %>
                <% if Value %>
                <strong>$Label:</strong> $Value
                <% end_if %>
                <% end_loop %>
            </td>
        </tr>
        <% else %>
        <!-- skip subcolumn -->
        <% end_if %>
        <% else %>
        <tr>
            <% loop Rows %>
            <td>$Value</td>
            <% end_loop %>
        </tr>
        <% end_if %>
        <% end_loop %>
    </tbody>
</table>