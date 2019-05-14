<?php

namespace Dynamic\Foxy\Model;

class FoxyValidation extends \FoxyCart_Helper
{
    public function __construct()
    {
        self::setCartURL(Foxy::getStoreDomain());
        self::setSecret(Foxy::getStoreKey());
    }
}
