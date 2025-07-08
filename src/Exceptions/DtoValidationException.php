<?php

namespace App\Exceptions;

class DtoValidationException extends \Exception
{
    private array $validationErrors;

    public function __construct(
        array $errors = [],
        string $message = 'Validation failed',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->setValidationErrors($errors);
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    private function setValidationErrors(array $errors): void
    {
        $this->validationErrors = $errors;
    }

}
