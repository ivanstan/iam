<?php


namespace App\Controller\Api;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class AbstractApiController extends AbstractController
{
    protected SerializerInterface $serializer;

    /**
     * @required
     *
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
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
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request === null) {
            return [];
        }

        return json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }
}
