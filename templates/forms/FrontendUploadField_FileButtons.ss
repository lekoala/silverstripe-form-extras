<% if canEditFrontend %>
<button class="ss-uploadfield-item-edit btn" title="<% _t('UploadField.EDITINFO', 'Edit this file') %>">
	<i class="$IconEdit"></i> <% _t('UploadField.EDIT', 'Edit') %>
	<span class="toggle-details">
		<span class="toggle-details-icon"></span>
	</span>
</button>
<% else %>
<%-- hide button --%>
<% end_if %>
<% if $canDelete %> <%-- if the user can delete the file, allow so to avoid tons of useless files on server... --%>
<button data-href="$UploadFieldDeleteLink" class="ss-uploadfield-item-delete ss-ui-button ui-corner-all">
	<i class="$IconRemove"></i> <% _t('UploadField.DELETE', 'Delete from files') %></button>
<% else %>
<button class="ss-uploadfield-item-remove btn">
	<i class="$IconRemove"></i> <% _t('UploadField.REMOVE', 'Remove') %></button>
<% end_if %>
<% if UploadField.canAttachExisting %>
<button class="ss-uploadfield-item-choose-another ss-uploadfield-fromfiles btn">
	<% _t('UploadField.CHOOSEANOTHERFILE', 'Choose another file') %></button>
<% end_if %>