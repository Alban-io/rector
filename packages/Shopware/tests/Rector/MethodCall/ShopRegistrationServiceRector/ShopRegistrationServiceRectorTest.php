<?php

declare(strict_types=1);

namespace Rector\Shopware\Tests\Rector\MethodCall\ShopRegistrationServiceRector;

use Iterator;
use Rector\Shopware\Rector\MethodCall\ShopRegistrationServiceRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class ShopRegistrationServiceRectorTest extends AbstractRectorTestCase
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
        return ShopRegistrationServiceRector::class;
    }
}
