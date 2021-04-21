<?php

declare(strict_types=1);

namespace Serializer\SerializerBundle\EventListener;

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

    public function __invoke(ViewEvent $event): void
    {
        $object = $event->getControllerResult();

        if ($object === null) {
            return;
        }

        $content = $this->serializer->serialize($object);
        $response = new Response($content, Response::HTTP_OK, ['Content-Type' => 'application/json']);

        $event->setResponse($response);
    }
}
