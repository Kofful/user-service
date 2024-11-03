<?php
declare(strict_types=1);

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class GetUsersDto
{
    public function __construct(
        #[NotBlank(allowNull: true)]
        #[Length(max: 8, maxMessage:  'The login field must be shorter than 8 characters.')]
        #[Type('string', message: 'The login field must be a string.')]
        public readonly ?string $login = null,
    ) {
    }
}
