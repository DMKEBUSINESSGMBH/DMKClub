<?php

namespace DMKClub\Bundle\MemberBundle\Accounting\PDF;


use DMKClub\Bundle\MemberBundle\Form\Type\SimpleProcessorSettingsType;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\BasicsBundle\PDF\GeneratorInterface;
use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
/**
 */
class SimpleMemberFeePdf implements GeneratorInterface {
	const NAME = 'simplefee';

	/** @var \WhiteOctober\TCPDFBundle\Controller\TCPDFController */
	protected $tcpdfController;

	public function __construct(\WhiteOctober\TCPDFBundle\Controller\TCPDFController $tcpdfController) {
		$this->tcpdfController = $tcpdfController;

	}
	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return self::NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLabel() {
		return $this->getName();
	}

	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\BasicsBundle\PDF\GeneratorInterface::execute()
	 */
	public function execute(TwigTemplate $twigTemplate, $filename, array $context = array()) {
		$entity = $context['entity'];
		$pdf = $this->initPdf($twigTemplate);
		$pdf->AddPage();

		$this->writeContent($pdf, $entity);

		$pdf->lastPage();
		$pdf->Output($filename, 'F');

	}

	protected function writeContent(\TCPDF $pdf, MemberFee $fee) {

		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->Write(0, 'Hello Member Fee ' . $fee->getBilling()->getName());
	}

	/**
	 *
	 * @param TwigTemplate $twigTemplate
	 * @return \TCPDF
	 */
	protected function initPdf(TwigTemplate $twigTemplate) {
		$orientation = $twigTemplate->getOrientation() ? $twigTemplate->getOrientation() : 'P';
		// Format kann auch ein assoziatives Array sein.
		$pageFormat = $twigTemplate->getPageFormat() ? $twigTemplate->getPageFormatStructured() : 'A4';
		$pdf_a = true;

		$pdf = $this->tcpdfController->create(
				$orientation,
				PDF_UNIT,
				$pageFormat,
				true,
				'UTF-8',
				false,
				$pdf_a
		);

		$pdf->SetAuthor('dmkclub');
		// 		$pdf->SetTitle('Prueba TCPDF');
		// 		$pdf->SetSubject('Your client');
		//		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		$pdf->setFontSubsetting(true);

		$pdf->SetFont('helvetica', '', 11, '', true);
		return $pdf;
	}
}
