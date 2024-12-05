<?php declare(strict_types=1);

namespace OAS\Validator\Symfony;

use OAS\Validator;
use OAS\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Result implements Validator\Result
{
    public function __construct(private ConstraintViolationListInterface $violations)
    {
    }

    public function isValid(): bool
    {
        return $this->violations->count() === 0;
    }

    /**
     * @return iterable<int, ConstraintViolation>
     */
    public function getViolations(): iterable
    {
        return $this->violations;
    }
}