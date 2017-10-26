/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function(config) {
    // Define custom toolbars here
    config.toolbar_full = [
        {name: 'document', items: ['Source', '-', 'Save']},
        {name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']},
        {name: 'editing', items: ['SelectAll']},
        '/',
        {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat']},
        {name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language']},
        {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
        {name: 'insert', items: ['Image', 'EmbedSemantic', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'Iframe']},
        '/',
        {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
        {name: 'colors', items: ['TextColor', 'BGColor']},
        {name: 'tools', items: ['Maximize', 'ShowBlocks']}
    ];
    config.toolbar_advanced = [
        {name: 'clipboard', items: ['Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Maximize', 'ShowBlocks']},
        {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Format']},
        {name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight']},
        {name: 'insert', items: ['Link', 'Image', 'Embed', 'Table', 'HorizontalRule', 'Smiley', 'Iframe']}
    ];
    config.toolbar_advanced2 = [
        {name: 'clipboard', items: ['Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Maximize', 'ShowBlocks', 'SelectAll']},
        {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat']},
        {name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language']},
        '/',
        {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
        {name: 'insert', items: ['Image', 'EmbedSemantic', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'Iframe']},
        {name: 'styles', items: ['Format', 'Font', 'FontSize']},
        {name: 'colors', items: ['TextColor', 'BGColor']},
        {name: 'tools', items: ['Maximize', 'ShowBlocks']}
    ];
    config.toolbar_basic =
            [
                ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink']
            ];

    // Enable upload
//    config.extraPlugins = 'uploadimage,image2';

    // File manager - only upload, no browse
//    config.filebrowserBrowseUrl = '/__upload/ckeditor';
    config.filebrowserUploadUrl = '/__upload/ckeditor';
//    config.filebrowserImageBrowseUrl = '/__upload/ckeditor?type=Images';
    config.filebrowserImageUploadUrl = '/__upload/ckeditor?type=Images';

    // CSRF token
    config.filebrowserParams = function() {
        var params = {};

        var securityID = $('input[name="SecurityID"]').val();

        if (securityID) {
            params['SecurityID'] = securityID;
        }

        return params;
    };

    config.addQueryString = function(url, params) {
        var queryString = [];

        if (!params) {
            return url;
        } else {
            for (var i in params)
                queryString.push(i + "=" + encodeURIComponent(params[ i ]));
        }

        return url + ((url.indexOf("?") != -1) ? "&" : "?") + queryString.join("&");
    };

    CKEDITOR.on('dialogDefinition', function(ev) {
        // Take the dialog name and its definition from the event data.
        var dialogName = ev.data.name;
        var dialogDefinition = ev.data.definition;
        var content, upload;

        if (CKEDITOR.tools.indexOf(['link', 'image', 'image2', 'attachment', 'flash'], dialogName) > -1) {
            content = (dialogDefinition.getContents('Upload') || dialogDefinition.getContents('upload'));
            upload = (content == null ? null : content.get('upload'));

            if (upload && upload.filebrowser && upload.filebrowser['params'] === undefined) {
                upload.filebrowser['params'] = config.filebrowserParams();
                upload.action = config.addQueryString(upload.action, upload.filebrowser['params']);
            }
        }
    });

    // Upload integration
    config.uploadUrl = '/__upload/ckeditor';
    config.imageUploadUrl = '/__upload/ckeditor?type=Images';

    // Embed
    config.embed_provider = '/__upload/oembed?embed_url={url}&callback={callback}';
//    config.embed_provider = 'http://noembed.com/embed?url={url}&callback={callback}';
};
