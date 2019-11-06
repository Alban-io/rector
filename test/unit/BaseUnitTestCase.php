<?php
declare(strict_types = 1);

namespace TBolier\RethinkQL\UnitTest;

use Mockery;
use PHPUnit\Framework\TestCase;

class BaseUnitTestCase extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    protected function setUp(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);

        parent::setUp();
    }
}
