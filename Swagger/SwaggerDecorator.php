<?php

declare(strict_types=1);

namespace Mpp\ReferentialBundle\Swagger;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SwaggerDecorator implements NormalizerInterface
{
    private NormalizerInterface $decorated;

    public function __construct(NormalizerInterface $decorated, array $referentials)
    {
        $this->decorated = $decorated;
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
                ],
            ],
        ];

        foreach ($this->referentials as $referential => $values) {
            $path = [
                'paths' => [
                    sprintf('/v1/referential/%s', $referential) => [
                        'get' => [
                            'tags' => [sprintf('Referential %s', $referential)],
                            'summary' => sprintf('Get %s referential.', $referential),
                            'responses' => [
                                Response::HTTP_OK => [
                                    'description' => sprintf('Get %s referential values', $referential),
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