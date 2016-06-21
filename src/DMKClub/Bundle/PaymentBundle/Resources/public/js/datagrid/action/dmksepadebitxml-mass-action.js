define([
    'underscore',
    'oroui/js/messenger',
    'orotranslation/js/translator',
    'oro/datagrid/action/mass-action',
    'oroui/js/mediator'
], function(_, messenger, __, MassAction, mediator) {
    'use strict';

    var CreateSepaDebitAction;

    /**
     * Create a SEPA direct debit xml file
     * der Wert in (at)export bezieht sich auf den Dateinamen in der requirejs.yml
     *
     * @export  dmkclubpayment/js/datagrid/action/dmkcreatesepadebitxml-mass-action
     * @class   oro.datagrid.action.CreateSepaDebitAction
     * @extends oro.datagrid.action.MassAction
     */
    CreateSepaDebitAction = MassAction.extend({
    	_showAjaxSuccessMessage: function(data) {
          var type = data.url ? 'success' : 'error';
          var message = data.message || __(this.messages.error);
          if (data.url) {
          	var filename = data.url.split('/').reverse()[0];
          	filename = filename + ' (' + data.bytes_hr + ')'; 
          	message = message +
              ' <a class="no-hash" target="_blank" href="'+ data.url + '">' + filename + '</a>';
          }
          if(message)
        	  mediator.execute('showMessage', type, message);
      }

    });

    return CreateSepaDebitAction;
});
