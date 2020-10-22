<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle\Message\Routing\RoutingKeyResolver;

use DanielKorytek\MessengerBridgeBundle\Message\NamedMessageInterface;

interface RoutingKeyResolverInterface
{
    public function resolve(NamedMessageInterface $message): string;
}
