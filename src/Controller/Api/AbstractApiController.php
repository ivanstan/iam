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

    public function response($response): Response
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
}
