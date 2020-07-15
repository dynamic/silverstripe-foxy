<?php

namespace Dynamic\Foxy\Test\TestOnly;

use SilverStripe\Dev\TestOnly;
use SilverStripe\ORM\DataExtension;

/**
 * Class TestVariationDataExtension
 * @package Dynamic\Foxy\Test\TestOnly
 */
class TestVariationDataExtension extends DataExtension implements TestOnly
{
    /**
     * @var string[]
     */
    private static $has_one = [
        'Product' => TestProduct::class,
    ];
}
