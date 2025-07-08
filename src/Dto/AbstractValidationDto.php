<?php

namespace App\Dto;

use App\Exceptions\DtoValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractValidationDto
{
    /**
     * @throws DtoValidationException
     */
    public function validate(ValidatorInterface $validator): void
    {
        $errors = $validator->validate($this);

        if (count($errors) > 0) {
            throw new DtoValidationException($this->formatErrors($errors));
        }
    }

    protected function formatErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];

        foreach ($errors as $violation) {
            $property = $violation->getPropertyPath();
            $message = $violation->getMessage();

            $errorMessages[$property][] = $message;
        }

        return $errorMessages;
    }
}

