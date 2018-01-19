<?php

namespace TBProductColorizerTM\Forms\Elements;

use TBProductColorizerTM\Forms\FormElementsWithOptions;

/**
 * Class Check
 * @package TBProductColorizerTM\Forms\Elements
 */
class Check extends FormElementsWithOptions
{

    /**
     * @return string
     */
    protected function prepareOutput()
    {
        $output = '';

        foreach ($this->getOptions() as $id => $value)
        {
            $checked = ($this->isChecked($id)) ? ' checked=""' : '';

            $attributeId = $this->getId() . '_' . $this->getId($id);

            $output .= sprintf(
                '<input type="checkbox" name="%s" id="%s" value="%s" %s/>',
                $this->getId(), $attributeId, $id, $checked
            );

            if ($value)
            {
                $output .= sprintf('<label for="%s">%s</label>', $attributeId, $value);
            }
        }

        return $output;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function isChecked($value)
    {
        $default = $this->getDefault();

        return (
            $default &&
            (
                (is_bool($default) && true === $default) ||
                (is_string($default) && $default === $value) ||
                (is_int($value) && (int) $default == $value) ||
                (is_array($default) && in_array($value, $default))
            )
        );
    }

    /**
     * @return string
     */
    public function render()
    {
        return ($this->getRenderFile()) ? @file_get_contents($this->getRenderFile()) : $this->prepareOutput();
    }
}