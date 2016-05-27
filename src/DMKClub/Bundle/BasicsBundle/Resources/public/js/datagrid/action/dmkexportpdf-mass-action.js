define([
    'underscore',
    'oroui/js/messenger',
    'orotranslation/js/translator',
    'oro/datagrid/action/mass-action'
], function(_, messenger, __, MassAction) {
    'use strict';

    var ExportPdfAction;

    /**
     * Export pdf files to remote filesystem
     * der Wert in (at)export bezieht sich auf den Dateinamen in der requirejs.yml
     *
     * @export  dmkclubbasics/js/datagrid/action/dmkexportpdf-mass-action
     * @class   oro.datagrid.action.ExportPdfAction
     * @extends oro.datagrid.action.MassAction
     */
    ExportPdfAction = MassAction.extend({

    });

    return ExportPdfAction;
});
