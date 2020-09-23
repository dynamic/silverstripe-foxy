<?php

namespace Dynamic\Foxy\Test\TestOnly;

use Dynamic\Foxy\Model\Variation;
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

    public static function get_extra_config($class = null, $extensionClass = null, $args = null)
    {
        $config = [];

        // Only add these extensions if the $class is set to DataExtensionTest_Player, to
        // test that the argument works.
        if (strcasecmp($class, Variation::class) === 0) {
            $config['has_one'] = [
                'Product' => TestProduct::class,
            ];
        }

        return $config;
    }
}
