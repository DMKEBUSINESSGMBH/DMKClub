<?php
namespace DMKClub\Bundle\MemberBundle\DataGrid\Extension\MassAction;

use Oro\Bundle\DataGridBundle\Extension\Action\ActionConfiguration;
use Oro\Bundle\DataGridBundle\Extension\MassAction\Actions\AbstractMassAction;

class MemberFeeCorrectionUnmarkAction extends AbstractMassAction {

	/** @var array */
	protected $requiredOptions = ['handler', 'entity_name', 'data_identifier'];

	/**
	 * {@inheritDoc}
	 */
	public function setOptions(ActionConfiguration $options)
	{
		if (empty($options['handler'])) {
			$options['handler'] = 'dmkclub_member.datagrid.mass_action.mark_fee_correction_handler';
		}

		if (empty($options['frontend_type'])) {
			// Der Wert bezieht sich auf den Key in der requirejs.yml
			$options['frontend_type'] = 'mark-feecorrection-mass';
		}

		if (empty($options['route'])) {
			$options['route'] = 'dmkclub_member_feecorrection_massaction';
		}

		if (empty($options['datagrid'])) {
			$options['datagrid'] = 'dmkclub-memberfees-grid-billing';
		}

		if (empty($options['route_parameters'])) {
			$options['route_parameters'] = [];
		}

		if (empty($options['frontend_handle'])) {
			$options['frontend_handle'] = 'ajax';
		}

		$options['mark_type'] = MemberFeeCorrectionHandler::UNMARK;
		$options['confirmation'] = false;

		return parent::setOptions($options);
	}

}