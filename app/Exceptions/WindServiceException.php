<?php

namespace App\Exceptions;

use Exception;

class WindServiceException extends Exception
{
    /**
     * Render an exception into an HTTP response.
     *
     * @return string
     */
    public function render()
    {
        return 'no wind data';
    }
}
