<?php

namespace TBProductColorizerTM\Backend\Modules\Templates;

use TBProductColorizerTM\DI;
use TBProductColorizerTM\DTO\Collection\ImagesCollection;
use TBProductColorizerTM\DTO\ImageDTO;
use TBProductColorizerTM\DTO\TemplatesDTO;
use TBProductColorizerTM\Exception\ViewFileNotFoundException;
use TBProductColorizerTM\ProductColorizer;
use TBProductColorizerTM\Utils\Loader;

/**
 * Class Ajax
 * @package TBProductColorizerTM\Backend\Modules\Templates
 */
class Ajax
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
     * Ajax constructor.
     * @param string $url
     * @param string $path
     * @param DI $di
     */
    public function __construct($url, $path, DI $di)
    {
        $ds         = DIRECTORY_SEPARATOR;

        $path       = $path . 'views' . $ds . 'Templates' . $ds .'metabox' . $ds . 'block' . $ds;

        $this->path = $path;
        $this->url  = $url;
        $this->di   = $di;

        $this->defineHooks();
    }

    /**
     * Filters & Actions
     */
    private function defineHooks()
    {
        /** @var Loader $loader */
        $loader = $this->di->get('loader');

        $loader->addAction('wp_ajax_tb_wpc_images', $this, 'loadImages');
    }

    /**
     * Validate Ajax Request
     */
    private function validate()
    {
        check_ajax_referer(ProductColorizer::SLUG . '-ajax_nonce', 'nonce');
    }

    /**
     * @return string
     */
    public function loadImages()
    {
        $this->validate();

        $id         = (isset($_POST['id'])) ? (int) $_POST['id'] : 0;
        $increment  = (isset($_POST['total'])) ? (int) $_POST['total'] + 1 : 1;

        if (0 >= $id)
        {
            wp_send_json($this->loadImage(null, $increment));

            return false; // just in case
        }

        $metaData = (new TemplatesDTO($id))->hydrate();

        wp_send_json($this->getImagesContent($metaData->getImages()));

        return true; // just in case
    }

    /**
     * @param ImagesCollection|null $images
     *
     * @return string
     */
    private function getImagesContent(ImagesCollection $images = null)
    {
        if (null === $images)
        {
            return $this->loadImage();
        }

        $content = '';

        $i = 1;
        foreach ($images as $image)
        {
            $content .= $this->loadImage($image, $i);

            $i++;
        }

        return $content;
    }

    /**
     * @param ImageDTO|null $image
     * @param int $increment
     *
     * @return string
     * @throws ViewFileNotFoundException
     */
    private function loadImage(ImageDTO $image = null, $increment = 1)
    {
        $file = $this->path . 'svg-template-image.block.php';

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
}