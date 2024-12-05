<?php declare(strict_types=1);

namespace OAS\Runner\Report;

enum Result
{
    case SUCCESS;
    case FAILURE;
    case ERROR;

    public function isSuccess(): bool
    {
        return $this == self::SUCCESS;
    }

    public function isFailure(): bool
    {
        return $this === self::FAILURE;
    }

    public function isError(): bool
    {
        return $this === self::ERROR;
    }
}
