<?php

namespace Dynamic\Foxy\Test\TestOnly;

use Dynamic\Foxy\Extension\Shippable;
use SilverStripe\Dev\TestOnly;

class TestShippableProduct extends \Page implements TestOnly
{
    private static $table_name = 'TestShippableProduct';

    private static $extensions = [
        Shippable::class,
    ];
}
