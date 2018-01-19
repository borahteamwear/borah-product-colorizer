<?php

namespace TBProductColorizerTM\Utils;

/**
 * Class Loader
 * @package TBProductColorizerTM\Utils
 */
final class Loader
{

    /**
     * @var array
     */
    private $actions = [];

    /**
     * @var array
     */
    private $filters = [];

    /**
     * @param string $hook
     * @param object $component
     * @param string $callback
     * @param int $priority
     * @param int $acceptedArgs
     */
    public function addAction($hook, $component, $callback, $priority = 10, $acceptedArgs = 0)
    {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $acceptedArgs);
    }

    /**
     * @param string $hook
     * @param object $component
     * @param string $callback
     * @param int $priority
     * @param int $acceptedArgs
     */
    public function addFilter($hook, $component, $callback, $priority = 10, $acceptedArgs = 0)
    {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $acceptedArgs);
    }

    /**
     * @param array $hooks
     * @param string $hook
     * @param object $component
     * @param string $callback
     * @param int $priority
     * @param int $acceptedArgs
     * @return array
     */
    private function add($hooks, $hook, $component, $callback, $priority = 10, $acceptedArgs = 1)
    {
        $hooks[] = [
            "hook"          => $hook,
            "component"     => $component,
            "callback"      => $callback,
            "priority"      => $priority,
            "acceptedArgs"  => $acceptedArgs
        ];

        return $hooks;
    }

    /**
     * @param bool $emptyAfterRun
     */
    public function run($emptyAfterRun = true)
    {
        // Filters
        foreach ($this->filters as $key => $hook)
        {
            add_filter(
                $hook['hook'], [$hook['component'], $hook['callback']], $hook['priority'], $hook['acceptedArgs']
            );

            if (true === $emptyAfterRun)
            {
                unset($this->filters[$key]);
            }
        }

        // Actions
        foreach ($this->actions as $key => $hook)
        {
            add_action(
                $hook['hook'], [$hook['component'], $hook['callback']], $hook['priority'], $hook['acceptedArgs']
            );

            if (true === $emptyAfterRun)
            {
                unset($this->actions[$key]);
            }
        }
    }
}