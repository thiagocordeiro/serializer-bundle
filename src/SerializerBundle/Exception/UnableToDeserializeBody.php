<?php

declare(strict_types=1);

namespace Serializer\SerializerBundle\Exception;

use Exception;
use Throwable;

class UnableToDeserializeBody extends Exception
{
    public function __construct(Throwable $previous)
    {
        parent::__construct('', 0, $previous);
    }
}
