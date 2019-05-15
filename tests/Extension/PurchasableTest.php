<?php

namespace Dynamic\Foxy\Test\Extension;

use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Model\FoxyCategory;
use Dynamic\Foxy\Test\TestOnly\TestProduct;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\Debug;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;

class PurchasableTest extends SapphireTest
{
    protected static $fixture_file = '../fixtures.yml';

    public function testUpdateCMSFields()
    {
        $object = Injector::inst()->create(TestProduct::class);
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);

        $object->Price = 10.00;
        $object->Code = 000123;
        $object->FoxyCategoryID = $this->objFromFixture(FoxyCategory::class, 'one')->ID;
        /*
        // todo - table TestProduct does not exist
        $object->write();
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
        */
    }
}
