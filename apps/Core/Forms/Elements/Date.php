<?php
namespace TBProductColorizerTM\Forms\Elements;

use TBProductColorizerTM\Forms\FormElements;

/**
 * Class Date
 * @package TBProductColorizerTM\Forms\Elements
 */
class Date extends FormElements
{

    /**
     * @return string
     */
    protected function prepareOutput()
    {
        return sprintf(
            '<input id="%s" name="%s" type="date" %s value="%s" />',
            $this->getId(), $this->getFormName(), $this->prepareAttributes(), $this->getDefault()
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