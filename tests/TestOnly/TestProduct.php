<?php

namespace Dynamic\Foxy\Test\TestOnly;

use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Model\Variation;
use SilverStripe\Dev\TestOnly;

/**
 * Class TestProduct
 * @package Dynamic\Foxy\Test\TestOnly
 */
class TestProduct extends \Page implements TestOnly
{
    /**
     * @var string
     */
    private static $table_name = 'TestProduct';

    /**
     * @var array
     */
    private static $extensions = [
        Purchasable::class,
    ];

    /**
     * @var string[]
     */
    private static $has_many = [
        'Variations' => Variation::class,
    ];
}
