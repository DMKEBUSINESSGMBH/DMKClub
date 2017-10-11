define([
    'underscore',
    'oroui/js/messenger',
    'orotranslation/js/translator',
    'oro/datagrid/action/mass-action'
], function(_, messenger, __, MassAction) {
    'use strict';

    var SendMemberFee;

    /**
     * Send member fee to member by email
     * der Wert in (at)export bezieht sich auf den Dateinamen in der requirejs.yml
     *
     * @export  dmkclubmember/js/datagrid/action/send-memberfee-mass-action
     * @class   oro.datagrid.action.SendMemberFeeAction
     * @extends oro.datagrid.action.MassAction
     */
    SendMemberFee = MassAction.extend({

    });

    return SendMemberFee;
});
