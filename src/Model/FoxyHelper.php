<?php

namespace Dynamic\Foxy\Model;

use FoxyCart_Helper;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\ArrayList;

/**
 * Class FoxyHelper
 * @package Dynamic\Foxy\Model
 */
class FoxyHelper
{
    use Configurable;
    use Injectable;
    use Extensible;

    /**
     * @var FoxyCart_Helper
     */
    private $cartHelper;

    /**
     * @var string
     */
    private static $secret;

    /**
     * @var string
     */
    protected static $cart_url;

    /**
     * @var bool
     */
    private static $custom_ssl;

    /**
     * @var int
     */
    private static $max_quantity = 10;

    /**
     * @var array
     */
    private static $product_classes = [];

    /**
     * @var bool
     */
    private static $include_product_subclasses = false;

    /**
     * @var string
     */
    private $foxy_secret;

    /**
     * @var string
     */
    private $foxy_cart_url;

    /**
     * @var bool
     */
    private $foxy_custom_ssl;

    /**
     * @var int
     */
    private $foxy_max_quantity;

    /**
     * @var array
     */
    private $foxy_product_classes;

    /**
     * @return mixed
     */
    public function getStoreSecret()
    {
        if (!$this->foxy_secret) {
            $this->setStoreSecret();
        }

        return $this->foxy_secret;
    }

    /**
     * @return $this
     */
    public function setStoreSecret()
    {
        $this->foxy_secret = $this->config()->get('secret');

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStoreCartURL()
    {
        if (!$this->foxy_cart_url) {
            $this->setStoreCartURL();
        }

        return $this->foxy_cart_url;
    }

    /**
     * @return $this
     */
    public function setStoreCartURL()
    {
        $this->foxy_cart_url = $this->config()->get('cart_url');

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomSSL()
    {
        if (!$this->foxy_custom_ssl) {
            $this->setCustomSSL();
        }

        return $this->foxy_custom_ssl;
    }

    /**
     * @return $this
     */
    public function setCustomSSL()
    {
        $this->foxy_custom_ssl = $this->config()->get('custom_ssl');

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxQuantity()
    {
        if (!$this->foxy_max_quantity) {
            $this->setMaxQuantity();
        }

        return $this->foxy_max_quantity;
    }

    /**
     * @return $this
     */
    public function setMaxQuantity()
    {
        $this->foxy_max_quantity = $this->config()->get('max_quantity');

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProuductClasses()
    {
        if (!$this->foxy_product_classes || !is_array($this->foxy_product_classes)) {
            $this->setProductClasses();
        }

        return $this->foxy_product_classes;
    }

    /**
     * @return $this
     */
    public function setProductClasses()
    {
        $productClasses = $this->config()->get('product_classes');

        if (empty($productClasses)) {
            $this->foxy_product_classes = [];

            return $this;
        } elseif (!is_array($productClasses)) {
            $productClasses = [$productClasses];
        }

        if ($this->config()->get('include_product_subclasses')) {
            $productClasses = array_reduce($productClasses, function (array $list, $productClass) {
                foreach (ClassInfo::subclassesFor($productClass) as $key => $class) {
                    $list[$key] = $class;
                }

                return $list;
            }, []);
        }

        $this->foxy_product_classes = $productClasses;

        return $this;
    }

    /**
     * @return ArrayList|\SilverStripe\ORM\DataList
     */
    public function getProducts()
    {
        $productClasses = $this->getProuductClasses();
        $products = ArrayList::create();

        if (!empty($productClasses)) {
            $products = SiteTree::get()->filter('ClassName', array_values($productClasses));
        }

        $this->extend('updateProducts', $products);

        return $products;
    }

    /**
     * FoxyHelper constructor.
     *
     * Set the cart URL and secret in the FoxyCart_Helper
     */
    public function __construct()
    {
        $this->cartHelper = new FoxyCart_Helper();
        FoxyCart_Helper::setCartUrl($this->getStoreCartURL());
        FoxyCart_Helper::setSecret($this->getStoreSecret());
    }

    /**
     * Get the underlying FoxyCart_Helper instance
     *
     * @return FoxyCart_Helper
     */
    public function getCartHelper(): FoxyCart_Helper
    {
        if (!$this->cartHelper) {
            $this->cartHelper = new FoxyCart_Helper();
        }
        return $this->cartHelper;
    }

    /**
     * @return string
     */
    public static function StoreURL()
    {
        $helper = FoxyHelper::create();
        if ($helper->getCustomSSL()) {
            return sprintf('https://%s/', $helper->getStoreCartURL());
        } else {
            return sprintf('https://%s.foxycart.com/', $helper->getStoreCartURL());
        }
    }

    /**
     * @return string
     * @throws \SilverStripe\ORM\ValidationException
     */
    public static function FormActionURL()
    {
        return self::StoreURL() . 'cart';
    }
}
