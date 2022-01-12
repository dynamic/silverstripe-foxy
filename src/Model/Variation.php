<?php

namespace Dynamic\Foxy\Model;

use Bummzack\SortableFile\Forms\SortableUploadField;
use Dynamic\Foxy\Page\Product;
use SilverStripe\Assets\File;
use SilverStripe\Dev\Deprecation;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

/**
 * Class Variation
 * @package Dynamic\Foxy\Model
 *
 * @property string $Title
 * @property bool $UseProductContent
 * @property string Content
 * @property float $WeightModifier
 * @property string $CodeModifier
 * @property float $PriceModifier
 * @property string $WeightModifierAction
 * @property string $CodeModifierAction
 * @property string $PriceModifierAction
 * @property bool $Available
 * @property int $Type
 * @property string $OptionModifierKey
 * @property int $SortOrder
 * @property float $FinalPrice
 * @property float $FinalWeight
 * @property string $FinalCode
 * @property bool $IsDefault
 * @property int $VariationTypeID
 * @property int $ProductID
 *
 * @method VariationType VariationType()
 * @method Product Product
 *
 * @method ManyManyList Images()
 */
class Variation extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'Variation';

    /**
     * @var string
     */
    private static $singular_name = 'Variation';

    /**
     * @var string
     */
    private static $plural_name = 'Variations';

    /**
     * @var bool
     */
    private static $code_trim_right_space = true;

    /**
     * @var bool
     */
    private static $code_enforce_single_spaces = true;

    /**
     * @var bool
     */
    private static $code_trim_left_spaces = false;

    /**
     * Mapping from a Product's field to the Variation's field for a default variation
     *
     * @var string[]
     */
    private static $default_variation_mapping = [
        'Title' => 'Title',
        'Available' => 'Available',
    ];

    /**
     * @var string[]
     */
    private static $db = [
        'Title' => 'Varchar(255)',
        'UseProductContent' => 'Boolean',
        'Content' => 'HTMLText',
        'WeightModifier' => 'Decimal(9,3)',
        'CodeModifier' => 'Text',
        'PriceModifier' => 'Currency',
        'WeightModifierAction' => "Enum('Add,Subtract,Set', null)",
        'CodeModifierAction' => "Enum('Add,Subtract,Set', null)",
        'PriceModifierAction' => "Enum('Add,Subtract,Set', null)",
        'Available' => 'Boolean',
        'Type' => 'Int',
        'OptionModifierKey' => 'Varchar(255)',
        'SortOrder' => 'Int',
        'FinalPrice' => 'Currency',
        'FinalWeight' => 'Decimal(9,3)',
        'FinalCode' => 'Varchar(255)',
        'IsDefault' => 'Boolean',
        'ReceiptTitle' => 'HTMLVarchar(255)',
        'QuantityMax' => 'Int',
    ];

    /**
     * @var string[]
     */
    private static $indexes = [
        'FinalPrice' => true,
        'FinalWeight' => true,
        'FinalCode' => true,
    ];

    /**
     * @var string[]
     */
    private static $has_one = [
        'VariationType' => VariationType::class,
        'Product' => Product::class,
    ];

    /**
     * @var array
     */
    private static $many_many = [
        'Images' => File::class,
    ];

    /**
     * @var \string[][]
     */
    private static $many_many_extraFields = [
        'Images' => [
            'SortOrder' => 'Int',
        ],
    ];

    /**
     * @var string[]
     */
    private static $owns = [
        'Images',
    ];

    /**
     * @var string[]
     */
    private static $default_sort = 'SortOrder';

    /**
     * The relation name was established before requests for videos.
     * The relation has subsequently been updated from Image::class to File::class
     * to allow for additional file types such as mp4
     *
     * @var array
     */
    private static $allowed_images_extensions = [
        'gif',
        'jpeg',
        'jpg',
        'png',
        'bmp',
        'ico',
        'mp4',
    ];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'Images',
                'WeightModifier',
                'CodeModifier',
                'PriceModifier',
                'WeightModifierAction',
                'CodeModifierAction',
                'PriceModifierAction',
                'Available',
                'Type',
                'OptionModifierKey',
                'SortOrder',
                'ProductID',
                'FinalPrice',
                'FinalWeight',
                'FinalCode',
                'IsDefault',
            ]);

            $fields->insertBefore(
                'Content',
                CheckboxField::create('Available')
                    ->setTitle('Available for purchase')
            );

            $fields->insertBefore(
                'Content',
                $fields->dataFieldByName('VariationTypeID')
            );

            $fields->insertBefore(
                'Content',
                $fields->dataFieldByName('UseProductContent')
            );

            $content = $fields->dataFieldByName('Content');

            $content->hideUnless('UseProductContent')->isNotChecked()->end();

            if ($this->exists()) {
                $fields->addFieldToTab(
                    'Root.ProductModifications',
                    ReadonlyField::create('OptionModifierKey')
                        ->setTitle(_t('Variation.ModifierKey', 'Modifier Key'))
                );
            }

            if ($this->Product()->hasDatabaseField('Weight')) {
                $fields->addFieldtoTab(
                    'Root.ProductModifications',
                    FieldGroup::create(
                        DropdownField::create(
                            'WeightModifierAction',
                            _t('Variation.WeightModifierAction', 'Weight Modification Type'),
                            [
                                'Add' => _t(
                                    'Variation.WeightAdd',
                                    'Add to Base Weight',
                                    'Add to weight'
                                ),
                                'Subtract' => _t(
                                    'Variation.WeightSubtract',
                                    'Subtract from Base Weight',
                                    'Subtract from weight'
                                ),
                                'Set' => _t('Variation.WeightSet', 'Set as a new Weight'),
                            ]
                        )
                            ->setEmptyString('')
                            ->setDescription(_t(
                                'Variation.WeightDescription',
                                'Does weight modify or replace base weight?'
                            )),
                        NumericField::create("WeightModifier")
                            ->setTitle(_t('Variation.WeightModifier', 'Weight'))
                            ->setScale(3)
                            ->setDescription(_t(
                                'Variation.WeightDescription',
                                'Only supports up to 3 decimal places'
                            ))->displayIf('WeightModifierAction')->isNotEmpty()->end(),
                        NumericField::create('FinalWeight')
                            ->setTitle('Final Modified Weight')
                            ->setDescription("Product's weight is {$this->Product()->Weight}")
                            ->performDisabledTransformation()
                    )->setTitle('Weight Modification')
                );
            }

            $fields->addFieldsToTab(
                'Root.ProductModifications',
                [
                    // Price Modifier Fields
                    //HeaderField::create('PriceHD', _t('Variation.PriceHD', 'Modify Price'), 4),
                    FieldGroup::create(
                        DropdownField::create(
                            'PriceModifierAction',
                            _t('Variation.PriceModifierAction', 'Price Modification Type'),
                            [
                                'Add' => _t(
                                    'Variation.PriceAdd',
                                    'Add to Base Price',
                                    'Add to price'
                                ),
                                'Subtract' => _t(
                                    'Variation.PriceSubtract',
                                    'Subtract from Base Price',
                                    'Subtract from price'
                                ),
                                'Set' => _t('Variation.PriceSet', 'Set as a new Price'),
                            ]
                        )
                            ->setEmptyString('')
                            ->setDescription(_t('Variation.PriceDescription', 'Does price modify or replace base price?')),
                        CurrencyField::create('PriceModifier')
                            ->setTitle(_t('Variation.PriceModifier', 'Price'))
                            ->displayIf('PriceModifierAction')->isNotEmpty()->end(),
                        CurrencyField::create('FinalPrice')
                            ->setTitle('Final Modified Price')
                            ->setDescription("Product's price is {$this->Product()->Price}")
                            ->performDisabledTransformation()
                    )->setTitle('Price Modifications'),

                    // Code Modifier Fields
                    //HeaderField::create('CodeHD', _t('Variation.CodeHD', 'Modify Code'), 4),
                    FieldGroup::create(
                        DropdownField::create(
                            'CodeModifierAction',
                            _t('Variation.CodeModifierAction', 'Code Modification Type'),
                            [
                                'Add' => _t(
                                    'Variation.CodeAdd',
                                    'Add to Base Code',
                                    'Add to code'
                                ),
                                'Subtract' => _t(
                                    'Variation.CodeSubtract',
                                    'Subtract from Base Code',
                                    'Subtract from code'
                                ),
                                'Set' => _t('Variation.CodeSet', 'Set as a new Code'),
                            ]
                        )
                            ->setEmptyString('')
                            ->setDescription(_t('Variation.CodeDescription', 'Does code modify or replace base code?')),
                        TextField::create('CodeModifier')
                            ->setTitle(_t('Variation.CodeModifier', 'Code'))
                            ->displayIf('CodeModifierAction')->isNotEmpty()->end(),
                        TextField::create('FinalCode')
                            ->setTitle('Final Modified Code')
                            ->setDescription("Product's code is {$this->Product()->Code}")
                            ->performDisabledTransformation()
                    )->setTitle('Code Modification'),
                ]
            );

            // Images tab
            $images = SortableUploadField::create('Images')
                ->setSortColumn('SortOrder')
                ->setIsMultiUpload(true)
                ->setAllowedExtensions($this->config()->get('allowed_images_extensions'))
                ->setFolderName('Uploads/Products/Images');

            $fields->addFieldsToTab('Root.Images', [
                $images,
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

        $modifierKeyField = 'OptionModifierKey';
        $this->{$modifierKeyField} = $this->getGeneratedValue();

        $codeModifierField = 'CodeModifier';
        $codeModifier = $this->{$codeModifierField};

        switch ($this->CodeModifierAction) {
            case 'Subtract':
            case 'Add':
                if (static::config()->get('code_trim_left_spaces')) {
                    //remove duplicate leading spaces
                    if (strpos($codeModifier, ' ') == 0) {
                        $codeModifier = ltrim($codeModifier);
                    }
                }
                if (static::config()->get('code_trim_right_space')) {
                    $codeModifier = rtrim($codeModifier);
                }
                break;
            case 'Set':
                //We must trim for Foxy
                $codeModifier = trim($codeModifier);
                break;
        }

        if (static::config()->get('code_enforce_single_spaces')) {
            //replace duplicate spaces
            $codeModifier = preg_replace('/\s+/', ' ', $codeModifier);
        }

        /**
         * This method supersedes configs code_trim_right_space and code_enforce_single_spaces
         */
        if (static::config()->get('code_remove_spaces')) {
            // replace duplicate spaces
            $codeModifier = preg_replace('/\s+/', '', $codeModifier);
        }

        //can be used for backwards compatibility (do your own logic)
        $this->extend('updateCodeModifier', $this, $codeModifier);

        $this->{$codeModifierField} = $codeModifier;

        $this->FinalPrice = $this->calculateFinalPrice();
        $this->FinalCode = $this->calculateFinalCode();

        if ($this->Product()->hasDatabaseField('Weight')) {
            $this->FinalWeight = $this->calculateFinalWeight();
        }
    }

    /**
     * @return ValidationResult
     */
    public function validate()
    {
        $validate = parent::validate();
        $product = $this->Product();

        if (!$this->Title) {
            $validate->addFieldError('Title', 'A title is required');
        }

        /*if (!$this->VariationTypeID) {
            $validate->addFieldError('VariationTypeID', 'A variation type is required');
        }//*/

        if ($this->PriceModifierAction == 'Subtract' && $this->PriceModifier > $product->Price) {
            $validate->addFieldError('PriceModifier', "You can't subtract more than the price of the product ({$product->Price})");
        }

        if ($this->WeightModifierAction == 'Subtract' && $this->WeightModifier > $product->Weight) {
            $validate->addFieldError('WeightModifier', "You can't subtract more than the weight of the product ({$product->Weight})");
        }

        return $validate;
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
    protected function getWeightModifierWithSymbol()
    {
        return $this->getOptionModifierActionSymbol($this->WeightModifierAction) . $this->WeightModifier;
    }

    /**
     * @return string
     */
    protected function getPriceModifierWithSymbol()
    {
        return $this->getOptionModifierActionSymbol($this->PriceModifierAction) . $this->PriceModifier;
    }

    /**
     * @return string
     */
    protected function getCodeModifierWithSymbol()
    {
        return $this->getOptionModifierActionSymbol($this->CodeModifierAction) . $this->CodeModifier;
    }

    /**
     * @return float
     */
    protected function calculateFinalPrice()
    {
        $product = $this->Product();// this relation is set by a developer's data extension

        if ($this->PriceModifierAction == 'Add') {
            return $this->PriceModifier + $product->Price;
        } elseif ($this->PriceModifierAction == 'Subtract') {
            return $product->Price - $this->PriceModifier;
        } elseif ($this->PriceModifierAction == 'Set') {
            return $this->PriceModifier;
        }

        return $product->Price;
    }

    /**
     * @return float
     */
    protected function calculateFinalWeight()
    {
        $product = $this->Product();// this relation is set by a developer's data extension

        if ($this->WeightModifierAction == 'Add') {
            return $this->WeightModifier + $product->Weight;
        } elseif ($this->WeightModifierAction == 'Subtract') {
            return $product->Weight - $this->WeightModifier;
        } elseif ($this->WeightModifierAction == 'Set') {
            return $this->WeightModifier;
        }

        return $product->Weight;
    }

    /**
     * @return string
     */
    protected function calculateFinalCode()
    {
        $product = $this->Product();// this relation is set by a developer's data extension

        if ($this->CodeModifierAction == 'Add') {
            return "{$product->Code}{$this->CodeModifier}";
        } elseif ($this->CodeModifierAction == 'Subtract') {
            return rtrim($product->Code, $this->CodeModifier);
        } elseif ($this->CodeModifierAction == 'Set') {
            return $this->CodeModifier;
        }

        return $product->Code;
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
    public function getIsAvailable()
    {
        $available = true;

        if (!$this->Available) {
            $available = false;
        }

        $this->extend('updateGetIsAvailable', $available);

        return $available;
    }

    /**
     * @return bool
     */
    public function getAvailability()
    {
        Deprecation::notice('1.4', 'Use getIsAvailable() instead');
        return $this->getIsAvailable();
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

        if ($this->IsDefault) {
            return false;
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

        if ($this->IsDefault) {
            return false;
        }

        return Permission::checkMember($member, 'MANAGE_FOXY_PRODUCTS');
    }
}
