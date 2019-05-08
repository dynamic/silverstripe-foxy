<?php

namespace Dynamic\Foxy\Test\TestOnly;

use Dynamic\Foxy\Extension\Purchasable;
use SilverStripe\Dev\TestOnly;
use SilverStripe\ORM\DataObject;

class TestProduct extends DataObject implements TestOnly
{
    private static $table_name = 'TestProduct';
    
    private static $extensions = [
        Purchasable::class,
    ];
}
