<?php

namespace App\Validator;

use App\Entity\Contractor;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UnchangeableValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Unchangeable) {
            throw new UnexpectedTypeException($constraint, Unchangeable::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if ($value) {
            if ($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif ($value instanceof Contractor) {
                $value = $value->getDescription();
            } else {
                $value = (string) $value;
            }

            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
