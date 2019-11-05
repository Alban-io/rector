<?php

declare(strict_types=1);

namespace Rector\Symfony\Tests\Rector\Controller\RedirectToRouteRector;

use Iterator;
use Rector\Symfony\Rector\Controller\RedirectToRouteRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class RedirectToRouteRectorTest extends AbstractRectorTestCase
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
        return RedirectToRouteRector::class;
    }
}
