<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraints\Uuid as Base;

class Uuid extends Base
{
    const INVALID_UUID_ERROR = 'e41a564b-c912-4d19-bf2c-c70be934ee43';

    const INVALID_UUID_MESSAGE = 'This value {{ value }} is not a valid UUID';
}
