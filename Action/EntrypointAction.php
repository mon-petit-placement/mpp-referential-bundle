<?php

namespace Mpp\ReferentialBundle\Action;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Serializer\Serializer;

class EntrypointAction extends AbstractController
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var array
     */
    private $referentials;

    public function __construct(Serializer $serializer, array $referentials)
    {
        $this->serializer = $serializer;
        $this->referentials = $referentials;
    }

    public function getValues($_referential_item, $_format)
    {
        $mimeTypes = new MimeTypes();

        return new Response(
            $this->serializer->serialize($this->referentials[$_referential_item], $_format),
            Response::HTTP_OK,
            ['content-type' => $mimeTypes->getMimeTypes($_format)]
        );
    }
}