define([
    'underscore',
    'orotranslation/js/translator',
    'oroui/js/mediator',
    'oro/datagrid/action/mass-action'
], function(_, __, mediator, MassAction) {
    'use strict';

    /**
     * Download one combined pdf file
     * der Wert in (at)export bezieht sich auf den Dateinamen in der requirejs.yml
     *
     * @export  dmkclubbasics/js/datagrid/action/dmkdownloadpdf-mass-action
     * @class   oro.datagrid.action.DownloadPdfAction
     * @extends oro.datagrid.action.MassAction
     */
    const DownloadPdfAction = MassAction.extend({
    	_showAjaxSuccessMessage: function(data) {
            var type = data.url ? 'success' : 'error';
            var message = data.message || __(this.messages.error);
            if (data.url) {
            	var filename = data.url.split('/').reverse()[0];
//            	filename = filename + ' (' + data.bytes_hr + ')'; 
            	message = message +
                ' <a class="no-hash" target="_blank" href="'+ data.url + '">' + filename + '</a>';
            }
            if(message)
          	  mediator.execute('showMessage', type, message);
        }

    });

    return DownloadPdfAction;
});
