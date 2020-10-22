<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle;

use DanielKorytek\MessengerBridgeBundle\DependencyInjection\MessengerBridgeExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class MessengerBridgeBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new MessengerBridgeExtension();
    }
}
