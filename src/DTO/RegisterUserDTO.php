<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class RegisterUserDTO
{
    public function __construct(
        #[Email]
        #[NotBlank]
        public string $email,
        #[NotBlank]
        public string $password,
    ) {}
}
