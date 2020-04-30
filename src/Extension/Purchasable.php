<?php

namespace Dynamic\Foxy\Extension;

use Dynamic\Foxy\Model\FoxyCategory;
use Dynamic\Foxy\Model\ProductOption;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Class Purchasable
 * @package Dynamic\Foxy\Extension
 *
 * @property double Price
 * @property string Code
 * @property string ReceiptTitle
 * @property bool Available
 *
 * @property int FoxyCategoryID
 * @method FoxyCategory FoxyCategory()
 *
 * @method \SilverStripe\ORM\ManyManyList Options()
 *
 * @property-read \SilverStripe\ORM\DataObject|Purchasable $owner
 */
class Purchasable extends DataExtension implements PermissionProvider
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
    private static $many_many = [
        'Options' => ProductOption::class,
    ];

    /**
     * @var array
     */
    private static $many_many_extraFields = [
        'Options' => [
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
        ],
        'OptionTypes' => [
            'SortOrder' => 'Int',
        ],
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
        $fields->removeByName([
            'SKU',
        ]);

        $fields->addFieldsToTab(
            'Root.Ecommerce',
            [
                CurrencyField::create('Price')
                    ->setDescription(_t(
                        __CLASS__ . '.PriceDescription',
                        'Base price for this product. Can be modified using Product Options'
                    )),
                TextField::create('Code')
                    ->setDescription(_t(
                        __CLASS__ . '.CodeDescription',
                        'Required, must be unique. Product identifier used by FoxyCart in transactions. All leading and trailing spaces are removed on save.'
                    )),
                DropdownField::create('FoxyCategoryID')
                    ->setTitle($this->owner->fieldLabel('FoxyCategoryID'))
                    ->setSource(FoxyCategory::get()->map())
                    ->setDescription(_t(
                        __CLASS__ . '.FoxyCategoryDescription',
                        'Required. Must also exist in
                        <a href="https://admin.foxycart.com/admin.php?ThisAction=ManageProductCategories"
                            target="_blank">
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
            ],
            'Content'
        );

        if ($this->owner->ID) {
            $config = GridFieldConfig_RelationEditor::create();
            $config
                ->addComponents([
                    new GridFieldOrderableRows('SortOrder'),
                    new GridFieldAddExistingSearchButton(),
                ])
                ->removeComponentsByType([
                    GridFieldAddExistingAutocompleter::class,
                ]);
            $options = GridField::create(
                'Options',
                'Options',
                $this->owner->Options()->sort('SortOrder'),
                $config
            );

            $fields->addFieldsToTab(
                'Root.Options',
                [
                    $options,
                ]
            );
        }

        $fields->addFieldsToTab(
            'Root.Inventory',
            [
                CheckboxField::create('Available')
                    ->setDescription(_t(
                        __CLASS__ . '.AvailableDescription',
                        'If unchecked, will remove "Add to Cart" form and instead display "Currently unavailable"'
                    )),
            ]
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
        ]);
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        if (!$this->owner->Available) {
            return false;
        }

        if (!$this->owner->Options()->exists()) {
            return true;
        }

        foreach ($this->owner->Options() as $option) {
            if ($option->Available) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isProduct()
    {
        return true;
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            'MANAGE_FOXY_PRODUCTS' => [
                'name' => _t(
                    __CLASS__ . '.PERMISSION_MANAGE_PRODUCTS_DESCRIPTION',
                    'Manage products'
                ),
                'category' => _t(
                    __CLASS__ . '.PERMISSIONS_CATEGORY',
                    'Foxy'
                ),
                'help' => _t(
                    __CLASS__ . '.PERMISSION_MANAGE_PRODUCTS_HELP',
                    'Manage products and related settings'
                ),
                'sort' => 400,
            ],
        ];
    }

    /**
     * @param $member
     * @return bool|int|void
     */
    public function canCreate($member)
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
    public function canEdit($member)
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
    public function canDelete($member)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        return Permission::checkMember($member, 'MANAGE_FOXY_PRODUCTS');
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canUnpublish($member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        return Permission::checkMember($member, 'MANAGE_FOXY_PRODUCTS');
    }

    /**
     * @param $member
     * @return bool|int
     */
    public function canArchive($member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        return Permission::checkMember($member, 'MANAGE_FOXY_PRODUCTS');
    }

    /**
     *
     */
    public function onBeforeWrite()
    {
        // trim spaces and replace duplicate spaces
        $trimmed = trim($this->owner->Code);
        $this->owner->Code = preg_replace('/\s+/', ' ', $trimmed);
    }
}
