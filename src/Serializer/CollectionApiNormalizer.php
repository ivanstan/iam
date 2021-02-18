<?php

namespace App\Serializer;

use App\Model\Api\Collection;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CollectionApiNormalizer implements ContextAwareNormalizerInterface
{
    public function __construct(protected ObjectNormalizer $normalizer,)
    {
    }

    /**
     * @param Collection $object
     * @param string|null $format
     * @param array $context
     *
     * @return array|\ArrayObject|bool|float|int|mixed|string|null
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {

        $data = $this->normalizer->normalize($object, $format, $context);

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Collection;
    }
}
