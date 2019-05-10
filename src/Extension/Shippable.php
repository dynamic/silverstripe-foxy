<?php

namespace Dynamic\Foxy\Extension;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\ValidationResult;

class Shippable extends Purchasable
{
    /**
     * @var array
     */
    private static $db = [
        'Weight' => 'Decimal',
    ];

    /**
     * @var array
     */
    private static $defaults = [
        'Weight' => '0.0',
    ];

    /**
     * @param bool $includerelations
     *
     * @return array
     */
    public function updateFieldLabels(&$labels)
    {
        $labels['Weight'] = _t(__CLASS__ . '.WeightLabel', 'Weight');
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
                    ->setScale(2),
            ],
            'FoxyCategoryID'
        );
    }

    /**
     * @return \SilverStripe\ORM\ValidationResult
     */
    public function validate(ValidationResult $validationResult)
    {
        if (!$this->owner->Weight) {
            $validationResult->addError(
                _t(__CLASS__ . '.WeightRequired', 'You must set a product weight in the Foxy tab.')
            );
        }
    }
}
