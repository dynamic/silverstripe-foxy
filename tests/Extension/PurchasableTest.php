<?php

namespace Dynamic\Foxy\Test\Extension;

use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Model\FoxyCategory;
use Dynamic\Foxy\Model\OptionType;
use Dynamic\Foxy\Test\TestOnly\TestProduct;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\Debug;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ValidationException;

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
     *
     */
    public function setUp()
    {
        Config::modify()->set(OptionType::class, 'has_one', ['TestProduct' => TestProduct::class]);

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

    /**
     *
     */
    public function testIsAvailable()
    {
        $object = Injector::inst()->create(TestProduct::class);
        $this->assertTrue($object->isAvailable());

        $object->Available = 0;
        $this->assertFalse($object->isAvailable());
    }

    /**
     *
     */
    public function isProduct()
    {
        $object = Injector::inst()->create(TestProduct::class);
        $this->assertTrue($object->isProduct());
    }
}
