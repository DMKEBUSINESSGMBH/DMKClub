define([
    'underscore',
    'oroui/js/messenger',
    'orotranslation/js/translator',
    'oro/datagrid/action/mass-action'
], function(_, messenger, __, MassAction) {
    'use strict';

    var DownloadPdfAction;

    /**
     * Download one combined pdf file
     * der Wert in (at)export bezieht sich auf den Dateinamen in der requirejs.yml
     *
     * @export  dmkclubbasics/js/datagrid/action/dmkdownloadpdf-mass-action
     * @class   oro.datagrid.action.DownloadPdfAction
     * @extends oro.datagrid.action.MassAction
     */
    DownloadPdfAction = MassAction.extend({

    });

    return DownloadPdfAction;
});
