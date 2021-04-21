<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle\Message\Serializer;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

final class BusStampEnvelopeSerializer implements SerializerInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $busName;

    public function __construct(SerializerInterface $serializer, string $busName)
    {
        $this->serializer = $serializer;
        $this->busName = $busName;
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $envelope = $this->serializer->decode($encodedEnvelope);

        $envelope = $envelope->with(new BusNameStamp($this->busName));

        return $envelope;
    }

    public function encode(Envelope $envelope): array
    {
        return $this->serializer->encode($envelope);
    }
}
