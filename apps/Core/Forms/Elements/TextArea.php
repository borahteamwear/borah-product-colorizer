<?php

namespace TBProductColorizerTM\Forms\Elements;

use TBProductColorizerTM\Forms\FormElements;

/**
 * Class TextArea
 * @package TBProductColorizerTM\Forms\FormElements
 */
class TextArea extends FormElements
{

    /**
     * @return string
     */
    protected function prepareOutput()
    {
        return sprintf(
            '<textarea id="%s" name="%s" %s>%s</textarea>',
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