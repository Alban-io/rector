<?php
declare(strict_types = 1);

namespace TBolier\RethinkQL\UnitTest\Connection\Socket;

use Mockery\MockInterface;
use TBolier\RethinkQL\Connection\Socket\Handshake;
use TBolier\RethinkQL\UnitTest\BaseUnitTestCase;
use Psr\Http\Message\StreamInterface;

class HandshakeTest extends BaseUnitTestCase
{
    /**
     * @var Handshake
     */
    private $handshake;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->handshake = new Handshake('foo', 'bar', 42);
    }

    /**
     * @return void
     */
    public function testExceptionThrownOnStreamNotWritable(): void
    {
        $this->expectException('TBolier\RethinkQL\Connection\Socket\Exception');
        $this->expectExceptionMessage('Not connected');
        $stream = \Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('isWritable')->atLeast()->andReturn(false);
        $stream->shouldReceive('close');

        $this->handshake->hello($stream);
    }

    /**
     * @return void
     */
    public function testExceptionThrownOnError(): void
    {
        $this->expectException('TBolier\RethinkQL\Connection\Socket\Exception');
        $this->expectExceptionMessage('Foobar');
        $stream = \Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('isWritable')->atLeast()->andReturn(true);
        $stream->shouldReceive('close');
        $stream->shouldReceive('write');
        $stream->shouldReceive('getContents')->atLeast()->andReturn('ERROR: Foobar');

        $this->handshake->hello($stream);
    }

    /**
     * @return void
     */
    public function testExceptionThrownOnVerifyProtocolWithError(): void
    {
        $this->expectException('TBolier\RethinkQL\Connection\Socket\Exception');
        $this->expectExceptionMessage('Foobar');
        $stream = \Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('isWritable')->atLeast()->andReturn(true);
        $stream->shouldReceive('close');
        $stream->shouldReceive('write');
        $stream->shouldReceive('getContents')->atLeast()->andReturn('{"success":false, "error": "Foobar"}');

        $this->handshake->hello($stream);
    }

    /**
     * @return void
     */
    public function testExceptionThrownOnInvalidProtocolVersion(): void
    {
        $this->expectException('TBolier\RethinkQL\Connection\Socket\Exception');
        $this->expectExceptionMessage('Unsupported protocol version.');
        $stream = \Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('isWritable')->atLeast()->andReturn(true);
        $stream->shouldReceive('close');
        $stream->shouldReceive('write');
        $stream->shouldReceive('getContents')->atLeast()
            ->andReturn('{"success":true, "max_protocol_version": 1, "min_protocol_version": 1}');

        $this->handshake->hello($stream);
    }


    /**
     * @return void
     */
    public function testExceptionThrownOnProtocolError(): void
    {
        $this->expectException('TBolier\RethinkQL\Connection\Socket\Exception');
        $this->expectExceptionMessage('Woops!');
        /** @var MockInterface $stream */
        $stream = \Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('isWritable')->atLeast()->andReturn(true);
        $stream->shouldReceive('close');
        $stream->shouldReceive('write');
        $stream->shouldReceive('getContents')->atLeast()->andReturn('ERROR: Woops!');

        $this->handshake->hello($stream);
    }
}
