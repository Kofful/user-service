<?php
declare(strict_types=1);

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class LoginDto
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
    ) {
    }
}
