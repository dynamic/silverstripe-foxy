<?php

namespace Dynamic\Foxy\Test\Page;

use Dynamic\Foxy\Page\Product;
use SilverStripe\Dev\SapphireTest;

class ProductTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'product_test.yml';

    /**
     * @return void
     */
    public function testGetIsAvailable()
    {
        /** @var Product $product */
        $product = $this->objFromFixture(Product::class, 'productone');

        $this->assertTrue($product->getIsAvailable());
    }
}
