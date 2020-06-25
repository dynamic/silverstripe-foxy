<?php

namespace Dynamic\Foxy\Test\Model;

use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Model\OptionType;
use Dynamic\Foxy\Model\ProductOption;
use Dynamic\Foxy\Test\TestOnly\TestProduct;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Member;
use SilverStripe\Versioned\Versioned;

/**
 * Class ProductOptionTest
 * @package Dynamic\Foxy\Test\Model
 */
class ProductOptionTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = '../fixtures.yml';

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
        ],
    ];

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = $this->objFromFixture(ProductOption::class, 'small');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testCanCreate()
    {
        /** @var ProductOption $object */
        $object = singleton(ProductOption::class);
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
        /** @var ProductOption $object */
        $object = singleton(ProductOption::class);
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
        /** @var ProductOption $object */
        $object = singleton(ProductOption::class);
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
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function testGenerateKey()
    {
        $this->markTestSkipped();
        /*$product = $this->findOrMakeProduct();
        $option = ProductOption::create();
        //$option->write();

        $option->Title = $title = 'My Title';
        $price = 150;
        $action = 'Set';

        $product->Options()->add(
            $option,
            [
                'Available' => true,
                'PriceModifier' => $price,
                'PriceModifierAction' => $action,
            ]
        );

        $actionSymbol = ProductOption::getOptionModifierActionSymbol($action);

        $expected = "{$title}{p{$actionSymbol}{$price}|w+0|c+0}";

        $option = $product->Options()->filter('ProductOptionID', $option->ID)->first();

        $this->assertEquals(
            $expected,
            $option->OptionModifierKey
        );//*/
    }

    /**
     * @return TestProduct|\SilverStripe\ORM\DataObject
     */
    protected function findOrMakeProduct()
    {
        if (!$product = TestProduct::get()->first()) {
            $product = TestProduct::create();
            $product->Title = 'My Product';
            $product->writeToStage(Versioned::DRAFT);
            $product->publishSingle();
        }

        return $product;
    }
}
