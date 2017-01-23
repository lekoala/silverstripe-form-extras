<ul class="ss-uploadfield-files files">
    <% if $CustomisedItems %>
    <% loop $CustomisedItems %>
    <li class="ss-uploadfield-item template-download" data-fileid="$ID">
        <div class="ss-uploadfield-item-preview preview"><span>
                <a href="$URL" target="_blank">
                    <img alt="$hasRelation" src="$UploadFieldThumbnailURL" />
                </a>
            </span></div>
        <div class="ss-uploadfield-item-info">
            <input type='hidden' value='$ID' name='{$Top.Name}[Files][]' />
            <label class="ss-uploadfield-item-name">
                <span class="name">$Name.XML</span>
                <span class="size">$Size</span>
                <div class="clear"><!-- --></div>
            </label>
            <div class="ss-uploadfield-item-actions">
                <% if Top.isActive %>
                $UploadFieldFileButtons
                <% end_if %>
            </div>
        </div>
        <div class="ss-uploadfield-item-editform loading includeParent">
            <iframe frameborder="0" src="$UploadFieldEditLink"></iframe>
        </div>
    </li>
    <% end_loop %>
    <% end_if %>
</ul>
<% if canUpload || canAttachExisting %>
<div class="ss-uploadfield-item ss-uploadfield-addfile<% if $CustomisedItems %> borderTop<% end_if %>">
    <% if canUpload %>
    <div class="ss-uploadfield-item-preview ss-uploadfield-dropzone">
        <% if $multiple %>
        <% _t('UploadField.DROPFILES', 'drop files') %>
        <% else %>
        <% _t('UploadField.DROPFILE', 'drop a file') %>
        <% end_if %>
    </div>
    <% end_if %>
    <div class="ss-uploadfield-item-info">
        <label class="ss-uploadfield-item-name">
            <% if $multiple %>
            <b><% _t('UploadField.ATTACHFILES', 'Attach files') %></b>
            <% else %>
            <b><% _t('UploadField.ATTACHFILE', 'Attach a file') %></b>
            <% end_if %>
            <% if canPreviewFolder %>
            <small>(<%t UploadField.UPLOADSINTO 'saves into /{path}' path=$FolderName %>)</small>
            <% end_if %>
        </label>
        <% if canUpload %>
        <label class="ss-uploadfield-fromcomputer btn" title="<% _t('UploadField.FROMCOMPUTERINFO', 'Upload from your computer') %>">
            <i class="$IconUpload"></i> <% _t('UploadField.FROMCOMPUTER', 'From your computer') %>
            <input $AttributesHTML data-config="$configString"<% if $multiple %> multiple="multiple"<% end_if %>  />
        </label>
        <% end_if %>
        <% if canChooseFromGallery %>
        <label class="ss-uploadfield-fromgallery btn" title="<% _t('AvatarGalleryField.FROMGALLERY', 'From the gallery') %>" data-mfp-src="$GalleryUrl">
            <i class="$IconPicture"></i> <% _t('AvatarGalleryField.FROMGALLERY', 'From the gallery') %>
        </label>
        <% end_if %>

        <% if canAttachExisting %>
        <button class="ss-uploadfield-fromfiles" title="<% _t('UploadField.FROMCOMPUTERINFO', 'Select from files') %>"><% _t('UploadField.FROMFILES', 'From files') %></button>
        <% end_if %>
        <% if canUpload %>
        <% if not $autoUpload %>
        <button class="ss-uploadfield-startall"><% _t('UploadField.STARTALL', 'Start all') %></button>
        <% end_if %>
        <% end_if %>
        <div class="clear"><!-- --></div>
    </div>
    <div class="clear"><!-- --></div>
</div>
<% end_if %>