<?php

namespace DMKClub\Bundle\BasicsBundle\PDF;


use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;

interface PdfAwareInterface {

	/**
	 * @return TwigTemplate
	 */
	public function getTemplate();

	/**
	 * customer prefix vor filename
	 * @return string
	 */
	public function getFilenamePrefix();

}
