<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueEntityField extends Constraint
{
    public string $message = 'Field {{ field }} must be unique.';

    public string $entityClass;

    public string $field;

    // all configurable options must be passed to the constructor
    public function __construct(string $entityClass, string $field, ?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->entityClass = $entityClass;
        $this->field = $field;
    }
}
