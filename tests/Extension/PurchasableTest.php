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
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Member;

/**
 * Class PurchasableTest
 * @package Dynamic\Foxy\Test\Extension
 */
class PurchasableTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = '../fixtures.yml';

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
    public function setUp()
    {
        Config::modify()->set(Variation::class, 'has_one', ['Product' => TestProduct::class]);

        return parent::setUp();
    }

    /**
     *
     */
    public function testUpdateCMSFields()
    {
        $object = Injector::inst()->create(TestProduct::class);
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);

        $object->Price = 10.00;
        $object->Code = 000123;
        $object->FoxyCategoryID = $this->objFromFixture(FoxyCategory::class, 'one')->ID;

        $object->write();
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    /*
    public function testValidate()
    {
        $object = Injector::inst()->create(TestProduct::class);
        $object->Price = '';
        $this->setExpectedException(ValidationException::class);
        $object->write();

        $object->Price = '10.00';
        $object->Code = '';
        $this->setExpectedException(ValidationException::class);
        $object->write();

        $object->Code = '123';
        $object->FoxyCategoryID = '';
        $this->setExpectedException(ValidationException::class);
        $object->write();
    }
    */

    /**
     *
     */
    public function testIsAvailable()
    {
        $object = Injector::inst()->create(TestProduct::class);
        $this->assertTrue($object->isAvailable());

        $object2 = Injector::inst()->create(TestProduct::class);
        $object2->Available = false;
        $this->assertFalse($object2->isAvailable());

        $object3 = Injector::inst()->create(TestProduct::class);
        $object3->Available = 1;
        $type = Injector::inst()->create(TestProduct::class);
        $type->Title = 'Product One';

        $variation1 = Injector::inst()->create(Variation::class);
        $variation1->Title = 'small';
        $variation2 = Injector::inst()->create(Variation::class);
        $variation2->Title = 'large';
        $type->Variations()->add($variation1);
        $type->Variations()->add($variation2);
        $this->assertTrue($object3->isAvailable());
    }

    /**
     *
     */
    public function testIsProduct()
    {
        $object = Injector::inst()->create(TestProduct::class);
        $this->assertTrue($object->isProduct());
    }

    /**
     *
     */
    public function testProvidePermissions()
    {
        /** @var TestProduct $object */
        $object = singleton(Purchasable::class);

        i18n::set_locale('en');
        $expected = [
            'MANAGE_FOXY_PRODUCTS' => [
                'name' => 'Manage products',
                'category' => 'Foxy',
                'help' => 'Manage products and related settings',
                'sort' => 400
            ]
        ];
        $this->assertEquals($expected, $object->providePermissions());
    }

    /**
     *
     */
    public function testCanCreate()
    {
        /** @var TestProduct $object */
        $object = singleton(TestProduct::class);
        /** @var \SilverStripe\Security\Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        /** @var \SilverStripe\Security\Member $siteOwner */
        $siteOwner = $this->objFromFixture(Member::class, 'site-owner');
        /** @var \SilverStripe\Security\Member $default */
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
        $object = singleton(TestProduct::class);
        /** @var \SilverStripe\Security\Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        /** @var \SilverStripe\Security\Member $siteOwner */
        $siteOwner = $this->objFromFixture(Member::class, 'site-owner');
        /** @var \SilverStripe\Security\Member $default */
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
        $object = singleton(TestProduct::class);
        /** @var \SilverStripe\Security\Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        /** @var \SilverStripe\Security\Member $siteOwner */
        $siteOwner = $this->objFromFixture(Member::class, 'site-owner');
        /** @var \SilverStripe\Security\Member $default */
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
        $object = singleton(TestProduct::class);
        /** @var \SilverStripe\Security\Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        /** @var \SilverStripe\Security\Member $siteOwner */
        $siteOwner = $this->objFromFixture(Member::class, 'site-owner');
        /** @var \SilverStripe\Security\Member $default */
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
        $object = singleton(TestProduct::class);
        /** @var \SilverStripe\Security\Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        /** @var \SilverStripe\Security\Member $siteOwner */
        $siteOwner = $this->objFromFixture(Member::class, 'site-owner');
        /** @var \SilverStripe\Security\Member $default */
        $default = $this->objFromFixture(Member::class, 'default');

        $this->assertFalse($object->canArchive($default));
        $this->assertTrue($object->canArchive($admin));
        $this->assertTrue($object->canArchive($siteOwner));
    }
}
