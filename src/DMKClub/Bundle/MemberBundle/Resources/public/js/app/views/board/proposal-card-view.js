define(function(require) {
    'use strict';

    var ProposalCardView;
    var CardView = require('orodatagrid/js/app/views/board/card-view');

    ProposalCardView = CardView.extend({
        className: 'proposal-card-view card-view',
        template: require('tpl!../../../../templates/board/proposal-card-view.html')
    });

    return ProposalCardView;
});
