<?php

namespace Dynamic\Foxy\Test\Model;

use Dynamic\Foxy\Model\FoxyCategory;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ValidationException;

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
        $object->Code = '67890';
        $this->setExpectedException(ValidationException::class);
        $object->write();
    }
}
