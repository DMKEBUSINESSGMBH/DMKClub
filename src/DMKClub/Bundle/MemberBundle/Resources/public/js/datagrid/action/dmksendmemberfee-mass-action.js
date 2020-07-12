define(function(require) {
    'use strict';

    const MassAction = require('oro/datagrid/action/mass-action');

    /**
     * Send member fee to member by email
     * der Wert in (at)export bezieht sich auf den Dateinamen in der requirejs.yml
     *
     * @export  dmkclubmember/js/datagrid/action/send-memberfee-mass-action
     * @class   oro.datagrid.action.SendMemberFeeAction
     * @extends oro.datagrid.action.MassAction
     */
    const SendMemberFee = MassAction.extend({

    });

    return SendMemberFee;
});
