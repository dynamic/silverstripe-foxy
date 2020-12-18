<?php

namespace Dynamic\Foxy;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;

/**
 * Class ProductExpirationHelper
 * @package Dynamic\Foxy
 */
class ProductExpirationHelper
{
    use Configurable;
    use Injectable;
    use Extensible;

    /**
     * @var
     */
    private $expirations;

    /**
     * ProductExpirationHelper constructor.
     */
    public function __construct()
    {
        if (($expirations = get_defined_vars()) && count($expirations)) {
            foreach ($expirations as $expiration) {
                $this->addExpiration($expiration);
            }
        }
    }

    /**
     * @param $expiration
     * @return $this
     */
    public function addExpiration($expiration)
    {
        $this->expirations[] = $expiration;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getExpiration()
    {
        $finalExpiration = null;

        foreach ($this->expirations as $expiration) {
            if ($finalExpiration === null) {
                $finalExpiration = $expiration;
            }

            if ($expiration < $finalExpiration) {
                $finalExpiration = $expiration;
            }
        }

        return $finalExpiration;
    }
}
