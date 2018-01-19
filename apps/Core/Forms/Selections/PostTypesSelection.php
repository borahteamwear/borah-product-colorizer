<?php

namespace TBProductColorizerTM\Forms\Selections;

/**
 * Class PostTypesSelection
 * @package TBProductColorizerTM\Forms\Selections
 */
class PostTypesSelection
{

    /**
     * @var array
     */
    protected $postTypes;

    /**
     * PostTypesSelection constructor.
     */
    public function __construct()
    {
        $this->postTypes = $this->preparePostTypes();
    }

    /**
     * @return array
     */
    private function preparePostTypes()
    {
        $postTypes = get_post_types([], 'objects');

        $data = [];

        foreach ($postTypes as $postType)
        {
            if (true !== $postType->public || true !== $postType->show_ui)
            {
                continue;
            }

            $data[$postType->name] = $postType->label;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getPostTypes()
    {
        return $this->postTypes;
    }

    /**
     * @param array $postTypes
     *
     * @return $this
     */
    public function setPostTypes(array $postTypes)
    {
        $this->postTypes = $postTypes;

        return $this;
    }
}