<?php

declare(strict_types=1);

namespace Rector\Restoration\Tests\Rector\ClassConstFetch\MissingClassConstantReferenceToStringRector;

use Iterator;
use Rector\Restoration\Rector\ClassConstFetch\MissingClassConstantReferenceToStringRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class MissingClassConstantReferenceToStringRectorTest extends AbstractRectorTestCase
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
        return MissingClassConstantReferenceToStringRector::class;
    }
}
