<?php
declare(strict_types=1);

namespace TBolier\RethinkQL\Test\Connection;

use Mockery;
use Mockery\MockInterface;
use TBolier\RethinkQL\Connection\ConnectionInterface;
use TBolier\RethinkQL\Connection\OptionsInterface;
use TBolier\RethinkQL\Test\BaseTestCase;
use TBolier\RethinkQL\Types\Query\QueryType;

class ConnectionTest extends BaseTestCase
{
    /**
     * @var MockInterface
     */
    private $optionsMock;

    public function setUp()
    {
        parent::setUp();

        $this->optionsMock = Mockery::mock(OptionsInterface::class);
    }

    public function testConnect()
    {
        /** @var ConnectionInterface $connection */
        $connection = $this->createConnection('phpunit_default')->connect();

        static::assertInternalType('array', $connection->execute(
            [
                QueryType::START,
                'foo',
            ]
        ));
    }
}
