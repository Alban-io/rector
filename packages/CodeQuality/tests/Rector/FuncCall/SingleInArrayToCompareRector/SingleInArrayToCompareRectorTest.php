<?php

declare(strict_types=1);

namespace Rector\CodeQuality\Tests\Rector\FuncCall\SingleInArrayToCompareRector;

use Iterator;
use Rector\CodeQuality\Rector\FuncCall\SingleInArrayToCompareRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class SingleInArrayToCompareRectorTest extends AbstractRectorTestCase
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
        return SingleInArrayToCompareRector::class;
    }
}
