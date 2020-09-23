<?php

namespace Dynamic\Foxy\Test\Model;

use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Extension\Shippable;
use Dynamic\Foxy\Model\FoxyCategory;
use Dynamic\Foxy\Model\Variation;
use Dynamic\Foxy\Test\TestOnly\TestProduct;
use Dynamic\Foxy\Test\TestOnly\TestVariationDataExtension;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Member;

/**
 * Class FoxyCategoryTest
 * @package Dynamic\Foxy\Test\Model
 */
class FoxyCategoryTest extends SapphireTest
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
    public static $extra_dataobjects = [
        TestProduct::class,
    ];

    /**
     * @var \string[][]
     */
    protected static $required_extensions = [
        TestProduct::class => [
            Purchasable::class,
            Shippable::class,
        ],
        Variation::class => [
            TestVariationDataExtension::class,
        ],
    ];

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = $this->objFromFixture(FoxyCategory::class, 'one');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
        $this->assertInstanceOf(TextField::class, $fields->dataFieldByName('Title'));
        $this->assertInstanceOf(TextField::class, $fields->dataFieldByName('Code'));

        $default = FoxyCategory::create();
        $default->Title = 'DEFAULT';
        $default->Code = 'DEFAULT';
        $default->write();

        $defaultCategoryFields = $default->getCMSFields();

        $this->assertInstanceOf(ReadonlyField::class, $defaultCategoryFields->dataFieldByName('Title'));
        $this->assertInstanceOf(ReadonlyField::class, $defaultCategoryFields->dataFieldByName('Code'));
    }

    /**
     *
     */
    public function testValidateCode()
    {
        $object = $this->objFromFixture(FoxyCategory::class, 'one');
        $object->Code = '';
        $this->expectException(ValidationException::class);
        $object->write();

        $object = $this->objFromFixture(FoxyCategory::class, 'one');
        $object->Code = '67890';
        $this->expectException(ValidationException::class);
        $object->write();
    }

    /**
     *
     */
    public function testCanCreate()
    {
        /** @var FoxyCategory $object */
        $object = FoxyCategory::singleton();
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
        /** @var FoxyCategory $object */
        $object = FoxyCategory::singleton();
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
        /** @var FoxyCategory $object */
        $object = FoxyCategory::singleton();
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
}
