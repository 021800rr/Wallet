<?php

namespace App\Tests\Controller;

use App\Controller\ApiFeeController;
use App\Handler\FeeHandler;
use App\Service\FixedFees\FixedFeesInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class ApiFeeControllerTest extends TestCase
{
    public function testInvoke(): void
    {
        $feeHandler = $this->createMock(FeeHandler::class);
        $feeHandler->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(FixedFeesInterface::class));

        $fixedFees = $this->createMock(FixedFeesInterface::class);

        $controller = new ApiFeeController();

        $controller($feeHandler, $fixedFees);
    }

    public function testHandleThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error handling fixed fees');

        $feeHandler = $this->createMock(FeeHandler::class);
        $feeHandler->method('handle')
            ->will(
                $this->throwException(new Exception('Error handling fixed fees'))
            );

        $fixedFees = $this->createMock(FixedFeesInterface::class);

        $controller = new ApiFeeController();

        $controller($feeHandler, $fixedFees);
    }
}
