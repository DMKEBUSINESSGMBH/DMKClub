define([
    'underscore',
    'orotranslation/js/translator',
    'oro/datagrid/action/mass-action'
], function(_, __, MassAction) {
    'use strict';

    /**
     * Export pdf files to remote filesystem
     * der Wert in (at)export bezieht sich auf den Dateinamen in der requirejs.yml
     *
     * @export  dmkclubbasics/js/datagrid/action/dmkexportpdf-mass-action
     * @class   oro.datagrid.action.ExportPdfAction
     * @extends oro.datagrid.action.MassAction
     */
    const ExportPdfAction = MassAction.extend({

    });

    return ExportPdfAction;
});
