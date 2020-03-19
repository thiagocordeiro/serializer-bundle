<?php

declare(strict_types=1);

namespace Serializer\SerializerBundle\DependencyInjection;

use Serializer\Exception\MissingOrInvalidProperty;
use Serializer\Serializer;
use Serializer\SerializerBundle\Exception\UnableToDeserializeBody;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class HttpValueObjectFactory
{
    /** @var Serializer */
    private $serializer;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(Serializer $serializer, RequestStack $requestStack)
    {
        $this->serializer = $serializer;
        $this->requestStack = $requestStack;
    }

    public function __invoke(string $class)
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid Body');
        }

        $data = $request->getContent();

        try {
            $object = $this->serializer->deserialize($data, $class);
        } catch (MissingOrInvalidProperty $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage(), $e);
        } catch (Throwable $e) {
            throw new UnableToDeserializeBody($e);
        }

        return $object;
    }
}
