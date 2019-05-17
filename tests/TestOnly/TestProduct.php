<?php

namespace Dynamic\Foxy\Test\TestOnly;

use Dynamic\Foxy\Extension\Purchasable;
use SilverStripe\Dev\TestOnly;

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
}
