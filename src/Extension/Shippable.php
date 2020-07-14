<?php

namespace Dynamic\Foxy\Extension;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ValidationResult;

/**
 * Class Shippable
 * @package Dynamic\Foxy\Extension
 *
 * @property double Weight
 *
 * @property-read \SilverStripe\ORM\DataObject|Shippable $owner
 */
class Shippable extends DataExtension
{
    /**
     * @var array
     */
    private static $db = [
        'Weight' => 'Decimal(9,3)',
    ];

    /**
     * @var array
     */
    private static $defaults = [
        'Weight' => '1.0',
    ];

    /**
     * @param bool $includerelations
     *
     * @return array
     */
    public function updateFieldLabels(&$labels)
    {
        $labels['Title'] = _t(__CLASS__ . '.TitleLabel', 'Product Name');
        $labels['Weight'] = _t(__CLASS__ . '.WeightLabel', 'Weight');
        $labels['Image.CMSThumbnail'] = _t(__CLASS__ . '.ImageThumbnailLabel', 'Image');
        $labels['Price.Nice'] = _t(__CLASS__ . '.PriceLabel', 'Price');
    }

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        parent::updateCMSFields($fields);

        $fields->addFieldsToTab(
            'Root.Ecommerce',
            [
                NumericField::create('Weight')
                    ->setTitle($this->owner->fieldLabel('Weight'))
                    ->setDescription(_t(
                        __CLASS__ . '.WeightDescription',
                        'Base weight for this product in lbs. Can be modified using Product Options. Only supports up to 3 decimal places'
                    ))
                    ->setScale(3),
            ],
            'FoxyCategoryID'
        );
    }

    /**
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return new RequiredFields([
            "Price",
            "Code",
            "FoxyCategoryID",
            'Weight',
        ]);
    }
}
