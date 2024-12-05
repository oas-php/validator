<?php declare(strict_types=1);

namespace OAS\Validator\Constraints;

use OAS;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IfThenElseValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof IfThenElse) {
            throw new UnexpectedTypeException($constraint, IfThenElse::class);
        }

        $ifSchema = new Schema(
            $constraint->ifSchema,
            "{$constraint->getSchemaPath()}/if",
            $constraint->configuration,
            $constraint->enclosingSchemaConstraint,
            $constraint->referencingSchemaConstraint
        );

        $violations = $this->context->getValidator()->validate($value, $ifSchema);

        if (count($violations) == 0) {
            foreach ($ifSchema->successfullyEvaluatedPaths as $successfullyEvaluatedPath) {
                $constraint->enclosingSchemaConstraint
                    ->successfullyEvaluatedPaths
                    ->append($successfullyEvaluatedPath);
            }

            if ($constraint->thenSchema instanceof OAS\Schema) {
                $thenSchema = new Schema(
                    $constraint->thenSchema,
                    "{$constraint->getSchemaPath()}/then",
                    $constraint->configuration,
                    $constraint->enclosingSchemaConstraint,
                    $constraint->referencingSchemaConstraint
                );

                $violations = $this->context
                    ->getValidator()
                    ->inContext($this->context)
                    ->validate($value, $thenSchema)
                    ->getViolations();

                if ($violations->count() == 0) {
                    foreach ($thenSchema->successfullyEvaluatedPaths as $successfullyEvaluatedPath) {
                        $constraint->enclosingSchemaConstraint
                            ->successfullyEvaluatedPaths
                            ->append($successfullyEvaluatedPath);
                    }
                }
            }
        }

        // TODO: change to elseif
        if (count($violations) > 0 && $constraint->elseSchema instanceof OAS\Schema) {
            $elseSchema = new Schema(
                $constraint->elseSchema,
                "{$constraint->getSchemaPath()}/else",
                $constraint->configuration,
                $constraint->enclosingSchemaConstraint,
                $constraint->referencingSchemaConstraint
            );

            $violations = $this->context
                ->getValidator()
                ->inContext($this->context)
                ->validate($value, $elseSchema)
                ->getViolations();

            if ($violations->count() == 0) {
                foreach ($elseSchema->successfullyEvaluatedPaths as $successfullyEvaluatedPath) {
                    $constraint->enclosingSchemaConstraint
                        ->successfullyEvaluatedPaths
                        ->append($successfullyEvaluatedPath);
                }
            }
        }
    }
}
