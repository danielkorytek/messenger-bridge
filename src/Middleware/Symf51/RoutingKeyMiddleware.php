<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle\Middleware\Symf51;


use DanielKorytek\MessengerBridgeBundle\Exception\UnsupportedMessage;
use DanielKorytek\MessengerBridgeBundle\Message\NamedMessageInterface;
use DanielKorytek\MessengerBridgeBundle\Message\Routing\RoutingKeyResolver\RoutingKeyResolverInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;

final class RoutingKeyMiddleware implements MiddlewareInterface
{
	/** @var RoutingKeyResolverInterface */
	private $routingKeyResolver;

	public function __construct(RoutingKeyResolverInterface $routingKeyResolver)
	{
		$this->routingKeyResolver = $routingKeyResolver;
	}

	public function handle(Envelope $envelope, StackInterface $stack): Envelope
	{
		/** @var NamedMessageInterface $message */
		$message = $envelope->getMessage();

		if (!$message instanceof NamedMessageInterface)
		{
			throw new UnsupportedMessage('Message must be instance of NamedMessageInterface');
		}

		//If messages comes from transport, we're skipping that
		if ($envelope->all(ReceivedStamp::class))
		{
			return $stack->next()->handle($envelope, $stack);
		}

		$routingKey = $this->routingKeyResolver->resolve($message);
		$stamps     = array_merge([new AmqpStamp($routingKey)], ...array_values($envelope->all()));

		return $stack->next()->handle(Envelope::wrap($envelope, $stamps), $stack);
	}

}
