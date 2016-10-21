<div id="$Name" class="field<% if $extraClass %> $extraClass<% end_if %>">
    <% if $Title %><label class="left" for="$ID">$Title</label><% end_if %>
    <div class="middleColumn">
        <select name="$Name[day]" style="width:auto">
            <option value=""></option>
            <% loop Days %>
            <option value="$Value.XML"<% if $Selected %> selected="selected"<% end_if %><% if $Disabled %> disabled="disabled"<% end_if %>>$Title.XML</option>
            <% end_loop %>
        </select>
        <select  name="$Name[month]" style="width:auto">
            <option value=""></option>
            <% loop Months %>
            <option value="$Value.XML"<% if $Selected %> selected="selected"<% end_if %><% if $Disabled %> disabled="disabled"<% end_if %>>$Title.XML</option>
            <% end_loop %>
        </select>
        <select name="$Name[year]" style="width:auto">
            <option value=""></option>
            <% loop Years %>
            <option value="$Value.XML"<% if $Selected %> selected="selected"<% end_if %><% if $Disabled %> disabled="disabled"<% end_if %>>$Title.XML</option>
            <% end_loop %>
        </select>
    </div>
    <% if $RightTitle %><label class="right" for="$ID">$RightTitle</label><% end_if %>
    <% if $Message %><span class="message $MessageType">$Message</span><% end_if %>
    <% if $Description %><span class="description">$Description</span><% end_if %>
</div>
