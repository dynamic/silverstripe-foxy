<?php

namespace Dynamic\Foxy\Page;

use Dynamic\Foxy\Controller\ShippableProductController;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\RequiredFields;

class ShippableProduct extends Product
{
    /**
     * @var string
     */
    private static $table_name = 'ShippableProduct';

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
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);

        $labels['Title'] = _t(__CLASS__ . '.TitleLabel', 'Product Name');
        $labels['Weight'] = _t(__CLASS__ . '.WeightLabel', 'Weight');
        $labels['Image.CMSThumbnail'] = _t(__CLASS__ . '.ImageThumbnailLabel', 'Image');
        $labels['Price.Nice'] = _t(__CLASS__ . '.PriceLabel', 'Price');

        return $labels;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
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
        });

        return parent::getCMSFields();
    }

    /**
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        $validator = parent::getCMSValidator();

        $validator->appendRequiredFields(
            new RequiredFields([
                'Weight',
            ])
        );

        return $validator;
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return ShippableProductController::class;
    }
}
