<?php
namespace DMKClub\Bundle\MemberBundle\DataGrid\Extension\MassAction;

use Oro\Bundle\DataGridBundle\Extension\MassAction\Actions\Ajax\AjaxMassAction;
use Oro\Bundle\DataGridBundle\Extension\Action\ActionConfiguration;

class MemberFeeCorrectionAction extends AjaxMassAction {

	/**
	 * {@inheritDoc}
	 */
	public function setOptions(ActionConfiguration $options)
	{
		if (empty($options['handler'])) {
			$options['handler'] = 'dmkclub_member.extension.mass_action.handler.memberfeecorrection';
		}

		if (empty($options['frontend_type'])) {
			$options['frontend_type'] = 'memberfee-correction-mass';
		}

		return parent::setOptions($options);
	}

}