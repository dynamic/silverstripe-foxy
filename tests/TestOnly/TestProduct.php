<?php

namespace Dynamic\Foxy\Test\TestOnly;

use Dynamic\Foxy\Extension\Purchasable;
use SilverStripe\Dev\TestOnly;

class TestProduct extends \Page implements TestOnly
{
    private static $table_name = 'TestProduct';
    
    private static $extensions = [
        Purchasable::class,
    ];
}
