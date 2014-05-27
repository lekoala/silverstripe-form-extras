<% if canEditFrontend %>
	<button class="ss-uploadfield-item-edit btn" title="<% _t('UploadField.EDITINFO', 'Edit this file') %>">
	<i class="icon-pencil"></i> <% _t('UploadField.EDIT', 'Edit') %>
	<span class="toggle-details">
		<span class="toggle-details-icon"></span>
	</span>
	</button>
<% else %>
<span class="btn inactive-btn"><% _t('UploadField.NOT_EDITABLE', 'Not editable') %></span>
<% end_if %>
<button class="ss-uploadfield-item-remove btn" title="<% _t('UploadField.REMOVEINFO', 'Remove this file from here, but do not delete it from the file store') %>">
	<i class="icon-remove"></i> <% _t('UploadField.REMOVE', 'Remove') %></button>
<% if UploadField.canAttachExisting %>
	<button class="ss-uploadfield-item-choose-another ss-uploadfield-fromfiles btn" title="<% _t('UploadField.CHOOSEANOTHERINFO', 'Replace this file with another one from the file store') %>">
	<% _t('UploadField.CHOOSEANOTHERFILE', 'Choose another file') %></button>
<% end_if %>