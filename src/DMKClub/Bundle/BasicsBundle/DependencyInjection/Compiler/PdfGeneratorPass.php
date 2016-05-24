<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DMKClub\Bundle\BasicsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds tagged dmkclub_basics.pdfgenerator services to pdf service.
 */
class PdfGeneratorPass implements CompilerPassInterface
{
	const TAG = 'dmkclub_basics.pdfgenerator';
	const MANAGER = 'dmkclub_basics.pdf.manager';

	public function process(ContainerBuilder $container)
	{
		if (false === $container->hasDefinition(self::MANAGER)) {
			return;
		}

		$definition = $container->getDefinition(self::MANAGER);
		$taggedServices = $container->findTaggedServiceIds(self::TAG);
		foreach (array_keys($taggedServices) as $id) {
			$definition->addMethodCall(
					'addGenerator',
					array(new Reference($id))
			);
		}

	}
}
