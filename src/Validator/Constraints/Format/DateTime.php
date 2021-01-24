<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraints\DateTime as Base;

class DateTime extends Base
{
    const INVALID_FORMAT_MESSAGE = 'This value {{ value }} is not a valid datetime';

    public $message = self::INVALID_FORMAT_MESSAGE;
}
