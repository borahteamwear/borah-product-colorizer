<?php

/**
 * Plugin Name: WooCommerce Product Colorizer - TM
 * Plugin URI: https://triplebits.com
 * Description: Create unlimited number of color variations for your products for TM Extra Product Options.
 * Author: Ilgıt Yıldırım
 * Author URI: https://triplebits.com
 * Version: 1.0.0
 * Text Domain: tb-product-colorizer
 * Domain Path: /vars/languages/
 *
 * You should have received a copy of the GNU General Public License
 * along with Product Colorizer. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package tb-product-colorizer-tm
 * @category WooCommerce
 * @author Ilgıt Yıldırım
 */

// No Direct Access
if (!defined("WPINC"))
{
    die;
}

require_once plugin_dir_path(__FILE__) . 'apps/ProductColorizer.php';

$productColorizer = \TBProductColorizerTM\ProductColorizer::getInstance();

if (isset($wpdb))
{
    $productColorizer->getDI()->set('wpdb', $wpdb);
}

if (isset($post))
{
    $productColorizer->getDI()->set('post', $post);
}

if (isset($pagenow))
{
    $productColorizer->getDI()->set('pagenow', $pagenow);
}

// Run Plugin
$productColorizer->run();