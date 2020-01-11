<?php

declare(strict_types=1);

namespace Serializer\SerializerBundle;

use Serializer\JsonSerializer;
use Symfony\Component\HttpFoundation\Request;

class ValueObjectFactory
{
    /** @var JsonSerializer */
    private $serializer;

    public function __construct(JsonSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function __invoke(Request $request, $class)
    {
        $data = $request->getContent();

        return $this->serializer->deserialize($data, $class);
    }
}
