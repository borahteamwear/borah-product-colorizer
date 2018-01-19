<?php

namespace TBProductColorizerTM;

defined('ABSPATH') or die;

// Ensure autoloader & DI class is included
require_once __DIR__ .  DIRECTORY_SEPARATOR . 'DI.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Utils' . DIRECTORY_SEPARATOR . 'Autoloader.php';

use TBProductColorizerTM\Session\FlashBag;
use TBProductColorizerTM\Utils\Autoloader;
use TBProductColorizerTM\Utils\Loader;
use TBProductColorizerTM\Utils\Cache;
use TBProductColorizerTM\Utils\Logger;

/**
 * Class Application
 * @description This class should only be extended by main plugin class
 * @package TBProductColorizerTM
 */
abstract class Application
{

    /**
     * @var DI
     */
    protected $di;

    /**
     * @var Application
     */
    private static $instance;

    /**
     * @return string
     */
    abstract protected function getSlug();

    /**
     * @return void
     */
    abstract protected function pluginDependencies();

    /**
     * Application constructor.
     */
    private function __construct()
    {
        if ($this->shouldStopExecution())
        {
            return false;
        }

        // Set Application
        $this->setApplication();

        if (!method_exists($this, 'onActivation'))
        {
            return;
        }

        $file = $this->getPluginDirectory() . $this->getSlug() . '.php';

        // Activation Hook
        register_activation_hook($file, [$this, 'onActivation']);
    }

    /**
     * Check whether to execute script or not
     * @return bool
     */
    private function shouldStopExecution()
    {
        return (defined('static::ADMIN_ONLY') && true === static::ADMIN_ONLY);
    }

    /**
     * Prevent cloning
     * @return void
     */
    private function __clone(){}

    /**
     * Prevent unserialization
     * @return void
     */
    private function __wakeup(){}

    /**
     * Get Instance
     * @return Application
     */
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Set Application
     */
    private function setApplication()
    {
        $this->di = new DI;

        $this->registerNamespaces();
        $this->loadLanguages();
        $this->loadDependencies();
    }

    /**
     * @return string
     */
    protected function getPluginDirectory()
    {
        return WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->getSlug() . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getVarDirectory()
    {
        return $this->getPluginDirectory() . 'var' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getLogDirectory()
    {
        return $this->getVarDirectory() . 'log' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getCacheDirectory()
    {
        return $this->getVarDirectory() . 'cache' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getLanguagesDirectory()
    {
        return $this->getVarDirectory() . 'languages' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return DI
     */
    public function getDI()
    {
        return $this->di;
    }

    /**
     * Register namespaces that will be used by the plugin
     */
    public function registerNamespaces()
    {
        $autoloader = new Autoloader();
        $this->di->set('autoloader', $autoloader);

        // Base directory
        $dir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->getSlug() . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR;

        // Autoloader
        $autoloader->registerNamespaces([
            __NAMESPACE__ => [
                $dir,
                $dir . 'Core' . DIRECTORY_SEPARATOR
            ]
        ]);

        // Register namespaces
        $autoloader->register();
    }

    /**
     * Load Dependencies
     */
    private function loadDependencies()
    {
        $di = $this->getDI();

        // Set loader
        $di->set('loader', new Loader);

        // Set cache
        $di->set('cache', new Cache($this->getCacheDirectory()));

        // Set logger
        $di->set('logger', new Logger($this->getLogDirectory()));

        // Set flash bag
        $di->set('flashBag', new FlashBag);

        $this->pluginDependencies();
    }

    /**
     * Load language file
     */
    public function loadLanguages()
    {
        $languagesDirectory = $this->getLanguagesDirectory();

        // Set filter for plugins languages directory
        $languagesDirectory = apply_filters($this->getSlug() . '_languages_directory', $languagesDirectory);

        // Traditional WP plugin locale filter
        $locale             = apply_filters('plugin_locale', get_locale(), $this->getSlug());
        $moFile             = sprintf('%1$s-%2$s.mo', $this->getSlug(), $locale);

        // Setup paths to current locale file
        $moFileLocal        = $languagesDirectory . $moFile;
        $moFileGlobal       = WP_LANG_DIR . DIRECTORY_SEPARATOR . $this->getSlug() . DIRECTORY_SEPARATOR . $moFile;

        // Global file (/wp-content/languages/WPSTG)
        if (file_exists($moFileGlobal))
        {
            load_textdomain($this->getSlug(), $moFileGlobal);
        }
        // Local file (/wp-content/plugins/wp-staging/languages/)
        elseif (file_exists($moFileLocal))
        {
            load_textdomain($this->getSlug(), $moFileGlobal);
        }
        // Default file
        else
        {
            load_plugin_textdomain($this->getSlug(), false, $languagesDirectory);
        }
    }

    /**
     * Execute Plugin
     */
    public function run()
    {
        if ($this->shouldStopExecution())
        {
            return false;
        }

        $this->getDI()->get('loader')->run();
    }
}