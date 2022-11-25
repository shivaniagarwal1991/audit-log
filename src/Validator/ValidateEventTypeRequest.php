<?php

namespace App\Validator;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait ValidateEventTypeRequest
{
    
    protected function hasValidEventTypeRequest(array|null $requestInput): void
    {
        if(empty($requestInput) || empty($requestInput['type'])) {
            throw new BadRequestHttpException('expecting_type_as_body_parameter');
        }
    }

    protected function hasValidDetailInRequest(array|null $requestInput): void
    {
        if(empty($requestInput) || empty($requestInput['detail'])) {
            throw new BadRequestHttpException('expecting_detail_as_body_parameter');
        }
    }


}