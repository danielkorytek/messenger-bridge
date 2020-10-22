<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle\Message\Serializer\MappingResolver;

use DanielKorytek\MessengerBridgeBundle\Exception\CannotResolveMessageClass;

final class MessageMappingResolver implements MappingResolverInterface
{
	/** @var array */
	private $mapping;

	public function __construct(array $mapping)
	{
		$this->mapping = $mapping;
	}

	public function resolveClass(string $messageType): string
	{
		if (isset($this->mapping[$messageType]))
		{
			return $this->mapping[$messageType];
		}

		throw CannotResolveMessageClass::forMessageType($messageType);
	}

}
