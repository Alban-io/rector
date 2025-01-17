<?php

declare(strict_types=1);

namespace Rector\Renaming\Tests\Rector\Class_\RenameClassRector;

use Iterator;
use Rector\CodeQuality\Rector\BooleanAnd\SimplifyEmptyArrayCheckRector;
use Rector\Renaming\Rector\Class_\RenameClassRector;
use Rector\Renaming\Tests\Rector\Class_\RenameClassRector\Source\NewClass;
use Rector\Renaming\Tests\Rector\Class_\RenameClassRector\Source\OldClass;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class AutoImportNamesParameterTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/AutoImportNamesParameter/class_to_new_with_post_import.php.inc'];
        yield [__DIR__ . '/Fixture/AutoImportNamesParameter/partial_expression.php.inc'];
        yield [__DIR__ . '/Fixture/AutoImportNamesParameter/skip_closure_me.php.inc'];
        yield [__DIR__ . '/Fixture/AutoImportNamesParameter/skip_parent_class_same_name.php.inc'];
    }

    protected function getAutoImportNames(): ?bool
    {
        return true;
    }

    /**
     * @return mixed[]
     */
    protected function getRectorsWithConfiguration(): array
    {
        return [
            # this class causes to "partial_expression.php.inc" to fail
            SimplifyEmptyArrayCheckRector::class => [],
            RenameClassRector::class => [
                '$oldToNewClasses' => [
                    OldClass::class => NewClass::class,
                ],
            ],
        ];
    }
}
