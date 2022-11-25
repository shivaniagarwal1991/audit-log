<?php

namespace App\Validator;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

trait ValidateRequestHeader
{
    /**
     * @param string|null $xApiKey
     * @return void
     */
    protected function hasValidRequestHeader(string|null $xApiKey): void
    {
        if(empty($xApiKey)) {
            throw new BadRequestHttpException('expecting_x_api_key');
        }

        if($xApiKey != $this->getParameter('x_api_key')) {
            throw new UnauthorizedHttpException('','invalid_x_api_key');
        }
    }

}