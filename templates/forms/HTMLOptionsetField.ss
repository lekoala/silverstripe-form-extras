<ul id="$ID" class="$extraClass">
    <% loop $Options %>
    <li class="$Class">
        <input id="$ID" class="radio" name="$Name" type="radio" value="$Value.ATT"<% if $isChecked %> checked<% end_if %><% if $isDisabled %> disabled<% end_if %> />
               <label for="$ID">$Title.RAW</label>
    </li>
    <% end_loop %>
</ul>
