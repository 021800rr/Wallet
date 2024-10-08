<?php

namespace App\Tests\Service\ExpectedBackup;

use App\Service\ExpectedBackup\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new Calculator();
    }

    /**
     * @dataProvider appProvider
     *
     * @param array<int, array<string, string|float>> $backups
     */
    public function testCompute(array $backups, float $expected): void
    {
        $this->assertSame($expected, $this->calculator->compute($backups));
    }

    /**
     * @return array<int, array<int, array<int, array<string, string|float>>|int>>
     */
    public function appProvider(): array
    {
        return
            [
                [
                    [
                        [
                            'yearMonth' => '2021-06',
                            'sum_of_amounts' => 300.0,
                        ],
                        [
                            'yearMonth' => '2021-05',
                            'sum_of_amounts' => 300.0,
                        ],
                    ],
                    300
                ],
                [
                    [
                        [
                            'yearMonth' => '2021-06',
                            'sum_of_amounts' => 300.0,
                        ],
                        [
                            'yearMonth' => '2021-04',
                            'sum_of_amounts' => 300.0,
                        ],
                    ],
                    200
                ]
            ]
        ;
    }
}
