<?php

namespace TBProductColorizerTM\DTO;

use TBProductColorizerTM\DTO\Collection\ImagesCollection;

/**
 * Class TemplatesDTO
 * @package TBProductColorizerTM\DTO
 */
class TemplatesDTO extends BasePostMetaDTO
{

    const META_KEY = 'tb_wpc_extended';

    /**
     * @var ImagesCollection
     */
    protected $images;

    /**
     * @return string
     */
    public function getMetaKey()
    {
        return self::META_KEY;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function hydrate(array $data = [])
    {
        if (empty($data))
        {
            $data = $this->getDataFromDatabase();

            if (empty($data))
            {
                return $this;
            }
        }

        if (!isset($data['images']) || !is_array($data['images']) || empty($data['images']))
        {
            return $this;
        }

        $this->setImages(
            (new ImagesCollection)
                ->hydrate($data['images'])
        );

        return $this;
    }

    /**
     * @return ImagesCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param ImagesCollection $images
     *
     * @return $this
     */
    public function setImages(ImagesCollection $images)
    {
        $this->images = $images;

        return $this;
    }
}