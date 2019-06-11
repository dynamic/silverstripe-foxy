<?php

namespace Dynamic\Foxy\Test\Model;

use Dynamic\Foxy\Model\FoxyCategory;
use Dynamic\Foxy\Test\TestOnly\TestProduct;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Member;

class FoxyCategoryTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = '../fixtures.yml';

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = $this->objFromFixture(FoxyCategory::class, 'one');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);

        $object = Injector::inst()->create(FoxyCategory::class);
        $object->Code = 'DEFAULT';
        $object->write();
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testValidateCode()
    {
        $object = $this->objFromFixture(FoxyCategory::class, 'one');
        $object->Code = '';
        $this->setExpectedException(ValidationException::class);
        $object->write();

        $object = $this->objFromFixture(FoxyCategory::class, 'one');
        $object->Code = 'DEFAULT';
        $this->setExpectedException(ValidationException::class);
        $object->write();
    }

    /**
     *
     */
    public function testCanCreate()
    {
        /** @var FoxyCategory $object */
        $object = singleton(FoxyCategory::class);
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
        /** @var FoxyCategory $object */
        $object = singleton(FoxyCategory::class);
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
        /** @var FoxyCategory $object */
        $object = singleton(FoxyCategory::class);
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
}
