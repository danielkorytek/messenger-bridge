<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle\Message\Serializer;

use DanielKorytek\MessengerBridgeBundle\Message\Envelope\DocplannerEnvelope;
use DanielKorytek\MessengerBridgeBundle\Message\NamedMessageInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

final class DocplannerEnvelopeSerializer implements SerializerInterface
{
	private const STAMP_HEADER_PREFIX = 'X-Message-Stamp-';

	/**
	 * @var MappingAwareSerializer
	 */
	private $mappingAwareSerializer;
	/**
	 * @var Serializer
	 */
	private $serializer;

	public function __construct(MappingAwareSerializerInterface $mappingAwareSerializer, SymfonySerializerInterface $serializer)
	{
		$this->serializer             = $serializer;
		$this->mappingAwareSerializer = $mappingAwareSerializer;
	}

	public function decode(array $encodedEnvelope): Envelope
	{
		if (empty($encodedEnvelope['body']))
		{
			throw new MessageDecodingFailedException('Encoded envelope should have at least a "body" and some "headers".');
		}

		$body   = $encodedEnvelope['body'];
		$stamps = $this->decodeStamps($encodedEnvelope);

        try {
            /** @var DocplannerEnvelope $docplannerEnvelope */
            $docplannerEnvelope = $this->serializer->deserialize($body, DocplannerEnvelope::class, 'json');
            $message            = $this->mappingAwareSerializer->deserialize($docplannerEnvelope);
        } catch (UnexpectedValueException $e) {
            throw new MessageDecodingFailedException(sprintf('Could not decode message: %s.', $e->getMessage()), $e->getCode(), $e);
        }

		return new Envelope($message, $stamps);
	}

	public function encode(Envelope $envelope): array
	{
		$envelope = $envelope->withoutStampsOfType(NonSendableStampInterface::class);
		$envelope = $envelope->withoutStampsOfType(BusNameStamp::class);

		/** @var NamedMessageInterface $envelopeMessage */
		$envelopeMessage    = $envelope->getMessage();
		$docplannerEnvelope = DocplannerEnvelope::forMessage($envelopeMessage);
		$payload            = $this->serializer->serialize($envelopeMessage, 'json');
		$docplannerEnvelope = $docplannerEnvelope->withPayload($payload);

		return [
			'body'    => $this->serializer->serialize($docplannerEnvelope, 'json'),
			'headers' =>  $this->encodeStamps($envelope) +  $this->getContentTypeHeader()
			];
	}

	private function encodeStamps(Envelope $envelope): array
	{
		if (!$allStamps = $envelope->all())
		{
			return [];
		}

		$headers = [];
		foreach ($allStamps as $class => $stamps)
		{
			$headers[self::STAMP_HEADER_PREFIX . $class] = $this->serializer->serialize($stamps, 'json');
		}

		return $headers;
	}

	private function decodeStamps(array $encodedEnvelope): array
	{
		$stamps = [];
		foreach ($encodedEnvelope['headers'] ?? [] as $name => $value)
		{
			if (0 !== strpos($name, self::STAMP_HEADER_PREFIX))
			{
				continue;
			}

			try
			{
				$stamps[] = $this->serializer->deserialize($value, substr($name, \strlen(self::STAMP_HEADER_PREFIX)) . '[]', 'json');
			}
			catch (UnexpectedValueException $e)
			{
				throw new MessageDecodingFailedException(sprintf('Could not decode stamp: %s.', $e->getMessage()), $e->getCode(), $e);
			}
		}
		if ($stamps)
		{
			$stamps = array_merge(...$stamps);
		}

		return $stamps;
	}

	private function getContentTypeHeader(): array
	{
		return ['Content-Type' => 'application/json'];
	}

}
