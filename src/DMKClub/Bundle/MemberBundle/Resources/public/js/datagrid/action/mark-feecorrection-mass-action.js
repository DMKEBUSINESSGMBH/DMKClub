define(function(require) {
    'use strict';

    const MassAction = require('oro/datagrid/action/mass-action');

    /**
     * Mark feecorrection as mark/unmark
     * der Wert in (at)export bezieht sich auf den Dateinamen in der requirejs.yml
     *
     * @export  dmkclubmember/js/datagrid/action/mark-feecorrection-mass-action
     * @class   oro.datagrid.action.FeeCorrectionAction
     * @extends oro.datagrid.action.MassAction
     */
    const FeeCorrection = MassAction.extend({

    });

    return FeeCorrection;
});
