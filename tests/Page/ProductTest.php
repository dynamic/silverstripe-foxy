<?php

namespace Dynamic\Foxy\Test\Page;

use Dynamic\Foxy\Page\Product;
use Dynamic\Foxy\Model\FoxyCategory;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;

/**
 * Class ProductTest
 * @package Dynamic\Foxy\Test\Page
 */
class ProductTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = '../fixtures.yml';

    /**
     * Test that an available product returns true for getIsAvailable()
     */
    public function testGetIsAvailable(): void
    {
        $category = $this->objFromFixture(FoxyCategory::class, 'default');

        $product = Product::create();
        $product->Title = 'Test Product';
        $product->Price = 19.99;
        $product->Code = 'TEST-AVAIL-001';
        $product->Available = true;
        $product->FoxyCategoryID = $category->ID;
        $product->write();

        $this->assertTrue($product->getIsAvailable());
    }

    /**
     * Test that an unavailable product returns false for getIsAvailable()
     */
    public function testGetIsNotAvailable(): void
    {
        $category = $this->objFromFixture(FoxyCategory::class, 'default');

        $product = Product::create();
        $product->Title = 'Unavailable Product';
        $product->Price = 9.99;
        $product->Code = 'TEST-UNAVAIL-001';
        $product->Available = false;
        $product->FoxyCategoryID = $category->ID;
        $product->write();

        $this->assertFalse($product->getIsAvailable());
    }

    /**
     * Test that getCMSFields returns a FieldList
     */
    public function testGetCMSFields(): void
    {
        $category = $this->objFromFixture(FoxyCategory::class, 'default');

        $product = Product::create();
        $product->Title = 'CMS Fields Test';
        $product->Price = 29.99;
        $product->Code = 'TEST-CMS-001';
        $product->Available = true;
        $product->FoxyCategoryID = $category->ID;
        $product->write();

        $fields = $product->getCMSFields();

        $this->assertInstanceOf(FieldList::class, $fields);
        $this->assertNotNull($fields->dataFieldByName('Price'));
        $this->assertNotNull($fields->dataFieldByName('Code'));
        $this->assertNotNull($fields->dataFieldByName('FoxyCategoryID'));
    }

    /**
     * Test that Code is trimmed on write
     */
    public function testCodeTrimming(): void
    {
        $category = $this->objFromFixture(FoxyCategory::class, 'default');

        $product = Product::create();
        $product->Title = 'Trim Test';
        $product->Price = 19.99;
        $product->Code = '  TRIMMED-CODE  ';
        $product->Available = true;
        $product->FoxyCategoryID = $category->ID;
        $product->write();

        $this->assertEquals('TRIMMED-CODE', $product->Code);
    }

    /**
     * Test that duplicate spaces are removed from Code
     */
    public function testCodeDuplicateSpaceRemoval(): void
    {
        $category = $this->objFromFixture(FoxyCategory::class, 'default');

        $product = Product::create();
        $product->Title = 'Space Test';
        $product->Price = 19.99;
        $product->Code = 'CODE  WITH   SPACES';
        $product->Available = true;
        $product->FoxyCategoryID = $category->ID;
        $product->write();

        $this->assertEquals('CODE WITH SPACES', $product->Code);
    }

    /**
     * Test permissions for product management
     */
    public function testCanCreate(): void
    {
        $product = Product::singleton();

        $admin = $this->objFromFixture(\SilverStripe\Security\Member::class, 'admin');
        $siteOwner = $this->objFromFixture(\SilverStripe\Security\Member::class, 'site-owner');
        $defaultUser = $this->objFromFixture(\SilverStripe\Security\Member::class, 'default');

        $this->assertTrue($product->canCreate($admin));
        $this->assertTrue($product->canCreate($siteOwner));
        $this->assertFalse($product->canCreate($defaultUser));
    }
}
