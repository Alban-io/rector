<?php

declare(strict_types=1);

namespace Rector\Doctrine\Tests\Rector\Class_\AlwaysInitializeUuidInEntityRector;

use Iterator;
use Rector\Doctrine\Rector\Class_\AlwaysInitializeUuidInEntityRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class AlwaysInitializeUuidInEntityRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->doTestFile($file);
    }

    public function provideDataForTest(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    protected function getRectorClass(): string
    {
        return AlwaysInitializeUuidInEntityRector::class;
    }
}
