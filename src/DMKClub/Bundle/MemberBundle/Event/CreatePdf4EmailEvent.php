<?php
namespace DMKClub\Bundle\MemberBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Gaufrette\File;

class CreatePdf4EmailEvent extends Event
{

    const EVENT_NAME = 'dmk_club.mailer.create_pdf_4_email';

    protected $pdfFile;

    public function __construct(File $pdfFile)
    {
        $this->pdfFile = $pdfFile;
    }

    public function setPdfFile(File $pdfFile)
    {
        $this->pdfFile = $pdfFile;
    }

    public function getPdfFile():File
    {
        return $this->pdfFile;
    }
}
