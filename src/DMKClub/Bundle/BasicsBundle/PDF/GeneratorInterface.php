<?php

namespace DMKClub\Bundle\BasicsBundle\PDF;


use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;

interface GeneratorInterface {

	public function getName();
	public function execute(TwigTemplate $twigTemplate, $filename, array $context = array());

}
