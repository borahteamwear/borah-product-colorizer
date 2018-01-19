<?php

namespace TBProductColorizerTM\Backend\Modules\Templates;

use TBProductColorizerTM\DI;
use TBProductColorizerTM\DTO\TemplatesDTO;
use TBProductColorizerTM\ProductColorizer;
use TBProductColorizerTM\Utils\Loader;
use WP_Post;

/**
 * Class MetaBoxes
 * @package TBProductColorizerTM\Backend\Modules\Templates
 */
class MetaBoxes
{

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $path;

    /**
     * @var DI
     */
    private $di;

    /**
     * Filters constructor.
     * @param $url
     * @param $path
     * @param DI $di
     */
    public function __construct($url, $path, DI $di)
    {
        $ds         = DIRECTORY_SEPARATOR;

        $path       = $path . 'views' . $ds . 'Templates' . $ds .'metabox' . $ds;

        $this->url  = $url;
        $this->path = $path;
        $this->di   = $di;

        $this->defineHooks();
    }

    /**
     * Hooks
     */
    public function defineHooks()
    {
        /** @var Loader $loader */
        $loader = $this->di->get('loader');

        $loader->addAction('admin_menu', $this, 'removePublishMetaBox');
        $loader->addAction('admin_menu', $this, 'removeCategoryMetaBox');
        $loader->addAction('admin_init', $this, 'publish');
        $loader->addAction('admin_init', $this, 'svgTemplates');
        $loader->addAction('save_post', $this, 'saveSvgTemplates', 10, 2);
    }

    /**
     * Remove Publish Meta Box
     */
    public function removePublishMetaBox()
    {
        remove_meta_box( 'submitdiv', ProductColorizer::POST_TYPE, 'side');
    }

    /**
     * Remove Category (Color Palettes) Meta Box
     */
    public function removeCategoryMetaBox()
    {
        remove_meta_box( 'tb-pc-svg-colorsdiv', ProductColorizer::POST_TYPE, 'side');
    }

    /**
     * Add Publish Meta Box
     */
    public function publish()
    {
        add_meta_box(
            ProductColorizer::POST_TYPE . '_submitdiv',
            'Save',
            [$this, 'publishContent'],
            ProductColorizer::POST_TYPE,
            'side',
            'high'
        );
    }

    /**
     * @param array $data
     */
    public function publishContent($data)
    {
        require_once($this->path . 'side-publish.php');
    }

    /**
     * SVG Templates
     */
    public function svgTemplates()
    {
        add_meta_box(
            ProductColorizer::POST_TYPE . '_svgTemplates',
            'SVG Templates',
            [$this, 'svgTemplatesContent'],
            ProductColorizer::POST_TYPE,
            'normal',
            'high'
        );
    }

    /**
     * @param WP_Post|null $post
     */
    public function svgTemplatesContent(WP_Post $post = null)
    {
        require_once($this->path . 'normal-svg-templates.php');
    }

    /**
     * @param int $postId
     * @param WP_Post $post
     */
    public function saveSvgTemplates($postId, WP_Post $post)
    {
        if (ProductColorizer::POST_TYPE !== $post->post_type || !isset($_POST['tb_wpc_extended']))
        {
            return;
        }

        $metaData = (new TemplatesDTO($postId))->hydrate($_POST['tb_wpc_extended']);

        $metaData->save();
    }
}