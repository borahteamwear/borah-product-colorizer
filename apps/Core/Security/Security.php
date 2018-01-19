<?php

namespace TBProductColorizerTM\Security;

use TBProductColorizerTM\DTO\BaseOptionsDTO;


/**
 * Class Security
 * @package TBProductColorizerTM\Security
 */
class Security
{

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $sanitized;

    /**
     * @var BaseOptionsDTO
     */
    private $options;

    /**
     * Security constructor.
     * @param BaseOptionsDTO $options
     */
    public function __construct(BaseOptionsDTO $options)
    {
        $this->options  = $options;
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function sanitize($data = [])
    {
        $this->data      = $data;
        $this->sanitized = $this->sanitizeData($data);

        $this->filterSanitizedData();

        return $this->sanitized;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function sanitizeData($data = [])
    {
        $sanitized = [];

        foreach ($data as $key => $value)
        {
            $sanitized[$key] = (is_array($value)) ? $this->sanitizeData($value) : htmlspecialchars($value);
        }

        return $sanitized;
    }

    /**
     * Filter sanitized data
     */
    private function filterSanitizedData()
    {
        $data = $this->options->toArray();

        foreach ($data as $key => $value)
        {
            if (!isset($this->sanitized[$key]))
            {
                $this->sanitized[$key] = null;
            }
        }
    }
}