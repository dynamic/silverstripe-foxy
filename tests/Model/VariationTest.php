<?php

namespace Dynamic\Foxy\Test\Model;

use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Extension\Shippable;
use Dynamic\Foxy\Model\Variation;
use Dynamic\Foxy\Test\TestOnly\TestProduct;
use Dynamic\Foxy\Test\TestOnly\TestVariationDataExtension;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Member;
use SilverStripe\Versioned\Versioned;

/**
 * Class ProductOptionTest
 * @package Dynamic\Foxy\Test\Model
 */
class VariationTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = [
        '../fixtures.yml',
        '../shippableproducts.yml',
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
    public function testVariationHasExtension()
    {
        $this->assertTrue(Variation::has_extension(TestVariationDataExtension::class));
        $this->assertTrue(Variation::singleton()->hasDatabaseField('ProductID'));
    }

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = Variation::singleton();
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testCanCreate()
    {
        /** @var Variation $object */
        $object = Variation::singleton();
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
        /** @var Variation $object */
        $object = Variation::singleton();
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
        /** @var Variation $object */
        $object = Variation::singleton();
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
     * @throws ValidationException
     */
    public function testOnBeforeWrite()
    {
        $productID = $this->idFromFixture(TestProduct::class, 'productfiveandvariations');

        $newVariation = Variation::create();
        $newVariation->Title = 'My Variation - Before Write Test';
        $newVariation->ProductID = $productID;

        $newVariation->PriceModifierAction = 'Add';
        $newVariation->PriceModifier = 10;

        $newVariation->WeightModifier = .5;
        $newVariation->WeightModifierAction = 'Subtract';

        $newVariation->CodeModifier = 'my-foo-code-modifier';
        $newVariation->CodeModifierAction = 'Set';

        $this->assertNull($newVariation->FinalPrice);
        $this->assertNull($newVariation->FinalWeight);
        $this->assertNull($newVariation->FinalCode);

        $newVariation->write();
        /** @var Variation $newVariation */
        $newVariation = Variation::get()->byID($newVariation->ID);

        $this->assertEquals(110, $newVariation->FinalPrice);
        $this->assertEquals(9.5, $newVariation->FinalWeight);
        $this->assertEquals('my-foo-code-modifier', $newVariation->FinalCode);
    }

    /**
     *
     */
    public function testBeforeWriteRetainWhiteSpace()
    {
        Variation::config()->update('code_trim_right_space', false);
        Variation::config()->update('code_trim_left_spaces', false);
        $productID = $this->idFromFixture(TestProduct::class, 'productfiveandvariations');

        $newVariation = Variation::create();
        $newVariation->Title = 'My Variation - Before Write Whitespace Test';
        $newVariation->ProductID = $productID;

        $newVariation->CodeModifier = ' my-foo-code- modifier ';
        $newVariation->CodeModifierAction = 'Add';

        $newVariation->write();
        $newVariation = Variation::get()->byID($newVariation->ID);

        $this->assertEquals(' my-foo-code- modifier ', $newVariation->CodeModifier);
    }

    /**
     * @throws ValidationException
     */
    public function testBeforeWriteTrimRight()
    {
        Variation::config()->update('code_trim_right_space', true);
        $productID = $this->idFromFixture(TestProduct::class, 'productfiveandvariations');

        $newVariation = Variation::create();
        $newVariation->Title = 'My Variation - Before Write Whitespace Test';
        $newVariation->ProductID = $productID;

        $newVariation->CodeModifier = ' my-foo-code- modifier ';
        $newVariation->CodeModifierAction = 'Add';

        $newVariation->write();
        $newVariation = Variation::get()->byID($newVariation->ID);

        $this->assertEquals(' my-foo-code- modifier', $newVariation->CodeModifier);
    }

    /**
     * @throws ValidationException
     */
    public function testBeforeWriteSingleSpace()
    {
        Variation::config()->update('code_trim_left_spaces', false);
        Variation::config()->update('code_trim_right_space', false);
        Variation::config()->update('code_enforce_single_spaces', true);
        $productID = $this->idFromFixture(TestProduct::class, 'productfiveandvariations');

        $newVariation = Variation::create();
        $newVariation->Title = 'My Variation - Before Write Whitespace Test';
        $newVariation->ProductID = $productID;

        $newVariation->CodeModifier = '   my-foo-code-  modifier    ';
        $newVariation->CodeModifierAction = 'Add';

        $newVariation->write();
        $newVariation = Variation::get()->byID($newVariation->ID);

        $this->assertEquals(' my-foo-code- modifier ', $newVariation->CodeModifier);
    }

    /**
     * @throws ValidationException
     */
    public function testBeforeWriteRemoveSpaces()
    {
        Variation::config()->update('code_enforce_single_spaces', true);
        Variation::config()->update('code_remove_spaces', true);
        $productID = $this->idFromFixture(TestProduct::class, 'productfiveandvariations');

        $newVariation = Variation::create();
        $newVariation->Title = 'My Variation - Before Write Whitespace Test';
        $newVariation->ProductID = $productID;

        $newVariation->CodeModifier = '   my-foo-code-  modifier    ';
        $newVariation->CodeModifierAction = 'Add';

        $newVariation->write();
        $newVariation = Variation::get()->byID($newVariation->ID);

        $this->assertEquals('my-foo-code-modifier', $newVariation->CodeModifier);
    }

    /**
     * @throws ValidationException
     */
    public function testBeforeWriteSetSpaces()
    {
        $productID = $this->idFromFixture(TestProduct::class, 'productfiveandvariations');

        $newVariation = Variation::create();
        $newVariation->Title = 'My Variation - Before Write Whitespace Test';
        $newVariation->ProductID = $productID;

        $newVariation->CodeModifier = ' my-foo-code-  modifier    ';
        $newVariation->CodeModifierAction = 'Add';

        $newVariation->write();
        $newVariation = Variation::get()->byID($newVariation->ID);

        $this->assertEquals(' my-foo-code- modifier', $newVariation->CodeModifier);

        Variation::config()->update('code_trim_left_spaces', true);

        $newVariation->CodeModifier = ' my-foo-code-  modifier    ';
        $newVariation->write();

        $this->assertEquals('my-foo-code- modifier', $newVariation->CodeModifier);
    }

    /**
     * @throws ValidationException
     */
    public function testBeforeWriteNoSpaces()
    {
        Variation::config()->update('code_remove_spaces', true);
        $productID = $this->idFromFixture(TestProduct::class, 'productfiveandvariations');

        $newVariation = Variation::create();
        $newVariation->Title = 'My Variation - Before Write Whitespace Test';
        $newVariation->ProductID = $productID;

        $newVariation->CodeModifier = '   my-foo-code-  modifier    ';
        $newVariation->CodeModifierAction = 'Add';

        $newVariation->write();
        $newVariation = Variation::get()->byID($newVariation->ID);

        $this->assertEquals('my-foo-code-modifier', $newVariation->CodeModifier);
    }

    /**
     *
     */
    public function testGetGeneratedValue()
    {
        $variation = $this->objFromFixture(Variation::class, 'variationone');
        $variation2 = $this->objFromFixture(Variation::class, 'variationtwo');
        $variation3 = $this->objFromFixture(Variation::class, 'variationthree');

        //Add
        $expected = sprintf(
            '%s{p%s|w%s|c%s}',
            $variation->Title,
            '+10',
            '+0',
            '+'
        );

        //Set
        $expected2 = sprintf(
            '%s{p%s|w%s|c%s}',
            $variation2->Title,
            ':150',
            '+0',
            '+'
        );

        //Subtract
        $expected3 = sprintf(
            '%s{p%s|w%s|c%s}',
            $variation3->Title,
            '-20',
            '+0',
            '+'
        );

        $this->assertEquals($expected, $variation->getGeneratedValue());
        $this->assertEquals($expected2, $variation2->getGeneratedValue());
        $this->assertEquals($expected3, $variation3->getGeneratedValue());
    }

    /**
     * @return TestProduct|DataObject
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
