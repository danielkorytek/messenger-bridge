<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle\Message\Serializer;


use DanielKorytek\MessengerBridgeBundle\Message\Envelope\DocplannerEnvelope;
use DanielKorytek\MessengerBridgeBundle\Message\NamedMessageInterface;

interface MappingAwareSerializerInterface
{
    public function deserialize(DocplannerEnvelope $docplannerEnvelope): NamedMessageInterface;

    public function serialize(DocplannerEnvelope $docplannerEnvelope): string;
}
