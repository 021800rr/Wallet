<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class Unchangeable extends Constraint
{
    public string $message = 'Changed field "{{ string }}" have been submitted, which must not be changed.';
}
