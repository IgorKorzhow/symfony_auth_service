<?php

namespace App\Dto\Request;

use App\Entity\User;
use App\Validator\UniqueEntityField;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterRequestDto
{
    public function __construct(
        #[Assert\NotBlank, Assert\Length(min: 3, max: 100)]
        public ?string $name,

        #[Assert\NotBlank, Assert\Email, UniqueEntityField(User::class, 'email')]
        public ?string $email,

        #[Assert\NotBlank, Assert\Length(min: 3, max: 100)]
        public ?string $phone,

        #[Assert\NotBlank, Assert\Length(min: 5, max: 100)]
        public ?string $password,
    )
    {
    }
}
