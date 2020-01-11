<?php

declare(strict_types=1);

namespace Serializer\SerializerBundle\DependencyInjection;

use Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValueObjectFactory
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
            throw new BadRequestHttpException();
        }

        $data = $request->getContent();

        return $this->serializer->deserialize($data, $class);
    }
}
