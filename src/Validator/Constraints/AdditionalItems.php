<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use OAS\Validator\Configuration;
use OAS\Validator\Constraint;

/**
 * @internal to be used as nested constraint in OAS\Validator\Constraints\Schema
 */
class AdditionalItems extends Constraint
{
    const UNEXPECTED_ITEM_ERROR = '409b5914-ade5-4d1c-a883-7788532c5866';

    const UNEXPECTED_ITEM_MESSAGE = 'Index {{ index }} has not been defined and additional items are not allowed';

    public int $tupleLength;

    public OAS\Schema $additionalItemsSchema;

    public function __construct(OAS\Schema $additionalItemsSchema, int $tupleLength, string $path, Configuration $configuration)
    {
        $this->tupleLength = $tupleLength;
        $this->additionalItemsSchema = $additionalItemsSchema;
        parent::__construct("{$path}/additionalItems", $configuration);
    }
}
