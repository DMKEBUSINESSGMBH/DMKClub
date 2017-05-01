<?php

namespace DMKClub\Bundle\MemberBundle\Form\EntityField\Handler;


use Oro\Bundle\EntityBundle\Form\EntityField\Handler\Processor\EntityApiHandlerInterface;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;

class MemberFeeHandler implements EntityApiHandlerInterface
{
	private $data = [];

	/**
	 * @param MemberFee $entity
	 * {@inheritDoc}
	 * @see \Oro\Bundle\EntityBundle\Form\EntityField\Handler\Processor\EntityApiHandlerInterface::preProcess()
	 */
	public function preProcess($entity) {
		// Hier steht der alte Wert in Cent aus der DB in der Entity
		// Save the data
		$this->data[$entity->getId()] = [
			'paid' => [
				'value' => $entity->getPayedTotal(),
				'changed' => false,
			]
		];
	}

	/**
	 * @param MemberFee $entity
	 * {@inheritDoc}
	 * @see \Oro\Bundle\EntityBundle\Form\EntityField\Handler\Processor\EntityApiHandlerInterface::beforeProcess()
	 */
	public function beforeProcess($entity) {
		// was data changed?
		$old = $this->data[$entity->getId()]['paid'];
		$new = (int) $entity->getPayedTotal();
		if($new != $old) {
			// Wert wurde editiert
			$this->data[$entity->getId()]['paid']['changed'] = true;
		}
	}

	/**
	 * @param MemberFee $entity
	 * {@inheritDoc}
	 * @see \Oro\Bundle\EntityBundle\Form\EntityField\Handler\Processor\EntityApiHandlerInterface::afterProcess()
	 */
	public function afterProcess($entity) {
		if($this->data[$entity->getId()]['paid']['changed']) {
			$response = [
					'fields' => [],
			];
			// Hier stehen Cent in der Entity. Also durch 100 rechnen
			$response['fields']['payedTotal'] = $entity->getPayedTotal() / 100;
		}
		return $response;
	}

	/**
	 * {@inheritDoc}
	 * @see \Oro\Bundle\EntityBundle\Form\EntityField\Handler\Processor\EntityApiHandlerInterface::invalidateProcess()
	 */
	public function invalidateProcess($entity) {
	}

	/**
	 * {@inheritDoc}
	 * @see \Oro\Bundle\EntityBundle\Form\EntityField\Handler\Processor\EntityApiHandlerInterface::getClass()
	 */
	public function getClass() {
		return MemberFee::class;
	}
}
