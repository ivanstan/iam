<?php

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class AbstractApiController extends AbstractController
{
    protected SerializerInterface $serializer;
    protected NormalizerInterface $normalizer;
    protected EntityManagerInterface $em;

    /**
     * @required
     *
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    /**
     * @required
     *
     * @param NormalizerInterface $normalizer
     */
    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @required
     *
     * @param EntityManagerInterface $em
     */
    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    protected function response($response): Response
    {
        return new Response(
            $response,
            Response::HTTP_OK,
            [
                'Content-type' => 'application/json',
                'Access-Control-Allow-Origin' => '*',
            ]
        );
    }

    protected function getPayload(): array
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request === null) {
            throw new BadRequestHttpException('Missing request data.');
        }

        try {
            return json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('Unable to parse json data.');
        }
    }

    protected function validate(array $constraints): void
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();

        $validator = Validation::createValidator();

        $violations = $validator->validate(
            $request->request->all(),
            new Assert\Collection($constraints)
        );

        if ($violations->count() > 0) {
            throw new BadRequestHttpException($violations->get(0)->getMessage());
        }
    }
}
