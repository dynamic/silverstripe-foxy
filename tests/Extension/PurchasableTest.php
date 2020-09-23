<?php

namespace Dynamic\Foxy\Test\Extension;

use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Model\FoxyCategory;
use Dynamic\Foxy\Model\OptionType;
use Dynamic\Foxy\Model\ProductOption;
use Dynamic\Foxy\Model\Setting;
use Dynamic\Foxy\Model\Variation;
use Dynamic\Foxy\Test\TestOnly\TestProduct;
use Dynamic\Foxy\Test\TestOnly\TestVariationDataExtension;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\Debug;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Member;
use SilverStripe\Versioned\Versioned;

/**
 * Class PurchasableTest
 * @package Dynamic\Foxy\Test\Extension
 */
class PurchasableTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = [
        '../fixtures.yml',
        '../purchasableproducts.yml',
    ];

    /**
     * @var array
     */
    protected static $extra_dataobjects = [
        TestProduct::class,
    ];

    /**
     * @var \string[][]
     */
    protected static $required_extensions = [
        TestProduct::class => [
            Purchasable::class,
        ],
        Variation::class => [
            TestVariationDataExtension::class,
        ],
    ];

    /**
     *
     */
    public function testUpdateCMSFields()
    {
        $newProduct = TestProduct::singleton();
        $fields = $newProduct->getCMSFields();

        $this->assertInstanceOf(FieldList::class, $fields);
        $this->assertNull($fields->dataFieldByName('SKU'));

        $existingProduct = $this->objFromFixture(TestProduct::class, 'productone');
        $existingFields = $existingProduct->getCMSFields();

        $this->assertNull($existingFields->dataFieldByName('SKU'));
        $this->assertInstanceOf(GridField::class, $existingFields->dataFieldByName('Variations'));
    }

    /**
     *
     */
    public function testGetIsAvailable()
    {
        /** @var TestProduct $availableProduct */
        $availableProduct = $this->objFromFixture(TestProduct::class, 'productone');
        $this->assertTrue($availableProduct->getIsAvailable());

        /** @var TestProduct $unavailableProduct */
        $unavailableProduct = $this->objFromFixture(TestProduct::class, 'productwo');
        $this->assertFalse($unavailableProduct->getIsAvailable());

        /** @var TestProduct $availableVariations */
        $availableVariations = $this->objFromFixture(TestProduct::class, 'productfour');
        $this->assertTrue($availableVariations->getIsAvailable());

        /** @var TestProduct $unavailableVariations */
        $unavailableVariations = $this->objFromFixture(TestProduct::class, 'productthree');
        $this->assertFalse($unavailableVariations->getIsAvailable());
    }

    /**
     *
     */
    public function testIsProduct()
    {
        /** @var TestProduct $object */
        $object = TestProduct::singleton();

        $this->assertTrue($object->isProduct());
    }

    /**
     *
     */
    public function testProvidePermissions()
    {
        /** @var Purchasable $object */
        $object = singleton(Purchasable::class);

        i18n::set_locale('en');
        $expected = [
            'MANAGE_FOXY_PRODUCTS' => [
                'name' => 'Manage products',
                'category' => 'Foxy',
                'help' => 'Manage products and related settings',
                'sort' => 400,
            ],
        ];
        $this->assertEquals($expected, $object->providePermissions());
    }

    /**
     *
     */
    public function testCanCreate()
    {
        /** @var TestProduct $object */
        $object = TestProduct::singleton();
        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        /** @var Member $siteOwner */
        $siteOwner = $this->objFromFixture(Member::class, 'site-owner');
        /** @var Member $default */
        $default = $this->objFromFixture(Member::class, 'default');

        $this->assertFalse($object->canCreate($default));
        $this->assertTrue($object->canCreate($admin));
        $this->assertTrue($object->canCreate($siteOwner));
    }

    /**
     *
     */
    public function testCanEdit()
    {
        /** @var TestProduct $object */
        $object = TestProduct::singleton();
        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        /** @var Member $siteOwner */
        $siteOwner = $this->objFromFixture(Member::class, 'site-owner');
        /** @var Member $default */
        $default = $this->objFromFixture(Member::class, 'default');

        $this->assertFalse($object->canEdit($default));
        $this->assertTrue($object->canEdit($admin));
        $this->assertTrue($object->canEdit($siteOwner));
    }

    /**
     *
     */
    public function testCanDelete()
    {
        /** @var TestProduct $object */
        $object = TestProduct::singleton();
        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        /** @var Member $siteOwner */
        $siteOwner = $this->objFromFixture(Member::class, 'site-owner');
        /** @var Member $default */
        $default = $this->objFromFixture(Member::class, 'default');

        $this->assertFalse($object->canDelete($default));
        $this->assertTrue($object->canDelete($admin));
        $this->assertTrue($object->canDelete($siteOwner));
    }

    /**
     *
     */
    public function testCanUnpublish()
    {
        /** @var TestProduct $object */
        $object = TestProduct::singleton();
        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        /** @var Member $siteOwner */
        $siteOwner = $this->objFromFixture(Member::class, 'site-owner');
        /** @var Member $default */
        $default = $this->objFromFixture(Member::class, 'default');

        $this->assertFalse($object->canUnpublish($default));
        $this->assertTrue($object->canUnpublish($admin));
        $this->assertTrue($object->canUnpublish($siteOwner));
    }

    /**
     *
     */
    public function testCanArchive()
    {
        /** @var TestProduct $object */
        $object = TestProduct::singleton();
        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        /** @var Member $siteOwner */
        $siteOwner = $this->objFromFixture(Member::class, 'site-owner');
        /** @var Member $default */
        $default = $this->objFromFixture(Member::class, 'default');

        $this->assertFalse($object->canArchive($default));
        $this->assertTrue($object->canArchive($admin));
        $this->assertTrue($object->canArchive($siteOwner));
    }

    /**
     *
     */
    public function testBeforeWrite()
    {
        $product = TestProduct::create();
        $product->Title = 'My New Product';
        $product->Code = ' foo- bar  -baz ';

        $this->assertEquals(' foo- bar  -baz ', $product->Code);
        $product->writeToStage(Versioned::DRAFT);

        $product = TestProduct::get()->byID($product->ID);
        $this->assertEquals('foo- bar -baz', $product->Code);
    }
}
