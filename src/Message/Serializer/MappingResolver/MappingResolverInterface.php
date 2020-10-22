<?php

declare(strict_types=1);
namespace DanielKorytek\MessengerBridgeBundle\Message\Serializer\MappingResolver;

interface MappingResolverInterface
{
    public function resolveClass(string $messageType): string;
}
