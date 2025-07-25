<?php

namespace App\Dto\Response;

use App\Entity\User;
use App\Validator\UniqueEntityField;
use Symfony\Component\Validator\Constraints as Assert;

class UserResponseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $email,
        public ?string $phone,
        public ?array $roles,
    )
    {
    }
}
