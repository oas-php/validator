<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraints\Hostname as Base;

class Hostname extends Base
{
    const INVALID_HOSTNAME_MESSAGE = 'The value {{ value }} is not a valid hostname';

    public $message = self::INVALID_HOSTNAME_MESSAGE;
}
