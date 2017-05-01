/** @lends CentEditorView */
define(function(require) {
    'use strict';

    /**
     * cCell content editor for currency data with cent values in database.
     *
     * ### Column configuration samples:
     * ``` yml
     * datagrid:
     *   {grid-uid}:
     *     inline_editing:
     *       enable: true
     *     # <grid configuration> goes here
     *     columns:
     *       # Full configuration
     *       {column-name-2}:
     *         inline_editing:
     *           editor:
     *             view: oroform/js/app/views/editor/cent-editor-view
     *             view_options:
     *               placeholder: '<placeholder>'
     *               css_class_name: '<class-name>'
     *           validation_rules:
     *             NotBlank: ~
     * ```
     *
     * ### Options in yml:
     *
     * Column option name                                  | Description
     * :---------------------------------------------------|:-----------
     * inline_editing.editor.view_options.placeholder      | Optional. Placeholder translation key for an empty element
     * inline_editing.editor.view_options.placeholder_raw  | Optional. Raw placeholder value
     * inline_editing.editor.view_options.css_class_name   | Optional. Additional css class name for editor view DOM el
     * inline_editing.editor.validation_rules | Optional. Validation rules. See [documentation](https://goo.gl/j9dj4Y)
     *
     * ### Constructor parameters
     *
     * @class
     * @param {Object} options - Options container
     * @param {Object} options.model - Current row model
     * @param {string} options.fieldName - Field name to edit in model
     * @param {string} options.placeholder - Placeholder translation key for an empty element
     * @param {string} options.placeholder_raw - Raw placeholder value. It overrides placeholder translation key
     * @param {Object} options.validationRules - Validation rules. See [documentation here](https://goo.gl/j9dj4Y)
     *
     * @augments [TextEditorView](./text-editor-view.md)
     * @exports CentEditorView
     */
    var CentEditorView;
    var NumberEditorView = require('oroform/js/app/views/editor/number-editor-view');
//    var _ = require('underscore');
//    var NumberFormatter = require('orofilter/js/formatter/number-formatter');

    CentEditorView = NumberEditorView.extend(/** @exports CentEditorView.prototype */{
        className: 'dmkcent-editor',

        getServerUpdateData: function() {
            var data = {};
            var value = this.getValue();
            data[this.fieldName] = isNaN(value) ? null : (value*100);
            return data;
        }
    });

    return CentEditorView;
});
