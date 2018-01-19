<?php

namespace TBProductColorizerTM\Forms\Elements;

use TBProductColorizerTM\Forms\FormElementsWithOptions;

/**
 * Class Select
 * @package TBProductColorizerTM\Forms\Elements
 */
class Select extends FormElementsWithOptions
{

    /**
     * @return string
     */
    protected function prepareOutput()
    {
        $name = $this->getFormName();

        if (isset($this->getAttributes()['multiple']))
        {
            $name .= '[]';
        }

        $output = sprintf(
            '<select id="%s" name="%s" %s>',
            $this->getId(), $name, $this->prepareAttributes()
        );

            foreach ($this->getOptions() as $id => $value)
            {
                $selected = ($this->isSelected($id)) ? ' selected=""' : '';

                $output .= sprintf(
                    '<option value="%s"%s>%s</option>',
                    $id, $selected, $value
                );
            }

        $output.= "</select>";

        return $output;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function isSelected($value)
    {
        $default = $this->getDefault();

        return (
            $default &&
            (
                (is_string($default) && $default === $value) ||
                (is_array($default) && in_array($value, $default))
            )
        );
    }

    /**
     * @return string
     */
    public function render()
    {
        return ($this->renderFile) ? @file_get_contents($this->renderFile) : $this->prepareOutput();
    }
}