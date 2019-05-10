<?php

namespace Dynamic\Foxy\Extension;

use Dynamic\Foxy\Model\FoxyCategory;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ValidationResult;

class Purchasable extends DataExtension
{
    /**
     * @var array
     */
    private static $db = [
        'Price' => 'Currency',
        'Code' => 'Varchar(100)',
        'ReceiptTitle' => 'HTMLVarchar(255)',
        'Available' => 'Boolean',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'FoxyCategory' => FoxyCategory::class,
    ];

    /**
     * @var array
     */
    private static $indexes = [
        'Code' => [
            'type' => 'unique',
            'columns' => ['Code'],
        ],
    ];

    /**
     * @var array
     */
    private static $defaults = [
        'ShowInMenus' => false,
        'Available' => true,
        'Weight' => '0.0',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Image.CMSThumbnail',
        'Title',
        'Code',
        'Price.Nice',
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Title',
        'Code',
        'Available',
    ];

    /**
     * @param bool $includerelations
     *
     * @return array
     */
    public function updateFieldLabels(&$labels)
    {
        $labels['Title'] = _t(__CLASS__ . '.TitleLabel', 'Product Name');
        $labels['Code'] = _t(__CLASS__ . '.CodeLabel', 'Code');
        $labels['Price'] = _t(__CLASS__ . '.PriceLabel', 'Price');
        $labels['Price.Nice'] = _t(__CLASS__ . '.PriceLabel', 'Price');
        $labels['Available'] = _t(__CLASS__ . '.AvailableLabel', 'Available for purchase');
        $labels['Available.Nice'] = _t(__CLASS__ . '.AvailableLabelNice', 'Available');
        $labels['Image.CMSThumbnail'] = _t(__CLASS__ . '.ImageLabel', 'Image');
        $labels['ReceiptTitle'] = _t(__CLASS__ . '.ReceiptTitleLabel', 'Product title for receipt');
        $labels['FoxyCategoryID'] = _t(__CLASS__ . '.FoxyCategoryLabel', 'Foxy Category');
    }

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.Foxy',
            [
                CurrencyField::create('Price')
                    ->setDescription(_t(
                        __CLASS__ . '.PriceDescription',
                        'Base price for this product. Can be modified using Product Options'
                    )),
                TextField::create('Code')
                    ->setDescription(_t(
                        __CLASS__ . '.CodeDescription',
                        'Required, must be unique. Product identifier used by FoxyCart in transactions'
                    )),
                DropdownField::create('FoxyCategoryID')
                    ->setSource(FoxyCategory::get()->map())
                    ->setDescription(_t(
                        __CLASS__ . '.FoxyCategoryDescription',
                        'Required. Must also exist in 
                        <a href="https://admin.foxycart.com/admin.php?ThisAction=ManageProductCategories" target="_blank">
                            Foxy Categories
                        </a>.
                        Used to set category specific options like shipping and taxes. Managed in Foxy > Categories'
                    ))
                    ->setEmptyString(''),
                TextField::create('ReceiptTitle')
                    ->setDescription(_t(
                        __CLASS__ . '.ReceiptTitleDescription',
                        'Optional. Alternate title to display on order receipt'
                    )),
                CheckboxField::create('Available')
                    ->setDescription(_t(
                        __CLASS__ . '.AvailableDescription',
                        'If unchecked, will remove "Add to Cart" form and instead display "Currently unavailable"'
                    )),
            ],
            'Content'
        );
    }

    /**
     * @return \SilverStripe\ORM\ValidationResult
     */
    public function validate(ValidationResult $validationResult)
    {
        if (!$this->owner->Price) {
            $validationResult->addError(
                _t(__CLASS__ . '.PriceRequired', 'You must set a product price in the Foxy tab')
            );
        }

        if (!$this->owner->Code) {
            $validationResult->addError(
                _t(__CLASS__ . '.CodeRequired', 'You must set a product code in the Foxy tab')
            );
        }

        if (!$this->owner->FoxyCategoryID) {
            $validationResult->addError(
                _t(__CLASS__ . '.FoxyCategoryRequired', 'You must set a foxy category in the Foxy tab.')
            );
        }
    }
}
