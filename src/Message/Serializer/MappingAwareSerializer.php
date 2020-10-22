<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle\Message\Serializer;


use DanielKorytek\MessengerBridgeBundle\Exception\UnsupportedMessage;
use DanielKorytek\MessengerBridgeBundle\Message\Envelope\DocplannerEnvelope;
use DanielKorytek\MessengerBridgeBundle\Message\NamedMessageInterface;
use DanielKorytek\MessengerBridgeBundle\Message\Serializer\MappingResolver\MappingResolverInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class MappingAwareSerializer implements MappingAwareSerializerInterface
{
	/** @var MappingResolverInterface */
	private $mappingResolver;
	/** @var SerializerInterface */
	private $serializer;

	public function __construct(MappingResolverInterface $mappingResolver, SerializerInterface $serializer)
	{
		$this->mappingResolver = $mappingResolver;
		$this->serializer      = $serializer;
	}

	public function deserialize(DocplannerEnvelope $docplannerEnvelope): NamedMessageInterface
	{
		$messageClass = $this->mappingResolver->resolveClass($docplannerEnvelope->getType());
		$message      = $this->deserializeMessage($docplannerEnvelope->getPayload(), $messageClass);

		if (false === $message instanceof NamedMessageInterface)
		{
			throw new UnsupportedMessage('Message must be instance of NamedMessageInterface');
		}

		return $message;
	}

	public function serialize(DocplannerEnvelope $docplannerEnvelope): string
	{
		return $this->serializer->serialize($docplannerEnvelope, 'json');
	}

	/** @inheritDoc */
	private function deserializeMessage(string $message, string $messageClass)
	{
		return $this->serializer->deserialize($message, $messageClass, 'json');
	}
}
