<?php

declare(strict_types=1);

namespace Rector\Symfony\Tests\Rector\FrameworkBundle\GetToConstructorInjectionRector;

use Iterator;
use Rector\Configuration\Option;
use Rector\Symfony\Rector\FrameworkBundle\GetToConstructorInjectionRector;
use Rector\Symfony\Tests\FrameworkBundle\AbstractToConstructorInjectionRectorSource\SomeKernelClass;
use Rector\Symfony\Tests\Rector\FrameworkBundle\GetToConstructorInjectionRector\Source\GetTrait;
use Rector\Symfony\Tests\Rector\Source\SymfonyController;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class GetToConstructorInjectionRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->setParameter(Option::KERNEL_CLASS_PARAMETER, SomeKernelClass::class);
        $this->doTestFile($file);
    }

    public function provideDataForTest(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    /**
     * @return mixed[]
     */
    protected function getRectorsWithConfiguration(): array
    {
        return [
            GetToConstructorInjectionRector::class => [
                '$getMethodAwareTypes' => [SymfonyController::class, GetTrait::class],
            ],
        ];
    }
}
