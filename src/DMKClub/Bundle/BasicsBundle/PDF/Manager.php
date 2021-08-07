<?php
namespace DMKClub\Bundle\BasicsBundle\PDF;

use Monolog\Logger;
use Gaufrette\File;
use Oro\Bundle\ImportExportBundle\File\FileManager;
use Oro\Bundle\GaufretteBundle\FileManager as GaufretteFileManager;

use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;
use Twig\Environment;

/**
 * Class PDF-Manager
 *
 * @package DMKClub\Bundle\DMKClubBasicsBundle\PDF
 */
class Manager
{
    /**
     *
     * @var array
     */
    protected $generators = [];

    /** @var \TCPDF */
    protected $tcpdf;

    /** @var FileManager */
    protected $fileManager;

    /** @var GaufretteFileManager */
    private $gaufretteFileManager;

    /** @var Logger */
    protected $logger;

    /** Environment */
    protected $twig;

    /**
     *
     * @param \TCPDF $tcpdf
     */
    public function __construct(\WhiteOctober\TCPDFBundle\Controller\TCPDFController $tcpdf, Environment $twig, FileManager $fm, GaufretteFileManager $gaufretteFileManager, Logger $logger)
    {
        $this->tcpdf = $tcpdf;
        $this->twig = clone $twig;
        $this->fileManager = $fm;
        $this->gaufretteFileManager = $gaufretteFileManager;
        $this->logger = $logger;
    }

    /**
     *
     * @param PdfAwareInterface $entity
     * @throws PdfException
     * @return File
     */
    public function buildPdf(PdfAwareInterface $entity): File
    {
        $twigTemplate = $entity->getTemplate();
        if (! $twigTemplate) {
            throw new PdfException('No template instance found');
        }

        $outputFormat = 'pdf';
        $fileName = $this->fileManager->generateFileName($entity->getFilenamePrefix(), $outputFormat);
        $localFile = $this->fileManager->generateTmpFilePath($fileName);
        try {
            $this->createPdf($twigTemplate, $localFile, [
                'entity' => $entity
            ]);
            $this->fileManager->writeFileToStorage($localFile, $fileName, true);
        } catch (\Exception $e) {
            $this->logger->error('Error generating pdf file', [
                'e' => $e,
                'local file' => $localFile
            ]);
            throw new PdfException('Error generating pdf file', 0, $e);
        }
        finally {
            if (file_exists($localFile)) {
                unlink($localFile);
            }
        }
        return $this->gaufretteFileManager->getFile($fileName);
    }

    public function buildPdfCombined($nextEntity): File
    {
        $twigTemplate = null;
        $pdfGenerator = null;

        $nextEntity(function (PdfAwareInterface $entity) use ($twigTemplate, &$pdfGenerator) {
            if ($twigTemplate === null) {
                $twigTemplate = $entity->getTemplate();
                if (! $twigTemplate) {
                    throw new PdfException('No template instance found');
                }
                if ($pdfGenerator === null) {
                    // Call generator
                    $pdfGenerator = $this->getGeneratorByName($twigTemplate->getGenerator());
                    $pdfGenerator->combinedInit($twigTemplate);
                }
                $pdfGenerator->combinedExecute($twigTemplate, [
                    'entity' => $entity
                ]);
            }
        });
        $outputFormat = 'pdf';
        $fileName = $this->fileManager->generateFileName('pdfFile', $outputFormat);
        $localFile = $this->fileManager->generateTmpFilePath($fileName);
        $pdfGenerator->combinedFinalize($localFile);
        $this->fileManager->writeFileToStorage($localFile, $fileName);

        if (file_exists($localFile)) {
            unlink($localFile);
        }

        return $this->gaufretteFileManager->getFile($fileName);
    }

    /**
     *
     * @param TwigTemplate $twigTemplate
     * @return string filename
     */
    protected function createPdf(TwigTemplate $twigTemplate, $filename, array $context = array())
    {
        if ($generatorName = $twigTemplate->getGenerator()) {
            // Call generator
            $generator = $this->getGeneratorByName($generatorName);
            $generator->execute($twigTemplate, $filename, $context);
        } else {
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
    public function generateByTemplate(TwigTemplate $twigTemplate, $filename, array $context = array())
    {
        // Zuerst das HTML erzeugen
        $template = $this->twig->createTemplate($twigTemplate->getTemplate());
        $html = $template->render($context);

        // mit Daten aus Template initialisieren
        $orientation = $twigTemplate->getOrientation() ? $twigTemplate->getOrientation() : 'P';
        // Format kann auch ein assoziatives Array sein.
        $pageFormat = $twigTemplate->getPageFormat() ? $twigTemplate->getPageFormatStructured() : 'A4';
        $pdf_a = true;

        $pdf = $this->tcpdf->create($orientation, PDF_UNIT, $pageFormat, true, 'UTF-8', false, $pdf_a);

        $pdf->SetAuthor('dmkclub');
        // $pdf->SetTitle('Prueba TCPDF');
        // $pdf->SetSubject('Your client');
        // $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->setFontSubsetting(true);

        $pdf->SetFont('helvetica', '', 11, '', true);
        $pdf->AddPage();

        $pdf->writeHTML($html);
        $pdf->lastPage();

        $pdf->Output($filename, 'F');
    }

    /**
     *
     * @param GeneratorInterface $generator
     */
    public function addGenerator(GeneratorInterface $generator)
    {
        $this->generators[$generator->getName()] = $generator;
    }

    /**
     *
     * @return GeneratorInterface[]
     */
    public function getGenerators()
    {
        return $this->generators;
    }

    /**
     *
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
     *
     * @param string $name
     * @return bool
     */
    public function hasGenerator($name)
    {
        return isset($this->generators[$name]);
    }

    /**
     * Auswahlliste für Form
     *
     * @return array
     */
    public function getVisibleGeneratorChoices()
    {
        $choices = [];
        foreach ($this->getGenerators() as $generator) {
            $choices[$generator->getName()] = $generator->getLabel();
        }
        return $choices;
    }
}
