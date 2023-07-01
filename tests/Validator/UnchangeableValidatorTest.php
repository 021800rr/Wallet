<?php

namespace App\Tests\Validator;

use App\Validator\Unchangeable;
use App\Validator\UnchangeableValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class UnchangeableValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): UnchangeableValidator
    {
        return new UnchangeableValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new Unchangeable());

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testInvalidValues(mixed $values): void
    {
        $constraint = new Unchangeable([
            'message' => 'myMessage',
        ]);

        /** @var string|int $values */
        $this->validator->validate($values, $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ string }}', (string) $values)
            ->assertRaised();
    }

    /**
     * @return array<int, mixed>
     */
    public static function getInvalidValues(): array
    {
        return [
            ['a'],
            [' '],
            [1],
        ];
    }
}
