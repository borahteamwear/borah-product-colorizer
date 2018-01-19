<?php

namespace TBProductColorizerTM\Backend\Modules\WooCommerce\Ajax;

use TBProductColorizerTM\DI;
use TBProductColorizerTM\DTO\Collection\WCProductColorCollection;
use TBProductColorizerTM\DTO\Collection\WCProductTemplateCollection;
use TBProductColorizerTM\DTO\WCProductColorDTO;
use TBProductColorizerTM\DTO\WCProductDTO;
use TBProductColorizerTM\DTO\WCProductTemplateDTO;
use TBProductColorizerTM\Exception\ViewFileNotFoundException;
use TBProductColorizerTM\ProductColorizer;
use TBProductColorizerTM\Utils\Loader;
use WP_Post;
use WP_Query;
use WP_Term;

/**
 * Class WCProductAjax
 * @package TBProductColorizerTM\Backend\Modules\WooCommerce\Ajax
 */
class WCProductAjax
{

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $url;

    /**
     * @var DI
     */
    private $di;

    /**
     * WCProductAjax constructor.
     * @param string $url
     * @param string $path
     * @param DI $di
     */
    public function __construct($url, $path, DI $di)
    {
        $ds         = DIRECTORY_SEPARATOR;
        $this->path = $path . 'block' . $ds;
        $this->url  = $url;
        $this->di   = $di;

        $this->defineHooks();
    }

    /**
     * Actions & Filters
     */
    private function defineHooks()
    {
        /** @var Loader $loader */
        $loader = $this->di->get('loader');

        $loader->addAction('wp_ajax_tb_wpc_save', $this, 'save');
        $loader->addAction('wp_ajax_tb_wpc_templates', $this, 'loadTemplates');
        $loader->addAction('wp_ajax_tb_wpc_colors', $this, 'loadColors');
    }

    /**
     * Validate Ajax Requests
     */
    private function validate()
    {
        check_ajax_referer(ProductColorizer::SLUG . '-ajax_nonce', 'nonce');
    }

    /**
     * Save Product Colorizer
     */
    public function save()
    {
        $this->validate();

        $id     = (int) $_POST['id'];

        parse_str($_POST['data'], $data);

        if (!$this->isDataValid($data))
        {
            wp_send_json(false);
            return;
        }

        $data = $data['tb_wpc_data'];

        $metaData = (new WCProductDTO($id))
            ->hydrate($data)
        ;

        wp_send_json($metaData->save());
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    private function isDataValid(array $data)
    {
        return (isset($data['tb_wpc_data']) && is_array($data['tb_wpc_data']) && !empty($data['tb_wpc_data']));
    }

    /**
     * @return string
     */
    public function loadTemplates()
    {
        $this->validate();

        $id         = (isset($_POST['id'])) ? (int) $_POST['id'] : 0;
        $increment  = (isset($_POST['total'])) ? (int) $_POST['total'] + 1 : 1;

        if (0 >= $id)
        {
            wp_send_json($this->loadTemplate(null, $increment));

            return false; // just in case
        }

        $metaData = (new WCProductDTO($id))->hydrate();

        wp_send_json($this->getImagesContent($metaData->getTemplates()));

        return true; // just in case
    }

    /**
     * @param WCProductTemplateCollection|null $templates
     *
     * @return string
     */
    private function getImagesContent(WCProductTemplateCollection $templates = null)
    {
        if (null === $templates)
        {
            return $this->loadTemplate();
        }

        $content = '';

        $i = 1;
        foreach ($templates as $template)
        {
            $content .= $this->loadTemplate($template, $i);

            $i++;
        }

        return $content;
    }

    /**
     * @param WCProductTemplateDTO|null $template
     * @param int $increment
     *
     * @return string
     * @throws ViewFileNotFoundException
     */
    private function loadTemplate(WCProductTemplateDTO $template = null, $increment = 1)
    {
        $file = $this->path . 'product-template.block.php';

        if (!is_file($file))
        {
            throw new ViewFileNotFoundException($file);
        }

        if (1 > (int) $increment)
        {
            $increment = 1;
        }

        ob_start();

        require $file;

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

    /**
     * @return array
     */
    private function getTemplates()
    {
        $query = new WP_Query([
            'posts_fields'      => 'ID, post_title',
            'posts_per_page'    => -1,
            'post_type'         => ProductColorizer::POST_TYPE,
            'post_status'       => 'publish',
        ]);

        if (!$query->have_posts())
        {
            return [];
        }

        $posts = ['' => 'Select a Template'];

        /** @var WP_Post $post */
        foreach ($query->get_posts() as $post)
        {
            $posts[(int) $post->ID] = $post->post_title;
        }

        wp_reset_postdata();

        return $posts;
    }

    /**
     * @return string
     */
    public function loadColors()
    {
        $this->validate();

        $id         = (isset($_POST['id'])) ? (int) $_POST['id'] : 0;
        $increment  = (isset($_POST['total'])) ? (int) $_POST['total'] + 1 : 1;

        if (0 >= $id)
        {
            wp_send_json($this->loadColor(null, $increment));

            return false; // just in case
        }

        $metaData = (new WCProductDTO($id))->hydrate();

        wp_send_json($this->getColorsContent($metaData->getColors()));

        return true; // just in case
    }

    /**
     * @param WCProductColorCollection|null $colors
     *
     * @return string
     */
    private function getColorsContent(WCProductColorCollection $colors = null)
    {
        if (null === $colors)
        {
            return $this->loadColor();
        }

        $content = '';

        $i = 1;
        foreach ($colors as $color)
        {
            $content .= $this->loadColor($color, $i);

            $i++;
        }

        return $content;
    }

    /**
     * @param WCProductColorDTO|null $color
     * @param int $increment
     *
     * @return string
     * @throws ViewFileNotFoundException
     */
    private function loadColor(WCProductColorDTO $color = null, $increment = 1)
    {
        $file = $this->path . 'product-color.block.php';

        if (!is_file($file))
        {
            throw new ViewFileNotFoundException($file);
        }

        if (1 > (int) $increment)
        {
            $increment = 1;
        }

        ob_start();

        require $file;

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

    /**
     * @return array
     */
    private function getColors()
    {
        $terms = get_terms([
            'hide_empty'    => false,
            'taxonomy'      => ProductColorizer::TAXONOMY_COLORS,
            'parent'        => 0,
        ]);

        $colors = ['' => 'Select a Palette'];

        if (!is_array($terms) || empty($terms))
        {
            return $colors;
        }

        /** @var WP_Term $term */
        foreach ($terms as $term)
        {
            $colors[(int) $term->term_id] = $term->name;
        }

        return $colors;
    }
}