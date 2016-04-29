<?php

namespace DMKClub\Bundle\MemberBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use DMKClub\Bundle\MemberBundle\DependencyInjection\Compiler\MemberBillingProcessorPass;

class DMKClubMemberBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container);

		$container->addCompilerPass(new MemberBillingProcessorPass());
	}

}
