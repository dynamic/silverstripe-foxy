<?php

namespace Dynamic\Foxy\Extension;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ValidationResult;

class Shippable extends DataExtension
{
    /**
     * @var array
     */
    private static $db = [
        'Weight' => 'Decimal(9,6)',
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
            'Root.Foxy',
            [
                NumericField::create('Weight')
                    ->setTitle($this->owner->fieldLabel('Weight'))
                    ->setDescription(_t(
                        __CLASS__ . '.WeightDescription',
                        'Base weight for this product in lbs. Can be modified using Product Options'
                    ))
                    ->setScale(6),
            ],
            'FoxyCategoryID'
        );
    }

    /**
     * @return \SilverStripe\ORM\ValidationResult
     */
    public function validate(ValidationResult $validationResult)
    {
        // todo: validation fails even if positive value is entered
        /*
        if ($this->owner->Weight <= 0) {
            $validationResult->addError(
                _t(__CLASS__ . '.WeightRequired', 'You must set a product weight in the Foxy tab.')
            );
        }
        */
    }
}
