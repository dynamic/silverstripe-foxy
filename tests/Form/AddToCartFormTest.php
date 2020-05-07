<?php

namespace Dynamic\Foxy\Test\Form;

use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Extension\Shippable;
use Dynamic\Foxy\Form\AddToCartForm;
use Dynamic\Foxy\Test\TestOnly\TestShippableProduct;
use Dynamic\Foxy\Test\TestOnly\TestShippableProductController;
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
    protected static $fixture_file = '../fixtures.yml';

    /**
     * @var \string[][]
     */
    protected static $required_extensions = [
        TestShippableProduct::class => [
            Purchasable::class,
            Shippable::class,
        ]
    ];

    /**
     *
     */
    public function testConstruct()
    {
        $object = Injector::inst()->create(TestShippableProduct::class);
        $controller = TestShippableProductController::create($object);
        $form = $controller->AddToCartForm($controller, __FUNCTION__, null, null, null, $object);
        $this->assertInstanceOf(Form::class, $form);
    }

    /**
     *
     */
    public function testGetProductFields()
    {
        $object = Injector::inst()->create(TestShippableProduct::class);
        $object->Weight = 1.0;
        $controller = TestShippableProductController::create($object);
        $form = $controller->AddToCartForm($controller, __FUNCTION__, null, null, null, $object);
        $fields = $form->Fields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testGetProductActions()
    {
        $object = Injector::inst()->create(TestShippableProduct::class);
        $controller = TestShippableProductController::create($object);
        $form = $controller->AddToCartForm($controller, __FUNCTION__, null, null, null, $object);
        $fields = $form->Actions();
        $this->assertInstanceOf(FieldList::class, $fields);
    }
}
