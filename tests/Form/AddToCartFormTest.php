<?php

namespace Dynamic\Foxy\Test\Form;

use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Extension\PurchasableExtension;
use Dynamic\Foxy\Extension\Shippable;
use Dynamic\Foxy\Form\AddToCartForm;
use Dynamic\Foxy\Model\Variation;
use Dynamic\Foxy\Test\TestOnly\TestProduct;
use Dynamic\Foxy\Test\TestOnly\TestShippableProduct;
use Dynamic\Foxy\Test\TestOnly\TestShippableProductController;
use Dynamic\Foxy\Test\TestOnly\TestVariationDataExtension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;

/**
 * Class AddToCartFormTest
 * @package Dynamic\Foxy\Test\Form
 */
class AddToCartFormTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = [
        '../fixtures.yml',
        '../shippableproducts.yml',
    ];

    /**
     * @var string[]
     */
    protected static $extra_controllers = [
        TestShippableProductController::class,
    ];

    /**
     * @var string[]
     */
    protected static $extra_dataobjects = [
        TestProduct::class,
        TestShippableProduct::class,
    ];

    /**
     * @var \string[][]
     */
    protected static $required_extensions = [
        TestProduct::class => [
            Purchasable::class,
            Shippable::class,
        ],
        TestShippableProduct::class => [
            Purchasable::class,
            Shippable::class,
        ],
        Variation::class => [
            TestVariationDataExtension::class,
        ],
        TestShippableProductController::class => [
            PurchasableExtension::class,
        ],
    ];


    /**
     *
     */
    public function testConstruct()
    {
        $object = TestShippableProduct::create();
        $controller = TestShippableProductController::create($object);
        $form = $controller->AddToCartForm();
        $this->assertInstanceOf(AddToCartForm::class, $form);
    }

    /**
     *
     */
    public function testGetProductFields()
    {
        /** @var TestProduct $productPage */
        $productPage = $this->objFromFixture(TestProduct::class, 'productone');
        $controller = TestShippableProductController::create($productPage);
        $form = $controller->AddToCartForm();
        $fields = $form->Fields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testGetProductActions()
    {
        $productPage = $this->objFromFixture(TestProduct::class, 'productone');
        $controller = TestShippableProductController::create($productPage);
        $form = $controller->AddToCartForm();
        $fields = $form->Actions();
        $this->assertInstanceOf(FieldList::class, $fields);
    }
}
