<?php

namespace Dynamic\Foxy\Test\TestOnly;

use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Extension\Shippable;
use Intervention\Image\Image;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\TestOnly;

/**
 * Class TestShippableProduct
 * @package Dynamic\Foxy\Test\TestOnly
 */
class TestShippableProduct extends \Page implements TestOnly
{
    /**
     * @var string
     */
    private static $table_name = 'TestShippableProduct';

    /**
     * @var string[]
     */
    private static $extensions = [
        Purchasable::class,
        Shippable::class,
    ];

    /*
    public function getImage()
    {
        return Injector::inst()->create(Image::class);
    }
    */
}
