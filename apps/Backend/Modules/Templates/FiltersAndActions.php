<?php

namespace TBProductColorizerTM\Backend\Modules\Templates;

use TBProductColorizerTM\DI;
use TBProductColorizerTM\ProductColorizer;
use TBProductColorizerTM\Utils\Loader;
use WP_Error;
use WP_Post;

/**
 * Class FiltersAndActions
 * @package TBProductColorizerTM\Backend\Modules\Templates
 */
class FiltersAndActions
{

    const FORCED_STATUS = 'publish';

    const STATUS_TRASH = 'trash';

    /**
     * @var string
     */
    private $url;

    /**
     * @var DI
     */
    private $di;

    /**
     * Filters constructor.
     * @param $url
     * @param DI $di
     */
    public function __construct($url, DI $di)
    {
        $this->url  = $url;
        $this->di   = $di;

        $this->defineFilters();
    }

    /**
     * Filters and actions
     */
    public function defineFilters()
    {
        /** @var Loader $loader */
        $loader = $this->di->get('loader');

        $loader->addFilter('wp_insert_post_data', $this, 'forcePublish', 10, 1);
        $loader->addAction('admin_print_scripts', $this, 'disableAutoSave');
        $loader->addFilter('post_updated_messages', $this, 'postSaveMessages', 10, 1);
        $loader->addFilter('upload_mimes', $this, 'uploadMimeTypes', 10, 1);
        $loader->addFilter('script_loader_src', $this, 'disableAutoDraft', 10, 2);
    }

    /**
     * Force publish to ensure there are no saving as drafts
     * @param array $post
     *
     * @return array
     */
    public function forcePublish($post)
    {
        if (!$this->isForcePublish($post))
        {
            return $post;
        }

        $post['post_status']    = 'publish';

        return $post;
    }

    /**
     * @param array $post
     *
     * @return bool
     */
    private function isForcePublish($post)
    {
        if ($post['post_status'] === self::STATUS_TRASH)
        {
            return false;
        }

        if ($post['post_type'] !== ProductColorizer::POST_TYPE)
        {
            return false;
        }

        return true;
    }

    /**
     * Disable auto-save for templates
     */
    public function disableAutoSave()
    {
        $post = $this->di->get('post');

        if (!$post || $post->post_type === ProductColorizer::POST_TYPE)
        {
            return;
        }

//        wp_deregister_script('autosave');
        wp_dequeue_script('autosave'); // Better for WP 3.1 and above
    }

    /**
     * Template messages
     *
     * @see http://codex.wordpress.org/Function_Reference/register_post_type
     * @param array $messages
     *
     * @return array
     */
    public function postSaveMessages(array $messages)
    {
        /** @var WP_Post $post */
        $post = $this->di->get('post');

        $messages[ProductColorizer::POST_TYPE] = [
            0   => '', // Not used
            1   => __('Template updated.'),
            2   => __('Custom field updated'),
            3   => __('Custom field deleted'),
            4   => __('Template updated'),
            /* translators: %s: date and time of the revision */
            5   => '',
            6   => __('Template created.'),
            7   => __('Template saved.'),
            8   => '',
            9   => '',
            10  => '',
        ];

        return $messages;
    }

    /**
     * Add SVG and PNG mime-types for uploads
     *
     * @param array|null $mimeTypes
     *
     * @return array
     */
    public function uploadMimeTypes(array $mimeTypes = [])
    {
        $mimeTypes['svg'] = 'image/svg+xml';
        $mimeTypes['png'] = 'image/png';

        return $mimeTypes;
    }

    /**
     * @param string $src
     * @param string $handle
     *
     * @return string
     */
    public function disableAutoDraft($src, $handle)
    {
        $post = get_post();

        if ('autosave' !== $handle || ProductColorizer::POST_TYPE !== $post->post_type)
        {
            return $src;
        }

        return '';
    }
}