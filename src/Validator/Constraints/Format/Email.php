<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraints\Email as Base;

class Email extends Base
{
    const INVALID_FORMAT_MESSAGE = 'The value {{ value }} is not a valid e-mail.';

    public $message = self::INVALID_FORMAT_MESSAGE;
}
