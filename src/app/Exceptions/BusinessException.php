<?php

namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Exception;

class BusinessException extends Exception
{
    public function render($request)
    {
        return (new ApiResponse)->message($this->getMessage())->status($this->getCode())->json();
    }
}
