<?php

declare(strict_types=1);

namespace Test\Serializer;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Throwable;

class TestCase extends PHPUnitTestCase
{
    private ?Throwable $expectedExceptionObject = null;

    /**
     * @inheritDoc
     */
    protected function runTest(): void
    {
        try {
            parent::runTest();
        } catch (Throwable $exception) {
            if ($this->expectedExceptionObject === null) {
                throw $exception;
            }

            $this->assertThat($exception, new IsEqual($this->expectedExceptionObject));

            return;
        }

        if ($this->expectedExceptionObject !== null) {
            throw new AssertionFailedError(
                sprintf(
                    'Failed asserting that exception with message "%s" is thrown',
                    get_class($this->expectedExceptionObject)
                )
            );
        }
    }

    public function expectExceptionObject(Throwable $exception): void
    {
        $this->expectedExceptionObject = $exception;
    }
}
