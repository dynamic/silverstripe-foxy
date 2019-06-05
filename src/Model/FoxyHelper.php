<?php

namespace Dynamic\Foxy\Model;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;

class FoxyHelper extends \FoxyCart_Helper
{
    use Configurable;
    use Injectable;
    use Extensible;

    /**
     * @var
     */
    private static $secret;

    /**
     * @var
     */
    protected static $cart_url;

    /**
     * @var
     */
    private static $custom_ssl;

    /**
     * @var
     */
    private static $max_quantity = 10;

    /**
     * @var
     */
    private $_secret;

    /**
     * @var
     */
    private $_cart_url;

    /**
     * @var
     */
    private $_custom_ssl;

    /**
     * @var
     */
    private $_max_quantity;

    /**
     * @return mixed
     */
    public function getStoreSecret()
    {
        if (!$this->_secret) {
            $this->setStoreSecret();
        }

        return $this->_secret;
    }

    /**
     * @return $this
     */
    public function setStoreSecret()
    {
        $this->_secret = $this->config()->get('secret');

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStoreCartURL()
    {
        if (!$this->_cart_url) {
            $this->setStoreCartURL();
        }

        return $this->_cart_url;
    }

    /**
     * @return $this
     */
    public function setStoreCartURL()
    {
        $this->_cart_url = $this->config()->get('cart_url');

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomSSL()
    {
        if (!$this->_custom_ssl) {
            $this->setCustomSSL();
        }

        return $this->_custom_ssl;
    }

    /**
     * @return $this
     */
    public function setCustomSSL()
    {
        $this->_custom_ssl = $this->config()->get('custom_ssl');

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxQuantity()
    {
        if (!$this->_max_quantity) {
            $this->setMaxQuantity();
        }

        return $this->_max_quantity;
    }

    /**
     * @return $this
     */
    public function setMaxQuantity()
    {
        $this->_max_quantity = $this->config()->get('max_quantity');

        return $this;
    }

    /**
     * FoxyHelper constructor.
     *
     * Set the private statics $secret and $cart_url in FoxyCart_Helper
     *
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function __construct()
    {
        self::setCartURL($this->getStoreCartURL());
        self::setSecret($this->getStoreSecret());
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
