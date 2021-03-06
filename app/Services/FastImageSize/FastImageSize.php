<?php

namespace App\Services\FastImageSize;

use App\Services\FastImageSize\Type\TypeJpeg;

class FastImageSize
{
    protected $size = [];

    protected $data = '';

    protected $context = null;

    protected $supportedTypes = [
        'png'   => ['png'],
        'gif'   => ['gif'],
        'jpeg'  => [
            'jpeg',
            'jpg',
            'jpe',
            'jif',
            'jfif',
            'jfi',
        ],
        'jp2'   => [
            'jp2',
            'j2k',
            'jpf',
            'jpg2',
            'jpx',
            'jpm',
        ],
        'psd'   => [
            'psd',
            'photoshop',
        ],
        'bmp'   => ['bmp'],
        'tif'   => [
            'tif',
            'tiff',
        ],
        'wbmp'  => [
            'wbm',
            'wbmp',
            'vnd.wap.wbmp',
        ],
        'iff'   => [
            'iff',
            'x-iff',
        ],
        'ico'   => [
            'ico',
            'vnd.microsoft.icon',
            'x-icon',
            'icon',
        ],
        'webp'  => [
            'webp',
        ]
    ];

    protected $classMap;

    protected $type;

    /**
     * Get image dimensions of supplied image
     *
     * @param string $file Path to image that should be checked
     * @param string $type Mimetype of image
     * @return array|bool Array with image dimensions if successful, false if not
     */
    public function getImageSize($file, $type = '', $context = null)
    {
        // Reset values
        if ($context) {
            $this->context = $context;
        }
        $this->resetValues();
        // Treat image type as unknown if extension or mime type is unknown
        if (!preg_match('/\.([a-z0-9]+)$/i', $file, $match) && empty($type)) {
            $this->getImagesizeUnknownType($file);
        } else {
            $extension = (empty($type) && isset($match[1])) ? $match[1] : preg_replace('/.+\/([a-z0-9-.]+)$/i', '$1', $type);
            $this->getImageSizeByExtension($file, $extension);
        }

        return sizeof($this->size) > 1 ? $this->size : false;
    }

    /**
     * Get dimensions of image if type is unknown
     *
     * @param string $filename Path to file
     */
    protected function getImagesizeUnknownType($filename)
    {
        // Grab the maximum amount of bytes we might need
        $data = $this->getImage($filename, 0, TypeJpeg::JPEG_MAX_HEADER_SIZE, false);

        if ($data !== false) {
            $this->loadAllTypes();

            foreach ($this->type as $imageType) {
                $imageType->getSize($filename);

                if (sizeof($this->size) > 1) {
                    break;
                }
            }
        }
    }

    /**
     * Get image size by file extension
     *
     * @param string $file Path to image that should be checked
     * @param string $extension Extension/type of image
     */
    protected function getImageSizeByExtension($file, $extension)
    {
        $extension = strtolower($extension);
        $this->loadExtension($extension);

        if (isset($this->classMap[$extension])) {
            $this->classMap[$extension]->getSize($file);
        }
    }

    /**
     * Reset values to default
     */
    protected function resetValues()
    {
        $this->size = [];
        $this->data = '';
    }

    /**
     * Set mime type based on supplied image
     *
     * @param int $type Type of image
     */
    public function setImageType($type)
    {
        $this->size['type'] = $type;
    }

    /**
     * Set size info
     *
     * @param array $size Array containing size info for image
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Get image from specified path/source
     *
     * @param string $filename Path to image
     * @param int $offset Offset at which reading of the image should start
     * @param int $length Maximum length that should be read
     * @param bool $forceLength True if the length needs to be the specified
     *          length, false if not. Default: true
     *
     * @return false|string Image data or false if result was empty
     */
    public function getImage($filename, $offset, $length, $forceLength = true)
    {
        if (empty($this->data)) {
            $this->data = @file_get_contents($filename, null, $this->context, $offset, $length);
        }
        // Force length to expected one. Return false if data length
        // is smaller than expected length
        if ($forceLength === true) {
            return (strlen($this->data) < $length) ? false : substr($this->data, $offset, $length) ;
        }

        return empty($this->data) ? false : $this->data;
    }

    /**
     * Get return data
     *
     * @return array|bool Size array if dimensions could be found, false if not
     */
    protected function getReturnData()
    {
        return sizeof($this->size) > 1 ? $this->size : false;
    }

    /**
     * Load all supported types
     */
    protected function loadAllTypes()
    {
        foreach ($this->supportedTypes as $imageType => $extension) {
            $this->loadType($imageType);
        }
    }

    /**
     * Load an image type by extension
     *
     * @param string $extension Extension of image
     */
    protected function loadExtension($extension)
    {
        if (isset($this->classMap[$extension])) {
            return;
        }

        foreach ($this->supportedTypes as $imageType => $extensions) {
            if (in_array($extension, $extensions, true)) {
                $this->loadType($imageType);
            }
        }
    }

    /**
     * Load an image type
     *
     * @param string $imageType Mimetype
     */
    protected function loadType($imageType)
    {
        if (isset($this->type[$imageType])) {
            return;
        }
        $className              = 'App\Services\FastImageSize\Type\Type' . mb_convert_case(mb_strtolower($imageType), MB_CASE_TITLE);
        $this->type[$imageType] = new $className($this);
        // Create class map
        foreach ($this->supportedTypes[$imageType] as $ext) {
            /** @var Type\TypeInterface */
            $this->classMap[$ext] = $this->type[$imageType];
        }
    }
}
