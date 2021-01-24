<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraints\Ip as Base;

class Ip extends Base
{
    const INVALID_IP_MESSAGE = 'The value {{ value }} is not a valid e-mail';

    public $message = self::INVALID_IP_MESSAGE;
}
