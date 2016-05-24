/*global define*/
define([
    './model-action',
    'orotranslation/js/translator',
    'oroui/js/mediator'
], function (ModelAction, __, mediator) {
    'use strict';

    var GetpdfAction;

    /**
     * dmkgetpdf action, triggers REST AJAX request
     *
     * @export  oro/datagrid/action/dmkgetpdf-action
     * @class   oro.datagrid.action.GetpdfAction
     * @extends oro.datagrid.action.ModelAction
     */
    
    GetpdfAction = ModelAction.extend({

        entityName: null,
        
         /**
         * Initialize view
         *
         * @param {Object} options
         * @param {Backbone.Model} options.model Optional parameter
         * @throws {TypeError} If model is undefined
         */
        initialize: function (options) {
            
            var opts = options || {};

            
            if (_.has(opts, 'entityName')) {
                this.entityName = opts.entityName;
            }
            ModelAction.__super__.initialize.apply(this, arguments);
            
        },
        defaultMessages: {
            error: 'PDF creation failed'
        },

        /**
         * Execute delete model
         */
        execute: function() {
            this._confirmationExecutor(_.bind(this.executePdfAction, this));
        	console.info('Test');
        },
        executePdfAction: function() {
            $.ajax({
                url: this.getLink(),
                data: this.getActionParameters(),
                context: this,
                dataType: 'json',
                type: this.requestType,
                error: this._onAjaxError,
                success: this._onAjaxSuccess
            });

        },

        _onAjaxSuccess: function(data) {
//            var defaultMessage = data.url ? this.messages.success : this.messages.error;
            var type = data.url ? 'success' : 'error';
            var message = data.message || __(this.messages.error);
            if (data.url) {
            	message = __('dmkclub.basics.pdf.export_done') +
                ' <a class="no-hash" target="_blank" href="'+ data.url + '">' + __('dmkclub.basics.pdf.download_file') + '</a>';
            }
        	mediator.execute('showMessage', type, message);
        }

    });
    

    return GetpdfAction;
});
