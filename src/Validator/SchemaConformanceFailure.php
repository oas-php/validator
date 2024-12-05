<?php declare(strict_types=1);

namespace OAS\Validator;

use OAS\Schema;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class SchemaConformanceFailure extends \Exception
{
    /** @var mixed */
    private $instance;
    private Schema|bool $schema;
    private ConstraintViolationListInterface $violations;

    public function __construct(
        $instance,
        Schema|bool $schema,
        ConstraintViolationListInterface $violations
    ) {
        parent::__construct(__CLASS__);
        $this->instance = $instance;
        $this->schema = $schema;
        $this->violations = $violations;
    }

    public function getInstance()
    {
        return $this->instance;
    }

    public function getSchema(): Schema|bool
    {
        return $this->schema;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
