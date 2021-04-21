<?php

declare(strict_types=1);

namespace Test\Serializer\Unit\Serializer\SerializerBundle\DependencyInjection;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Serializer\ArraySerializer;
use Serializer\Exception\MissingOrInvalidProperty;
use Serializer\Exception\SerializerException;
use Serializer\JsonSerializer;
use Serializer\SerializerBundle\DependencyInjection\HttpValueObjectFactory;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Test\Serializer\TestCase;
use TypeError;

class HttpValueObjectFactoryTest extends TestCase
{
    /** @var JsonSerializer&MockObject */
    private JsonSerializer|MockObject $jsonSerializer;

    /** @var ArraySerializer&MockObject */
    private ArraySerializer|MockObject $arraySerializer;

    /** @var RequestStack&MockObject */
    private RequestStack|MockObject $requestStack;

    private HttpValueObjectFactory $factory;

    protected function setUp(): void
    {
        $this->jsonSerializer = $this->createMock(JsonSerializer::class);
        $this->arraySerializer = $this->createMock(ArraySerializer::class);
        $this->requestStack = $this->createMock(RequestStack::class);

        $this->factory = new HttpValueObjectFactory($this->jsonSerializer, $this->arraySerializer, $this->requestStack);
    }

    public function testWhenCurrentRequestIsNullThenThrowAnException(): void
    {
        $this->requestStack->method('getCurrentRequest')->willReturn(null);

        $this->expectExceptionObject(new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid Request'));

        $this->factory->__invoke(stdClass::class);
    }

    public function testWhenContentTypeIsJsonThenUseJsonSerializer(): void
    {
        $request = new Request(content: '{"foo":"bar"}');
        $request->headers->set('content-type', 'application/json');
        $this->requestStack->method('getCurrentRequest')->willReturn($request);

        $this->jsonSerializer
            ->expects($this->once())
            ->method('deserialize')
            ->with('{"foo":"bar"}', stdClass::class);
        $this->arraySerializer
            ->expects($this->never())
            ->method('deserialize');

        $this->factory->__invoke(stdClass::class);
    }

    public function testWhenContentTypeIsNotJsonThenUseArraySerializer(): void
    {
        $request = new Request(query: ['foo' => 'bar'], request: ['biz' => 'baz']);
        $request->headers->set('content-type', 'multipart/form-data');
        $this->requestStack->method('getCurrentRequest')->willReturn($request);

        $this->jsonSerializer
            ->expects($this->never())
            ->method('deserialize');
        $this->arraySerializer
            ->expects($this->once())
            ->method('deserialize')
            ->with(['foo' => 'bar', 'biz' => 'baz']);

        $this->factory->__invoke(stdClass::class);
    }

    public function testWhenSerializerThrowsMissingPropertyExceptionThenThrowBadRequest(): void
    {
        $this->mockJsonRequest('{"foo":"bar"}');
        $error = 'Argument 1 passed to Bar must be of the type string, null given';
        $exception = new MissingOrInvalidProperty(new TypeError($error), ['foo']);
        $this->jsonSerializer->method('deserialize')->willThrowException($exception);

        $this->expectExceptionObject(
            new HttpException(400, 'Parameter "foo" is required', $exception),
        );

        $this->factory->__invoke(stdClass::class);
    }

    public function testWhenSerializerExceptionIsThrownThenByPass(): void
    {
        $this->mockJsonRequest('{"foo":"bar"}');
        $exception = new class () extends SerializerException
        {
            public function __construct()
            {
                parent::__construct('Something went very wrong');
            }
        };
        $this->jsonSerializer->method('deserialize')->willThrowException($exception);

        $this->expectExceptionObject($exception);

        $this->factory->__invoke(stdClass::class);
    }

    public function testWhenSerializerThrowsValueObjectExceptionThenThrowBadRequest(): void
    {
        $this->mockJsonRequest('{"foo":"bar"}');
        $exception = new Exception('something over there is invalid');
        $this->jsonSerializer->method('deserialize')->willThrowException($exception);

        $this->expectExceptionObject(
            new HttpException(400, 'Bad Request', $exception),
        );

        $this->factory->__invoke(stdClass::class);
    }

    public function testReturnDecodedObject(): void
    {
        $this->mockJsonRequest('{"foo":"bar"}');
        $decoded = new stdClass();
        $this->jsonSerializer->method('deserialize')->willReturn($decoded);

        $response = $this->factory->__invoke(stdClass::class);

        $this->assertEquals($decoded, $response);
    }

    private function mockJsonRequest(string $body): void
    {
        $request = new Request(content: $body);
        $request->headers->set('content-type', 'application/json');
        $this->requestStack->method('getCurrentRequest')->willReturn($request);
    }
}
