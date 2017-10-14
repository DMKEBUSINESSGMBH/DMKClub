<?php

namespace DMKClub\Bundle\BasicsBundle\Model;

use Exception;

/**
 * Email template not found
 */
class TemplateNotFoundException extends Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
