<?php

namespace DMKClub\Bundle\BasicsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use DMKClub\Bundle\BasicsBundle\DependencyInjection\Compiler\PdfGeneratorPass;

class DMKClubBasicsBundle extends Bundle {
	public function build(ContainerBuilder $container) {
		parent::build($container);

		$container->addCompilerPass(new PdfGeneratorPass());
	}
}
