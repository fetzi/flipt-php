<?php

use Fetzi\Flipt\EvaluateRequest;
use Fetzi\Flipt\EvaluateResponse;
use Fetzi\Flipt\Flipt;
use Http\Mock\Client;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

beforeEach(function () {
    $this->client   = new Client();
    $requestFactory = Mockery::mock(RequestFactoryInterface::class)->shouldIgnoreMissing();
    $streamFactory  = Mockery::mock(StreamFactoryInterface::class)->shouldIgnoreMissing();

    $request      = Mockery::mock(RequestInterface::class);
    $response     = Mockery::mock(ResponseInterface::class);
    $this->stream = Mockery::mock(StreamInterface::class);

    $requestFactory->shouldReceive('createRequest')->andReturn($request);

    $request->shouldReceive('withHeader')->andReturn($request);
    $request->shouldReceive('withBody')->andReturn($request);

    $response->shouldReceive('getBody')->andReturn($this->stream);

    $this->client->addResponse($response);

    $this->flipt = new Flipt($this->client, $requestFactory, $streamFactory, 'dummy');
});

it('does not match for a disabled flag', function () {
    $this->stream->shouldReceive('getContents')->andReturn(file_get_contents(__DIR__ . '/responses/disabledFlag.json'));

    $response = $this->flipt->evaluate(new EvaluateRequest('foo', 'id', []));

    expect($response)->toBeInstanceOf(EvaluateResponse::class);
    expect($response->hasError())->toBeTrue();
    expect($response->isMatch())->toBeFalse();
});

it('matches a simple feature flag', function () {
    $this->stream->shouldReceive('getContents')->andReturn(file_get_contents(__DIR__ . '/responses/simpleMatched.json'));

    $response = $this->flipt->evaluate(new EvaluateRequest('foo', 'id', []));

    expect($response)->toBeInstanceOf(EvaluateResponse::class);
    expect($response->hasError())->toBeFalse();
    expect($response->isMatch())->toBeTrue();
});

it('matches a simple feature flag 2', function () {
    $this->stream->shouldReceive('getContents')->andReturn(file_get_contents(__DIR__ . '/responses/simpleMatched2.json'));

    $response = $this->flipt->evaluate(new EvaluateRequest('foo', 'id', []));

    expect($response)->toBeInstanceOf(EvaluateResponse::class);
    expect($response->hasError())->toBeFalse();
    expect($response->isMatch())->toBeTrue();
});

it('does not match a simple feature flag', function () {
    $this->stream->shouldReceive('getContents')->andReturn(file_get_contents(__DIR__ . '/responses/simpleNotMatched.json'));

    $response = $this->flipt->evaluate(new EvaluateRequest('foo', 'id', []));

    expect($response)->toBeInstanceOf(EvaluateResponse::class);
    expect($response->hasError())->toBeFalse();
    expect($response->isMatch())->toBeFalse();
});

it('provides the basic request data', function () {
    $this->stream->shouldReceive('getContents')->andReturn(file_get_contents(__DIR__ . '/responses/simpleMatched.json'));

    $response = $this->flipt->evaluate(new EvaluateRequest('foo', 'id', []));

    expect($response)->toBeInstanceOf(EvaluateResponse::class);
    expect($response->getRequestId())->toBe('713b746e-6c90-4fa4-ab04-968b0fb24c64');
    expect($response->getEntityId())->toBe('id');
    expect($response->getContext())->toBe([]);
    expect($response->getFlagKey())->toBe('foo');
    expect($response->getSegmentKey())->toBe('sampleSegment');
});

it('provides a variant', function () {
    $this->stream->shouldReceive('getContents')->andReturn(file_get_contents(__DIR__ . '/responses/variantMatched.json'));

    $response = $this->flipt->evaluate(new EvaluateRequest('foo', 'id', []));

    expect($response)->toBeInstanceOf(EvaluateResponse::class);
    expect($response->isMatch())->toBeTrue();
    expect($response->getValue())->toBe('a');
    expect($response->getVariant())->toBe('a');
});
