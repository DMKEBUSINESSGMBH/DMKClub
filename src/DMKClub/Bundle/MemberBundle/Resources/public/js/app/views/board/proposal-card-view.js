define(function(require) {
    'use strict';

    const CardView = require('orodatagrid/js/app/views/board/card-view');

    const ProposalCardView = CardView.extend({
        className: 'proposal-card-view card-view',
        template: require('tpl-loader!../../../../templates/board/proposal-card-view.html'),

        /**
         * @inheritDoc
         */
        constructor: function ProposalCardView(options) {
            ProposalCardView.__super__.constructor.call(this, options);
        }

    });

    return ProposalCardView;
});
