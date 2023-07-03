<?php

namespace App\Tests\Service\ExpectedBackup;

use App\Service\ExpectedBackup\Calculator;
use Exception;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    /**
     * @dataProvider appProvider
     *
     * @param array<int, array<string, string|float>> $backups
     * @param float $expected
     * @return void
     * @throws Exception
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new Calculator();
    }
}
