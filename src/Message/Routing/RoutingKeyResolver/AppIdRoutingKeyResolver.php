<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle\Message\Routing\RoutingKeyResolver;


use DanielKorytek\MessengerBridgeBundle\Message\NamedMessageInterface;

final class AppIdRoutingKeyResolver implements RoutingKeyResolverInterface
{
    /** @var string */
    private $appId;

    public function __construct(string $appId)
    {
        $this->appId = $appId;
    }

    public function resolve(NamedMessageInterface $message): string
    {
        return sprintf('%s.%s', $this->appId, $message->getMessageName());
    }

}
