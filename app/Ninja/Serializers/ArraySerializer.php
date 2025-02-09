<?php

namespace App\Ninja\Serializers;

use League\Fractal\Serializer\ArraySerializer as FractalArraySerializer;

/**
 * Class ArraySerializer.
 */
class ArraySerializer extends FractalArraySerializer
{
    /**
     * @param string $resourceKey
     *
     */
    public function collection($resourceKey, array $data): array
    {
        return $data;
    }

    /**
     * @param string $resourceKey
     *
     */
    public function item($resourceKey, array $data): array
    {
        return $data;
    }
}
