<?php
declare(strict_types=1);

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class CreateUserDto
{
    public function __construct(
        #[NotBlank(message: 'The login field is required.')]
        #[Length(max: 8, maxMessage:  'The login field must be shorter than 8 characters.')]
        #[Type('string', message: 'The login field must be a string.')]
        public readonly ?string $login,

        #[NotBlank(message: 'The pass field is required.')]
        #[Length(max: 8, maxMessage:  'The pass field must be shorter than 8 characters.')]
        #[Type('string', message: 'The pass field must be a string.')]
        public readonly ?string $pass,

        #[NotBlank(message: 'The phone field is required.')]
        #[Length(max: 8, maxMessage:  'The phone field must be shorter than 8 characters.')]
        #[Regex(pattern: '/^[0-9]+$/', message: 'The phone field must contain only numbers.')]
        #[Type('string', message: 'The phone field must be a string.')]
        public readonly ?string $phone,
    ) {
    }
}
