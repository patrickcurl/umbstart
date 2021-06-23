<?php

namespace App\Services\FastImageSize\Type;

use App\Services\FastImageSize\FastImageSize;

abstract class TypeBase implements TypeInterface
{
    /** @var FastImageSize */
    protected $fastImageSize;

    /**
     * Base constructor for image types
     *
     * @param FastImageSize $fastImageSize
     */
    public function __construct(FastImageSize $fastImageSize)
    {
        $this->fastImageSize = $fastImageSize;
    }
}
