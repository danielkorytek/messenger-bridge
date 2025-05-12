<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle\Message\Envelope;

use DanielKorytek\MessengerBridgeBundle\Message\NamedMessageInterface;

final class DocplannerEnvelope
{
	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string|null
	 */
	private $payload;

	public function __construct(string $type, ?string $payload = null)
	{
		$this->type    = $type;
		$this->payload = $payload;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getPayload(): ?string
	{
		return $this->payload;
	}

	public static function forMessage(NamedMessageInterface $namedMessage): self
	{
		return new self($namedMessage->getMessageName());
	}

	public function withPayload(string $payload): self
	{
		return new self($this->type, $payload);
	}

}
