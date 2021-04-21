<?php

declare(strict_types=1);

namespace Serializer\SerializerBundle\DependencyInjection;

use Serializer\ArraySerializer;
use Serializer\Exception\MissingOrInvalidProperty;
use Serializer\Exception\SerializerException;
use Serializer\JsonSerializer;
use Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class HttpValueObjectFactory
{
    private JsonSerializer $jsonSerializer;
    private ArraySerializer $arraySerializer;
    private RequestStack $requestStack;

    public function __construct(
        JsonSerializer $jsonSerializer,
        ArraySerializer $arraySerializer,
        RequestStack $requestStack
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->arraySerializer = $arraySerializer;
        $this->requestStack = $requestStack;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T|array<T>
     * @throws Throwable
     */
    public function __invoke(string $class): object|array|null
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid Request');
        }

        $serializer = $this->getSerializer($request);
        $data = $this->getData($request);

        try {
            $object = $serializer->deserialize($data, $class);
        } catch (MissingOrInvalidProperty $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage(), $e);
        } catch (SerializerException $e) {
            /**
             * We don't want to suppress serializer exception as bad request
             * since it is thrown when creating parser mappers.
             * In other words, the class was not properly mapped
             */
            throw $e;
        } catch (Throwable $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Bad Request', $e);
        }

        return $object;
    }

    private function getSerializer(Request $request): Serializer
    {
        return match ($request->getContentType()) {
            'json' => $this->jsonSerializer,
            default => $this->arraySerializer,
        };
    }

    /**
     * @return array<mixed>
     */
    private function getData(Request $request): string|array
    {
        return match ($request->getContentType()) {
            'json' => (string) $request->getContent(),
            default => array_merge(
                $request->query->all(),
                $request->request->all(),
            ),
        };
    }
}
