define([
    'underscore',
    'oroui/js/messenger',
    'orotranslation/js/translator',
    'oro/datagrid/action/mass-action'
], function(_, messenger, __, MassAction) {
    'use strict';

    var FeeCorrection;

    /**
     * Mark feecorrection as mark/unmark
     * der Wert in (at)export bezieht sich auf den Dateinamen in der requirejs.yml
     *
     * @export  dmkclubmember/js/datagrid/action/mark-feecorrection-mass-action
     * @class   oro.datagrid.action.FeeCorrectionAction
     * @extends oro.datagrid.action.MassAction
     */
    FeeCorrection = MassAction.extend({

    });

    return FeeCorrection;
});
