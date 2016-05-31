<?php

namespace DMKClub\Bundle\BasicsBundle\PDF;


use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;

interface GeneratorInterface {

	public function getLabel();
	public function getName();
	public function execute(TwigTemplate $twigTemplate, $filename, array $context = array());

}
