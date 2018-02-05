<?php
declare(strict_types=1);

namespace TBolier\RethinkQL\Query;

use TBolier\RethinkQL\Response\ResponseInterface;
use TBolier\RethinkQL\RethinkInterface;
use TBolier\RethinkQL\Types\Query\QueryType;
use TBolier\RethinkQL\Types\Term\TermType;

class Database implements DatabaseInterface
{
    /**
     * @var MessageInterface
     */
    private $message;

    /**
     * @var RethinkInterface
     */
    private $rethink;

    /**
     * @param RethinkInterface $rethink
     * @param MessageInterface $message
     */
    public function __construct(RethinkInterface $rethink, MessageInterface $message)
    {
        $this->rethink = $rethink;

        $message
            ->setQueryType(QueryType::START)
            ->setQuery(new Query([
                TermType::DB_LIST,
            ]));

        $this->message = $message;
    }

    /**
     * @inheritdoc
     */
    public function dbCreate(string $name): DatabaseInterface
    {
        $this->message
            ->setQueryType(QueryType::START)
            ->setQuery(new Query([
                TermType::DB_CREATE,
                [
                    [
                        TermType::DATUM,
                        $name,
                    ],
                ],
            ]));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function dbDrop(string $name): DatabaseInterface
    {
        $this->message
            ->setQueryType(QueryType::START)
            ->setQuery(new Query([
                TermType::DB_DROP,
                [
                    [
                        TermType::DATUM,
                        $name,
                    ],
                ],
            ]));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function dbList(): DatabaseInterface
    {
        $this->message
            ->setQueryType(QueryType::START)
            ->setQuery(new Query([
                TermType::DB_LIST,
            ]));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function tableList(): DatabaseInterface
    {
        $this->message
            ->setQueryType(QueryType::START)
            ->setQuery(new Query(
                [
                    TermType::TABLE_LIST,
                ]
            ));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function tableCreate(string $name): DatabaseInterface
    {
        $this->message
            ->setQueryType(QueryType::START)
            ->setQuery(new Query([
                TermType::TABLE_CREATE,
                [
                    [
                        TermType::DATUM,
                        $name,
                    ],
                ],
            ]));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function tableDrop(string $name): DatabaseInterface
    {
        $this->message
            ->setQueryType(QueryType::START)
            ->setQuery(new Query([
                TermType::TABLE_DROP,
                [
                    [
                        TermType::DATUM,
                        $name,
                    ],
                ],
            ]));

        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function run(): ResponseInterface
    {
        return $this->rethink->connection()->run($this->message);
    }
}