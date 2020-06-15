<?php

namespace Dynamic\Foxy\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;

/**
 * Class VariationType
 * @package Dynamic\Foxy\Model
 */
class VariationType extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'VariationType';

    /**
     * @var string
     */
    private static $singular_name = 'Variation Type';

    /**
     * @var string
     */
    private static $plural_name = 'Variation Types';

    /**
     * @var string[]
     */
    private static $db = [
        'Title' => 'Varchar',
    ];

    /**
     * @var string[]
     */
    private static $has_many = [
        'Variations' => Variation::class,
    ];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'VariationSets',
            ]);
        });

        return parent::getCMSFields();
    }
}
