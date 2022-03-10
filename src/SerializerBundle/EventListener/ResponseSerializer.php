<?php

declare(strict_types=1);

namespace Serializer\SerializerBundle\EventListener;

use Serializer\Exception\SerializerException;
use Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class ResponseSerializer
{
    private Serializer $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @throws SerializerException
     */
    public function __invoke(ViewEvent $event): void
    {
        $object = $event->getControllerResult();

        if (false === is_object($object)) {
            return;
        }

        $content = $this->serializer->serialize($object);
        assert(is_string($content));

        $response = new Response($content, Response::HTTP_OK, ['Content-Type' => 'application/json']);

        $event->setResponse($response);
    }
}
