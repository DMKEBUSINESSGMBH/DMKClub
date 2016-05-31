<?php

namespace DMKClub\Bundle\BasicsBundle\PDF;


use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;
use Oro\Bundle\ImportExportBundle\File\FileSystemOperator;
/**
 * Class PDF-Manager
 *
 * @package DMKClub\Bundle\DMKClubBasicsBundle\PDF
 */
class Manager {
	/**
	 * @var array
	 */
	protected $generators = [];


	/** @var \TCPDF */
	protected $tcpdf;
	/** @var FileSystemOperator */
	protected $fileSystemOperator;
	/**
	 *
	 * @param \TCPDF $tcpdf
	 */
	public function __construct(\WhiteOctober\TCPDFBundle\Controller\TCPDFController $tcpdf, $twig, FileSystemOperator $fso) {
		$this->tcpdf = $tcpdf;
		$this->twig = clone $twig;
		$this->twig->setLoader(new \Twig_Loader_String());
		$this->fileSystemOperator = $fso;
	}

	/**
	 *
	 * @param PdfAwareInterface $entity
	 * @throws PdfException
	 * @return string server path to file
	 */
	public function buildPdf(PdfAwareInterface $entity) {
		$twigTemplate = $entity->getTemplate();
		if(!$twigTemplate)
			throw new PdfException('No template instance found');

		$outputFormat = 'pdf';
		$fileName   = $this->fileSystemOperator->generateTemporaryFileName($entity->getFilenamePrefix(), $outputFormat);
		try {
			$this->createPdf($twigTemplate, $fileName, ['entity' => $entity]);
		}
		catch(Exception $e) {
			throw new PdfException('Error generating pdf file', 0, $e);
		}
		return $fileName;
	}
	/**
	 *
	 * @param TwigTemplate $twigTemplate
	 * @return string filename
	 */
	protected function createPdf(TwigTemplate $twigTemplate, $filename, array $context = array()) {
		if($generatorName = $twigTemplate->getGenerator()) {
			// Call generator
			$generator = $this->getGeneratorByName($generatorName);
			$generator->execute($twigTemplate, $filename, $context);

		}
		else {
			$this->generateByTemplate($twigTemplate, $filename, $context);
		}

		return $filename;

	}

	/**
	 *
	 * @param TwigTemplate $twigTemplate
	 * @param string $filename
	 * @param array $context
	 * @return string filename
	 */
	public function generateByTemplate(TwigTemplate $twigTemplate, $filename, array $context = array()) {
		// Zuerst das HTML erzeugen
		$html = $this->twig->render($twigTemplate->getTemplate(), $context);

		// mit Daten aus Template initialisieren
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
	}



	/**
	 * @param GeneratorInterface $generator
	 */
	public function addGenerator(GeneratorInterface $generator)
	{
		$this->generators[$generator->getName()] = $generator;
	}

	/**
	 * @return GeneratorInterface[]
	 */
	public function getGenerators()
	{
		return $this->generators;
	}

	/**
	 * @param string $name
	 * @return GeneratorInterface
	 */
	public function getGeneratorByName($name)
	{
		if ($this->hasGenerator($name)) {
			return $this->generators[$name];
		} else {
			throw new \RuntimeException(sprintf('Generator >%s< is unknown', $name));
		}
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasGenerator($name) {
		return isset($this->generators[$name]);
	}

	/**
	 * Auswahlliste für Form
	 * @return array
	 */
	public function getVisibleGeneratorChoices() {
		$choices = [];
		foreach ($this->getGenerators() as $generator) {
			$choices[$generator->getName()] = $generator->getLabel();
		}
		return $choices;
	}

}

