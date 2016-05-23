<?php

namespace DMKClub\Bundle\BasicsBundle\PDF;


use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;
/**
 * Class PDF-Manager
 *
 * @package DMKClub\Bundle\DMKClubBasicsBundle\PDF
 */
class Manager {

	/** @var \TCPDF */
	protected $tcpdf;
	/**
	 *
	 * @param \TCPDF $tcpdf
	 */
	public function __construct(\WhiteOctober\TCPDFBundle\Controller\TCPDFController $tcpdf, $twig) {
		$this->tcpdf = $tcpdf;
		$this->twig = clone $twig;
		$this->twig->setLoader(new \Twig_Loader_String());
	}

	/**
	 *
	 * @param TwigTemplate $twigTemplate
	 * @return string filename
	 */
	public function createPdf(TwigTemplate $twigTemplate, $filename, array $context = array()) {

		// Zuerst das HTML erzeugen
		$html = $this->twig->render($twigTemplate->getTemplate(), $context);

		// TODO: mit Daten aus Template initialisieren
		$orientation = $twigTemplate->getOrientation() ? $twigTemplate->getOrientation() : 'P';
		// Format kann auch ein assoziatives Array sein.
		$pageFormat = $twigTemplate->getPageFormat() ? $twigTemplate->getPageFormatStructured() : 'A4';
		$pdf_a = true;

		$pdf = $this->tcpdf->create(
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
		$pdf->AddPage();

		$pdf->writeHTML($html);
		$pdf->lastPage();

		$pdf->Output($filename, 'F');
		return $filename;

	}
}

