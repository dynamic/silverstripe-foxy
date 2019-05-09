<?php

namespace Dynamic\Foxy\Test\Model;

use Dynamic\Foxy\Model\Setting;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Member;

/**
 * Class SettingsTest
 * @package Dynamic\Foxy\Test\Model
 */
class SettingsTest extends SapphireTest
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
        /** @var Setting $object */
        $object = singleton(Setting::class);
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testGetCMSActions()
    {
        /** @var Setting $object */
        $object = singleton(Setting::class);
        $fields = $object->getCMSActions();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testOnBeforeWrite()
    {
        /** @var Setting $object */
        $object = Setting::create();
        $this->assertNull($object->StoreKey);

        $object->write();
        $this->assertInternalType('string', $object->StoreKey);
    }

    /**
     *
     */
    public function testGenerateStoreKey()
    {
        /** @var Setting $object */
        $object = singleton(Setting::class);
        $key = $object->generateStoreKey();
        $this->assertEquals(60, strlen($key));
        $this->assertEquals('dYnm1c', substr($key, 0, 6));
    }

    /**
     *
     */
    public function testCanEdit()
    {
        /** @var Setting $object */
        $object = singleton(Setting::class);
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
    public function testProvidePermissions()
    {
        /** @var Setting $object */
        $object = singleton(Setting::class);
        $this->assertInternalType('array', $object->providePermissions());
        $this->assertArrayHasKey('EDIT_FOXY_SETTING', $object->providePermissions());
    }

    /**
     *
     */
    public function testCurrentFoxySetting()
    {
        $this->assertInstanceOf(Setting::class, Setting::current_foxy_setting());
    }

    /**
     *
     */
    public function testMakeFoxySetting()
    {
        $this->assertInstanceOf(Setting::class, Setting::make_foxy_setting());
    }

    /**
     *
     */
    public function testGetTemplateGlobalVariables()
    {
        $this->assertInternalType('array', Setting::get_template_global_variables());
        $this->assertArrayHasKey('FoxyStripe', Setting::get_template_global_variables());
    }
}
