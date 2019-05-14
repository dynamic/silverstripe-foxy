<?php

namespace Dynamic\Foxy\Model;

class Foxy
{
    /**
     * @var string
     */
    private static $keyPrefix = 'dYnm1c';

    /**
     * @param int $length
     * @param int $count
     *
     * @return string
     */
    public static function setStoreKey($length = 54, $count = 0)
    {
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' . strtotime('now');
        $strLength = strlen($charset);
        $str = '';
        while ($count < $length) {
            $str .= $charset[mt_rand(0, $strLength - 1)];
            ++$count;
        }
        return self::getKeyPrefix() . substr(base64_encode($str), 0, $length);
    }

    /**
     * @return mixed|null
     * @throws \SilverStripe\ORM\ValidationException
     */
    public static function getStoreKey()
    {
        $config = Setting::current_foxy_setting();
        if ($config->StoreKey) {
            return $config->StoreKey;
        }
        return false;
    }

    /**
     * @return mixed|null
     * @throws \SilverStripe\ORM\ValidationException
     */
    public static function getStoreDomain()
    {
        $config = Setting::current_foxy_setting();
        if ($config->CustomSSL) {
            if ($config->RemoteDomain) {
                return $config->RemoteDomain;
            }
        } else {
            if ($config->StoreDomain) {
                return $config->StoreDomain;
            }
        }
        return false;
    }

    /**
     * @return null|string
     * @throws \SilverStripe\ORM\ValidationException
     */
    public static function store_name_warning()
    {
        $warning = null;
        if (!self::getStoreDomain()) {
            $warning = 'Must define FoxyCart Store Name or Store Remote Domain in your site settings in the cms';
        }
        return $warning;
    }

    /**
     * @return string
     * @throws \SilverStripe\ORM\ValidationException
     */
    public static function FormActionURL()
    {
        $config = Setting::current_foxy_setting();
        if ($config->CustomSSL) {
            return sprintf('https://%s/cart', self::getStoreDomain());
        } else {
            return sprintf('https://%s.foxycart.com/cart', self::getStoreDomain());
        }
    }

    /**
     * @return string
     */
    public static function getKeyPrefix()
    {
        return self::$keyPrefix;
    }

    /**
     * @param null $productCode
     * @param null $optionName
     * @param null $optionValue
     * @param string $method
     * @param bool $output
     * @param bool $urlEncode
     *
     * @return null|string
     */
    public static function getGeneratedValue(
        $productCode = null,
        $optionName = null,
        $optionValue = null,
        $method = 'name',
        $output = false,
        $urlEncode = false
    ) {
        $optionName = ($optionName !== null) ? preg_replace('/\s/', '_', $optionName) : $optionName;
        $helper = new \FoxyCart_Helper();

        return $helper::fc_hash_value($productCode, $optionName, $optionValue, $method, $output, $urlEncode);
    }
}