<?php
namespace DMKClub\Bundle\PaymentBundle\DataGrid\Extension\MassAction;

use Oro\Bundle\DataGridBundle\Extension\Action\ActionConfiguration;
use Oro\Bundle\DataGridBundle\Extension\MassAction\Actions\AbstractMassAction;

class SepaDebitXmlAction extends AbstractMassAction
{

    /** @var array */
    protected $requiredOptions = [
        'entity_name',
        'data_identifier'
    ];

    /**
     *
     * {@inheritdoc}
     */
    public function setOptions(ActionConfiguration $options)
    {
        if (empty($options['route'])) {
            // Wir nutzen den zentralen Controller von Oro für den Dispatch
            $options['route'] = 'oro_datagrid_mass_action';
        }

        if (empty($options['route_parameters'])) {
            // Das Array muss initialisiert werden, damit die Parameter per JS gesetzt werden
            $options['route_parameters'] = [];
        }

        if (empty($options['handler'])) {
            $options['handler'] = SepaDebitXmlHandler::class;
        }

        if (empty($options['frontend_type'])) {
            // Der Wert bezieht sich auf den Key in der requirejs.yml, wobei das "-action" weggelassen werden muss
            $options['frontend_type'] = 'dmksepadebitxml-mass';
        }

        if (empty($options['frontend_handle'])) {
            $options['frontend_handle'] = 'ajax';
        }

        $options['confirmation'] = false;

        return parent::setOptions($options);
    }
}