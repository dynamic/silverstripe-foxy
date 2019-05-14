<?php

namespace Dynamic\Foxy\Test\TestOnly;

use Dynamic\Foxy\Extension\Shippable;
use Intervention\Image\Image;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\TestOnly;

class TestShippableProduct extends \Page implements TestOnly
{
    private static $table_name = 'TestShippableProduct';

    private static $extensions = [
        Shippable::class,
    ];

    /*
    public function getImage()
    {
        return Injector::inst()->create(Image::class);
    }
    */
}
