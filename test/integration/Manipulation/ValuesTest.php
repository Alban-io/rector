<?php
declare(strict_types=1);

namespace TBolier\RethinkQL\IntegrationTest\Manipulation;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use TBolier\RethinkQL\IntegrationTest\Operation\AbstractTableTest;
use TBolier\RethinkQL\Response\ResponseInterface;

class ValuesTest extends AbstractTableTest
{
    use ArraySubsetAsserts;

    /**
     * @throws \Exception
     */
    public function testValuesResult()
    {
        $this->insertDocumentWithNumber(1, 777);

        /** @var ResponseInterface $cursor */
        $response = $this->r()
            ->table('tabletest')
            ->get(1)
            ->values()
            ->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertCount(4, $response->getData());
        $this->assertArraySubset([2 => 777], $response->getData());
    }
}
