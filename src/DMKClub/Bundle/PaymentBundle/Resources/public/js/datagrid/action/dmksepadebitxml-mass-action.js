define([
    'underscore',
    'oroui/js/messenger',
    'orotranslation/js/translator',
    'oro/datagrid/action/mass-action'
], function(_, messenger, __, MassAction) {
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

    });

    return CreateSepaDebitAction;
});
