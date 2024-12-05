<?php declare(strict_types=1);

namespace OAS\Validator;

interface Result
{
    public function isValid(): bool;

    /**
     * @return iterable<int, ConstraintViolation>
     */
    public function getViolations(): iterable;
}