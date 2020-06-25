<?php

namespace Dynamic\Foxy\Test\TestOnly;

use Dynamic\Foxy\Extension\PurchasableExtension;

/**
 * Class TestShippableProductController
 * @package Dynamic\Foxy\Test\TestOnly
 */
class TestShippableProductController extends \PageController
{
    /**
     * @var string[]
     */
    private static $extensions = [
        PurchasableExtension::class,
    ];
}
