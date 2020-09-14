<?php

declare(strict_types=1);

namespace Mpp\ReferentialBundle\Swagger;

use Mpp\ReferentialBundle\Routing\ReferentialLoader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SwaggerDecorator implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    private NormalizerInterface $decorated;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var array
     */
    private $referentials;

    public function __construct(NormalizerInterface $decorated, UrlGeneratorInterface $router, array $referentials)
    {
        $this->decorated = $decorated;
        $this->router = $router;
        $this->referentials = $referentials;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        $docs['components']['schemas']['Referential'] = [
            'type' => 'array',
            'items' => [
                'type' => 'object',
                'properties' => [
                    'slug' => [
                        'type' => 'string',
                        'readOnly' => true,
                    ],
                    'label' => [
                        'type' => 'string',
                        'readOnly' => true,
                    ],
                    'meta' => [
                        'type' => 'array',
                        'items' => [],
                        'readOnly' => true,
                    ],
                ],
            ],
        ];

        foreach ($this->referentials as $item => $values) {
            $referentialRoute = $this->router->getRouteCollection()->get(ReferentialLoader::buildRouteName($item));
            $path = [
                'paths' => [
                    $referentialRoute->getPath() => [
                        'get' => [
                            'tags' => [sprintf('Referential %s', $item)],
                            'summary' => sprintf('Get %s referential.', $item),
                            'responses' => [
                                Response::HTTP_OK => [
                                    'description' => sprintf('Get %s referential values', $item),
                                    'content' => [
                                        'application/json' => [
                                            'schema' => [
                                                '$ref' => '#/components/schemas/Referential',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $docs = array_merge_recursive($docs, $path);
        }

        return $docs;
    }
}