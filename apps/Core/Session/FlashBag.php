<?php

namespace TBProductColorizerTM\Session;

/**
 * Class FlashBag
 * @package TBProductColorizerTM\Session
 */
class FlashBag
{

    const ERROR = 'error';

    const WARNING = 'warning';

    const INFO = 'info';

    const SUCCESS = 'success';

    /**
     * @var array
     */
    private $messages = [];

    /**
     * FlashBag constructor.
     */
    public function __construct()
    {
        $this->messages = (!isset($_SESSION['triplebits']['flashBag']))? [] : $_SESSION['triplebits']['flashBag'];
    }

    /**
     * FlashBag destructor
     */
    public function __destruct()
    {
        if (!isset($_SESSION['triplebits']))
        {
            $_SESSION['triplebits'] = [];
        }

        $_SESSION['triplebits']['flashBag'] = $this->messages;
    }

    /**
     * @param string $msg
     * @param string $type
     */
    public function add($msg, $type = self::ERROR)
    {
        if (!isset($this->messages[$type]))
        {
            $this->messages[$type] = [];
        }

        $this->messages[$type][] = $msg;
    }

    /**
     * @param string $msg
     */
    public function error($msg)
    {
        $this->add($msg, self::ERROR);
    }

    /**
     * @param string $msg
     */
    public function warning($msg)
    {
        $this->add($msg, self::WARNING);
    }

    /**
     * @param string $msg
     */
    public function info($msg)
    {
        $this->add($msg, self::INFO);
    }

    /**
     * @param string $msg
     */
    public function success($msg)
    {
        $this->add($msg, self::SUCCESS);
    }

    /**
     * @param string|null $type
     * @return array
     */
    public function getMessages($type = null)
    {
        if (null === $type)
        {
            return $this->messages;
        }

        return (isset($this->messages[$type])) ? $this->messages[$type] : [];
    }

    /**
     * @param string $type
     * @return string
     */
    public function prepareOutput($type = self::ERROR)
    {
        $output  = '<div class="notice notice-' . $type . ' is-dismissible">';
        $output .= '<p>{{message}}</p>';
        $output .= '</div>';

        return $output;
    }

    /**
     * @param string|null $type
     */
    public function output($type = null)
    {
        if (null === $type)
        {
            $this->outputAll();
        }
        else
        {
            $this->output($type);
        }
    }

    /**
     * Output all messages
     */
    private function outputAll()
    {
        foreach ($this->getMessages() as $type => $messages)
        {
            $this->outputType($type);
        }
    }

    /**
     * @param string $type
     */
    private function outputType($type = self::ERROR)
    {
        $html       = $this->prepareOutput($type);
        $messages   = $this->getMessages($type);

        foreach ($messages as $message)
        {
            echo str_replace('{{message}}', $messages, $html);
        }
    }

    /**
     * Action to use in admin_notices
     */
    public function adminNotices()
    {
        $this->output();
    }
}