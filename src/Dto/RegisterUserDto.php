<?php

namespace App\Dto;

use App\Entity\User;
use App\Validator\UniqueEntityField;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDto extends AbstractValidationDto
{
    #[Assert\NotBlank, Assert\Length(min: 3, max: 100)]
    private ?string $name;

    #[Assert\NotBlank, Assert\Email, UniqueEntityField(User::class, 'email')]
    private ?string $email;

    #[Assert\NotBlank, Assert\Length(min: 3, max: 100)]
    private ?string $phone;

    #[Assert\NotBlank, Assert\Length(min: 5, max: 100)]
    private ?string $password;

    #[Assert\NotBlank]
    private ?array $roles;

    public function __construct(array $data)
    {
        $this->setName($data['name'] ?? null);
        $this->setEmail($data['email'] ?? null);
        $this->setPhone($data['phone'] ?? null);
        $this->setPassword($data['password'] ?? null);
        $this->setRoles(['ROLE_USER']);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): void
    {
        $this->roles = $roles;
    }
}
