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
    config.toolbar_advanced =
            [
                {name: 'clipboard', items: ['Paste', 'PasteText', 'PasteFromWord', 'Undo', '-', 'Maximize', 'ShowBlocks']},
                {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike']},
                {name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight']},
                {name: 'links', items: ['Link', 'Unlink']},
                {name: 'insert', items: ['Image', 'Embed', 'Table', 'HorizontalRule', 'Smiley', 'Iframe']}
            ];

    config.toolbar_basic =
            [
                ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink']
            ];


    // Upload integration
    config.uploadUrl = '/__upload/ckeditor';
    config.imageUploadUrl = '/__upload/ckeditor?type=Images';

    // Embed
    config.embed_provider = '/__upload/oembed?embed_url={url}&callback={callback}';
//    config.embed_provider = 'http://noembed.com/embed?url={url}&callback={callback}';
};
