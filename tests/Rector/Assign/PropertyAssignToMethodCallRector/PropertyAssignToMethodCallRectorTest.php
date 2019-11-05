<?php

declare(strict_types=1);

namespace Rector\Tests\Rector\Assign\PropertyAssignToMethodCallRector;

use Iterator;
use Rector\Rector\Assign\PropertyAssignToMethodCallRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Rector\Tests\Rector\Assign\PropertyAssignToMethodCallRector\Source\ChoiceControl;
use Rector\Tests\Rector\Assign\PropertyAssignToMethodCallRector\Source\MultiChoiceControl;

final class PropertyAssignToMethodCallRectorTest extends AbstractRectorTestCase
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

    /**
     * @return mixed[]
     */
    protected function getRectorsWithConfiguration(): array
    {
        return [
            PropertyAssignToMethodCallRector::class => [
                '$oldPropertiesToNewMethodCallsByType' => [
                    ChoiceControl::class => [
                        'checkAllowedValues' => 'checkDefaultValue',
                    ],
                    MultiChoiceControl::class => [
                        'checkAllowedValues' => 'checkDefaultValue',
                    ],
                ],
            ],
        ];
    }
}
