<?php

namespace Dynamic\Foxy\Model;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBBoolean;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

class ProductOption extends DataObject
{
    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)',
    ];

    /**
     * @var string
     */
    private static $table_name = 'ProductOption';

    /**
     * @var string
     */
    private static $singular_name = 'Option';

    /**
     * @var string
     */
    private static $plural_name = 'Options';

    /**
     * @var array
     */
    private static $defaults = [
        'ManyMany[Available]' => true,
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title' => 'Title',
        'IsAvailable' => 'Available',
        'OptionType' => 'Type',
    ];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            if ($this->exists()) {
                $fields->addFieldToTab(
                    'Root.Main',
                    ReadonlyField::create('ManyMany[OptionModifierKey]')
                        ->setTitle(_t('OptionItem.ModifierKey', 'Modifier Key'))
                );
            }

            $fields->addFieldsToTab('Root.Main', [
                CheckboxField::create('ManyMany[Available]', 'Available for purchase'),

                DropdownField::create('ManyMany[Type]', 'Option Type', OptionType::get()->map())
                    ->setEmptyString(''),

                // Weight Modifier Fields
                HeaderField::create('WeightHD', _t('OptionItem.WeightHD', 'Modify Weight'), 4),
                TextField::create("ManyMany[WeightModifier]")
                    ->setTitle(_t('OptionItem.WeightModifier', 'Weight')),
                DropdownField::create(
                    'ManyMany[WeightModifierAction]',
                    _t('OptionItem.WeightModifierAction', 'Weight Modification'),
                    [
                        'Add' => _t(
                            'OptionItem.WeightAdd',
                            'Add to Base Weight',
                            'Add to weight'
                        ),
                        'Subtract' => _t(
                            'OptionItem.WeightSubtract',
                            'Subtract from Base Weight',
                            'Subtract from weight'
                        ),
                        'Set' => _t('OptionItem.WeightSet', 'Set as a new Weight'),
                    ]
                )
                    ->setEmptyString('')
                    ->setDescription(_t(
                        'OptionItem.WeightDescription',
                        'Does weight modify or replace base weight?'
                    )),

                // Price Modifier FIelds
                HeaderField::create('PriceHD', _t('OptionItem.PriceHD', 'Modify Price'), 4),
                CurrencyField::create('ManyMany[PriceModifier]')
                    ->setTitle(_t('OptionItem.PriceModifier', 'Price')),
                DropdownField::create(
                    'ManyMany[PriceModifierAction]',
                    _t('OptionItem.PriceModifierAction', 'Price Modification'),
                    [
                        'Add' => _t(
                            'OptionItem.PriceAdd',
                            'Add to Base Price',
                            'Add to price'
                        ),
                        'Subtract' => _t(
                            'OptionItem.PriceSubtract',
                            'Subtract from Base Price',
                            'Subtract from price'
                        ),
                        'Set' => _t('OptionItem.PriceSet', 'Set as a new Price'),
                    ]
                )
                    ->setEmptyString('')
                    ->setDescription(_t('OptionItem.PriceDescription', 'Does price modify or replace base price?')),

                // Code Modifier Fields
                HeaderField::create('CodeHD', _t('OptionItem.CodeHD', 'Modify Code'), 4),
                TextField::create('ManyMany[CodeModifier]')
                    ->setTitle(_t('OptionItem.CodeModifier', 'Code')),
                DropdownField::create(
                    'ManyMany[CodeModifierAction]',
                    _t('OptionItem.CodeModifierAction', 'Code Modification'),
                    [
                        'Add' => _t(
                            'OptionItem.CodeAdd',
                            'Add to Base Code',
                            'Add to code'
                        ),
                        'Subtract' => _t(
                            'OptionItem.CodeSubtract',
                            'Subtract from Base Code',
                            'Subtract from code'
                        ),
                        'Set' => _t('OptionItem.CodeSet', 'Set as a new Code'),
                    ]
                )
                    ->setEmptyString('')
                    ->setDescription(_t('OptionItem.CodeDescription', 'Does code modify or replace base code?')),
            ]);
        });

        return parent::getCMSFields();
    }

    /**
     *
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $field = 'ManyMany[OptionModifierKey]';
        $this->{$field} = $this->getGeneratedValue();
    }

    /**
     * @param $oma
     * @param bool $returnWithOnlyPlusMinus
     *
     * @return string
     */
    public static function getOptionModifierActionSymbol($oma, $returnWithOnlyPlusMinus = false)
    {
        switch ($oma) {
            case 'Subtract':
                $symbol = '-';
                break;
            case 'Set':
                $symbol = ($returnWithOnlyPlusMinus) ? '' : ':';
                break;
            default:
                $symbol = '+';
        }

        return $symbol;
    }

    /**
     * @return string
     */
    public function getWeightModifierWithSymbol()
    {
        return self::getOptionModifierActionSymbol($this->WeightModifierAction) . $this->WeightModifier;
    }

    /**
     * @return string
     */
    public function getPriceModifierWithSymbol()
    {
        return self::getOptionModifierActionSymbol($this->PriceModifierAction) . $this->PriceModifier;
    }

    /**
     * @return string
     */
    public function getCodeModifierWithSymbol()
    {
        return self::getOptionModifierActionSymbol($this->CodeModifierAction) . $this->CodeModifier;
    }

    /**
     * @return string
     */
    public function getGeneratedValue()
    {
        $modPrice = ($this->PriceModifier) ? (string)$this->PriceModifier : '0';
        $modPriceWithSymbol = self::getOptionModifierActionSymbol($this->PriceModifierAction) . $modPrice;
        $modWeight = ($this->WeightModifier) ? (string)$this->WeightModifier : '0';
        $modWeight = self::getOptionModifierActionSymbol($this->WeightModifierAction) . $modWeight;
        $modCode = self::getOptionModifierActionSymbol($this->CodeModifierAction) . $this->CodeModifier;

        return $this->Title . '{p' . $modPriceWithSymbol . '|w' . $modWeight . '|c' . $modCode . '}';
    }

    /**
     * @return mixed|string
     */
    public function getGeneratedTitle()
    {
        $modPrice = ($this->PriceModifier) ? (string)$this->PriceModifier : '0';
        $title = $this->Title;
        $title .= ($this->PriceModifier != 0) ?
            ': (' . self::getOptionModifierActionSymbol(
                $this->PriceModifierAction,
                $returnWithOnlyPlusMinus = true
            ) . '$' . $modPrice . ')' :
            '';

        return $title;
    }

    /**
     * @return bool
     */
    public function getAvailability()
    {
        $available = ($this->Available == 1) ? true : false;
        $this->extend('updateOptionAvailability', $available);

        return $available;
    }

    /**
     * @return string
     */
    public function getIsAvailable()
    {
        $available = DBBoolean::create();
        $available->setValue($this->getAvailability());

        return $available->Nice();
    }

    /**
     * @return string
     */
    public function getOptionType()
    {
        if ($this->Type) {
            $type = OptionType::get()->byID($this->Type);
            if ($type) {
                return $type->Title;
            }
        }
    }

    /**
     * @param $member
     * @return bool|int|void
     */
    public function canCreate($member = null, $context = [])
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        return Permission::checkMember($member, 'MANAGE_FOXY_PRODUCTS');
    }

    /**
     * @param $member
     * @return bool|int|void|null
     */
    public function canEdit($member = null, $context = [])
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        return Permission::checkMember($member, 'MANAGE_FOXY_PRODUCTS');
    }

    /**
     * @param $member
     * @return bool|int|void
     */
    public function canDelete($member = null, $context = [])
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        return Permission::checkMember($member, 'MANAGE_FOXY_PRODUCTS');
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getPrice($product)
    {
        switch ($this->PriceModifierAction) {
            case 'Subtract':
                return $product->Price - $this->PriceModifier;
                break;
            case 'Set':
                return $this->PriceModifier;
                break;
            case 'Add':
                return $product->Price + $this->PriceModifier;
                break;
        }

        return $product->Price;
    }
}
