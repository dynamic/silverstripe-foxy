<?php

namespace Dynamic\Foxy\Test\TestOnly;

use Dynamic\Foxy\Extension\PurchasableExtension;

class TestShippableProductController extends \PageController
{
    private static $extensions = [
        PurchasableExtension::class,
    ];
}
