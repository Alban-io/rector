<?php

declare(strict_types=1);

namespace Rector\Php52\Tests\Rector\Property\VarToPublicPropertyRector;

use Iterator;
use Rector\Php52\Rector\Property\VarToPublicPropertyRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class VarToPublicPropertyRectorTest extends AbstractRectorTestCase
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
        return VarToPublicPropertyRector::class;
    }
}
