<?php
namespace DMKClub\Bundle\BasicsBundle\PDF;

use Knp\Bundle\GaufretteBundle\FilesystemMap;

class FileManager extends \Oro\Bundle\ImportExportBundle\File\FileManager
{
    /**
     * @param FilesystemMap $filesystemMap
     */
    public function __construct(FilesystemMap $filesystemMap, string $fileSystem)
    {
        $this->filesystem = $filesystemMap->get($fileSystem);
    }
}
