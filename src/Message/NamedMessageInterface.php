<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle\Message;


interface NamedMessageInterface
{
    public function getMessageName(): string;
}
