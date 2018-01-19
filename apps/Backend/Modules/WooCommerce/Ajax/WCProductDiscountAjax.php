<?php

namespace TBProductColorizerTM\Backend\Modules\WooCommerce\Ajax;

use TBProductColorizerTM\DI;
use TBProductColorizerTM\DTO\Collection\WCProductColorCollection;
use TBProductColorizerTM\DTO\Collection\WCProductDiscountCollection;
use TBProductColorizerTM\DTO\Collection\WCProductTemplateCollection;
use TBProductColorizerTM\DTO\DiscountDTO;
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
 * Class WCProductDiscountAjax
 * @package TBProductColorizerTM\Backend\Modules\WooCommerce\Ajax
 */
class WCProductDiscountAjax
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

        $loader->addAction('wp_ajax_tb_wpc_discounts', $this, 'loadDiscounts');
    }

    /**
     * Validate Ajax Requests
     */
    private function validate()
    {
        check_ajax_referer(ProductColorizer::SLUG . '-ajax_nonce', 'nonce');
    }

    /**
     * @return string
     */
    public function loadDiscounts()
    {
        $this->validate();

        $id         = (isset($_POST['id'])) ? (int) $_POST['id'] : 0;
        $increment  = (isset($_POST['total'])) ? (int) $_POST['total'] + 1 : 1;

        if (0 >= $id)
        {
            wp_send_json($this->loadDiscount(null, $increment));

            return false; // just in case
        }

        $metaData = (new WCProductDTO($id))->hydrate();

        wp_send_json($this->getDiscountContent($metaData->getDiscounts()));

        return true; // just in case
    }

    /**
     * @param DiscountDTO|null $discountDTO
     * @param int $increment
     *
     * @return string
     * @throws ViewFileNotFoundException
     */
    private function loadDiscount(DiscountDTO $discountDTO = null, $increment = 1)
    {
        $file = $this->path . 'discount.block.php';

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
     * @param WCProductDiscountCollection|null $discounts
     *
     * @return string
     */
    private function getDiscountContent(WCProductDiscountCollection $discounts = null)
    {
        if (null === $discounts)
        {
            return $this->loadDiscount();
        }

        $content = '';

        $i = 1;
        foreach ($discounts as $discount)
        {
            $content .= $this->loadDiscount($discount, $i);

            $i++;
        }

        return $content;
    }
}