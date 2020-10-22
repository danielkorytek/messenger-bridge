<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle\Exception;

final class CannotResolveMessageClass extends \RuntimeException
{
	public static function forMessageType(string $messageType): self
	{
		$message = sprintf('Message class not found for given message type: %s', $messageType);

		return new self($message);
	}
}
