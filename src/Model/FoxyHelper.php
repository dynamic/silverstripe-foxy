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
     * @param $custom_ssl
     */
    public static function setCustomSSL($custom_ssl)
    {
        self::$custom_ssl = $custom_ssl;
    }

    /**
     * @return mixed
     */
    public static function getCustomSSL()
    {
        return self::$custom_ssl;
    }

    /**
     * FoxyHelper constructor.
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function __construct()
    {
        self::setCartURL($this->config()->get('cart_url'));
        self::setSecret($this->config()->get('secret'));
        self::setCustomSSL($this->config()->get('custom_ssl'));
    }

    /**
     * @return string
     */
    public static function StoreURL()
    {
        if (self::config()->get('custom_ssl')) {
            return sprintf('https://%s/', self::config()->get('cart_url'));
        } else {
            return sprintf('https://%s.foxycart.com/', self::config()->get('cart_url'));
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
