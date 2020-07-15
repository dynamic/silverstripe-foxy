<?php

namespace Dynamic\Foxy\Test\TestOnly;

use Dynamic\Foxy\Extension\Purchasable;
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

    private static $has_one = [

    ];

    /**
     * @var array
     */
    private static $extensions = [
        Purchasable::class,
    ];
}
