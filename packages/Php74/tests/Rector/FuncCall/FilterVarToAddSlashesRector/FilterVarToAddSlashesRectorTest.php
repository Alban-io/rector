<?php

declare(strict_types=1);

namespace Rector\Php74\Tests\Rector\FuncCall\FilterVarToAddSlashesRector;

use Iterator;
use Rector\Php74\Rector\FuncCall\FilterVarToAddSlashesRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class FilterVarToAddSlashesRectorTest extends AbstractRectorTestCase
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
        return FilterVarToAddSlashesRector::class;
    }
}
