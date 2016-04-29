<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DMKClub\Bundle\MemberBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds tagged twig.extension services to twig service.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MemberBillingProcessorPass implements CompilerPassInterface
{
	const TAG = 'dmkclub_member.billingprocessor';
	const MANAGER = 'dmkclub_member.memberbilling.processorprovider';

	public function process(ContainerBuilder $container)
	{
		if (false === $container->hasDefinition(self::MANAGER)) {
			return;
		}

		$definition = $container->getDefinition(self::MANAGER);
		$taggedServices = $container->findTaggedServiceIds(self::TAG);
		foreach (array_keys($taggedServices) as $id) {
			$definition->addMethodCall(
					'addProcessor',
					array(new Reference($id))
			);
		}

	}
}
