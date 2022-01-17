<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle;

use DanielKorytek\MessengerBridgeBundle\DependencyInjection\MessengerBridgeExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class MessengerBridgeBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new MessengerBridgeExtension();
    }
}
