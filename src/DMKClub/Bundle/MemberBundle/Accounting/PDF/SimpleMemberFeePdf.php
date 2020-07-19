<?php
namespace DMKClub\Bundle\MemberBundle\Accounting\PDF;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bridge\Twig\TwigEngine;

use Oro\Bundle\ContactBundle\Entity\Contact;
use Oro\Bundle\AddressBundle\Entity\Address;

use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\BasicsBundle\PDF\GeneratorInterface;
use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use Twig\Environment;

/**
 */
class SimpleMemberFeePdf implements GeneratorInterface
{

    const NAME = 'simplefee';

    const FONT_FAMILY_DEFAULT = 'helvetica';

    const FONT_FAMILY_BOLD = 'helveticaB';

    /**
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /** @var Environment */
    protected $twig;

    /** @var \WhiteOctober\TCPDFBundle\Controller\TCPDFController */
    protected $tcpdfController;

    public function __construct(\WhiteOctober\TCPDFBundle\Controller\TCPDFController $tcpdfController, TranslatorInterface $translator, Environment $twig)
    {
        $this->tcpdfController = $tcpdfController;
        $this->translator = $translator;
        $this->twig = clone $twig;
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function getLabel()
    {
        return $this->getName();
    }

    /*
     * (non-PHPdoc)
     * @see \DMKClub\Bundle\BasicsBundle\PDF\GeneratorInterface::execute()
     */
    public function execute(TwigTemplate $twigTemplate, $filename, array $context = array())
    {
        $memberFee = $this->getMemberFee($context);
        $pdf = $this->initPdf($twigTemplate);
        $this->writeMetaData($pdf, $memberFee);
        $pdf->AddPage();

        $pdfContext = $this->createPdfContext();
        $this->writeAddress($pdf, $pdfContext, $memberFee->getMember());

        $this->writeContent($pdf, $pdfContext, $memberFee, $twigTemplate);

        $pdf->lastPage();
        $pdf->Output($filename, 'F');
    }

    /** @var \TCPDF */
    protected $combinedPdf;

    public function combinedInit(TwigTemplate $twigTemplate)
    {
        $this->combinedPdf = $this->initPdf($twigTemplate);
    }

    public function combinedExecute(TwigTemplate $twigTemplate, array $context = array())
    {
        $memberFee = $this->getMemberFee($context);
        $this->combinedPdf->AddPage();
        $pdfContext = $this->createPdfContext();
        $this->writeAddress($this->combinedPdf, $pdfContext, $memberFee->getMember());

        $this->writeContent($this->combinedPdf, $pdfContext, $memberFee, $twigTemplate);
    }

    public function combinedFinalize($filename)
    {
        $this->combinedPdf->lastPage();
        $this->combinedPdf->Output($filename, 'F');
    }

    /**
     * Adressblock
     *
     * @param \TCPDF $pdf
     * @param \stdClass $pdfContext
     * @param Member $member
     */
    protected function writeAddress(\TCPDF $pdf, $pdfContext, Member $member)
    {
        $y = 70;
        $w = 100;
        $border = 0;
        $lineDistance = $pdfContext->cellHeight + 0;

        $address = $member->getPostalAddress();
        if (! $address)
            return;

        $pdf->SetY($y);
        $pdf->Cell($w, $pdfContext->cellHeight, $this->getMemberName($address, $member), $border);

        if ($address->getStreet()) {
            $y += $lineDistance;
            $pdf->SetY($y);
            $pdf->Cell($w, $pdfContext->cellHeight, $address->getStreet(), $border);
        }
        if ($address->getStreet2()) {
            $y += $lineDistance;
            $pdf->SetY($y);
            $pdf->Cell($w, $pdfContext->cellHeight, $address->getStreet2(), $border);
        }
        $y += $lineDistance;
        $pdf->SetY($y);
        $pdf->Cell($w, $pdfContext->cellHeight, trim($address->getPostalCode() . ' ' . $address->getCity()), $border);
    }

    /**
     * Main content
     *
     * @param \TCPDF $pdf
     * @param \stdClass $pdfContext
     * @param MemberFee $fee
     */
    protected function writeContent(\TCPDF $pdf, $pdfContext, MemberFee $fee, $twigTemplate)
    {
        $y = 100;
        // $border = 0;
        // $lineDistance = $pdfContext->cellHeight + 0;

        $pdf->SetY($y);

        // Den Content-String umsetzen
        $template = $this->twig->createTemplate($twigTemplate->getTemplate());
        $html = $template->render([
            'entity' => $fee
        ]);
//         $html = $this->twig->render($twigTemplate->getTemplate(), array(
//             'entity' => $fee
//         ));
        $html = str_replace('[SALUTATION]', $this->buildSalutation($fee), $html);
        $html = str_replace('[POSITIONS]', $this->buildPositions($fee), $html);

        $pdf->writeHTMLCell(0, $pdfContext->cellHeight, $pdf->GetX(), $y, $html);
    }

    /**
     * Position bauen
     *
     * @param MemberFee $fee
     * @throws \Exception
     */
    protected function buildPositions(MemberFee $fee)
    {
        $lines = array();
        foreach ($fee->getPositions() as $position) {
            $line = array();
            $line[] = $position->getDescription();
            $line[] = number_format($position->getPriceTotal() / 100, 2, ',', '.') . ' EUR';
            $lines[] = '<td>' . implode('</td><td align="right">', $line) . '</td>';
        }
        $table = '<table>';
        $table .= '<tr>' . implode('</tr><tr>', $lines) . '</tr>';

        return $table;
    }

    protected function lookupPrefix(Contact $contact, $address)
    {
        $prefix = $contact->getNamePrefix();
        if (!$prefix && $address) {
            $prefix = $address->getNamePrefix();
        }
        return $prefix;
    }
    /**
     * Anredezeile bauen
     *
     * @param MemberFee $fee
     * @throws \Exception
     */
    protected function buildSalutation(MemberFee $fee)
    {
        $contact = $fee->getMember()->getContact();
        if (! $contact) {
            throw new \Exception('No contact for member fee ' . $fee->getId() . ' found');
        }
        $address = $fee->getMember()->getPostalAddress();
        $gender = $contact->getGender();
        $gender = $gender ? $gender : 'unknown';
        $salutation = $this->translator->trans('dmkclub.member.memberbilling.pdf.salutation.' . $gender);
        // Titel suchen
        $prefix = $this->lookupPrefix($contact, $address);
        $salutation = trim($salutation . ' ' . $prefix);
        // Name
        $name = $address ? $address->getLastName() : '';
        $name = $name ? $name : $contact->getLastName();
        $salutation = trim($salutation . ' ' . $name);
        return $salutation;
    }

    /**
     * Name des Mitglieds holen.
     * Adresse hat Vorrang, Name im Mitglied ist Fallback.
     *
     * @param Address $address
     * @param Member $member
     */
    protected function getMemberName($address, $member)
    {
        if ($address->getFirstName() && $address->getLastName()) {
            return $address->getFirstName() . ' ' . $address->getLastName();
        }
        return $member->getName();
    }

    protected function createPdfContext()
    {
        $context = new \stdClass();
        $context->cellHeight = 5;
        return $context;
    }

    protected function writeMetaData(\TCPDF $pdf, MemberFee $memberFee)
    {
        $pdf->SetTitle($this->translator->trans('dmkclub.payment.bill.billfor') . ' ' . $memberFee->getMember()
            ->getName());
    }

    /**
     *
     * @param array $context
     * @return MemberFee
     */
    protected function getMemberFee($context)
    {
        return $context['entity'];
    }

    /**
     *
     * @param TwigTemplate $twigTemplate
     * @return \TCPDF
     */
    protected function initPdf(TwigTemplate $twigTemplate)
    {
        $orientation = $twigTemplate->getOrientation() ? $twigTemplate->getOrientation() : 'P';
        // Format kann auch ein assoziatives Array sein.
        $pageFormat = $twigTemplate->getPageFormat() ? $twigTemplate->getPageFormatStructured() : 'A4';
        $pdf_a = false;

        $pdf = $this->tcpdfController->create($orientation, PDF_UNIT, $pageFormat, true, 'UTF-8', false, $pdf_a);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetAuthor('dmkclub');
        // $pdf->SetTitle('Prueba TCPDF');
        // $pdf->SetSubject('Your client');
        // $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->setFontSubsetting(true);

        $pdf->SetFont('helvetica', '', 12, '', true);
        $pdf->SetMargins(PDF_MARGIN_LEFT + 10, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        return $pdf;
    }
}
