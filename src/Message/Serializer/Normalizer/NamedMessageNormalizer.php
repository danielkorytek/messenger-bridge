<?php

declare(strict_types=1);

namespace DanielKorytek\MessengerBridgeBundle\Message\Serializer\Normalizer;

use DanielKorytek\MessengerBridgeBundle\Message\NamedMessageInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class NamedMessageNormalizer implements ContextAwareNormalizerInterface
{
	/** @var ObjectNormalizer */
	private $normalizer;

	public function __construct(ObjectNormalizer $normalizer)
	{
		$this->normalizer = $normalizer;
	}

	public function supportsNormalization($data, string $format = null, array $context = []): bool
	{
		return $data instanceof NamedMessageInterface;
	}

	/** {@inheritDoc} */
	public function normalize($object, string $format = null, array $context = [])
	{
		return $this->normalizer->normalize($object, $format, $context);
	}

}
