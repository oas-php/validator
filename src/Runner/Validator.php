<?php declare(strict_types=1);

namespace OAS\Runner;

interface Validator
{
    /**
     * @throws UnresolvableRef
     */
    public function isValid(mixed $instance, array|bool $schema): bool;
}