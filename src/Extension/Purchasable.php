<?php

namespace Dynamic\Foxy\Extension;

use Dynamic\Foxy\Model\FoxyCategory;
use Dynamic\Foxy\Model\Variation;
use Dynamic\Foxy\Model\VariationType;
use micschk\GroupableGridfield\GridFieldGroupable;
use SilverStripe\Dev\Deprecation;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;

/**
 * Class Purchasable
 * @package Dynamic\Foxy\Extension
 *
 * @property double Price
 * @property string Code
 * @property string ReceiptTitle
 * @property bool Available
 * @property int $QuantityMax
 *
 * @property int FoxyCategoryID
 * @method FoxyCategory FoxyCategory()
 *
 * @method HasManyList Variations()
 *
 * @property-read DataObject|Purchasable $owner
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
        'QuantityMax' => 'Int',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'FoxyCategory' => FoxyCategory::class,
    ];

    /**
     * @var string[]
     */
    private static $has_many = [
        'Variations' => Variation::class,
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
     * @param array $labels
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
                        'Base price for this product. Can be modified using Product variations'
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

        if ($this->owner->exists()) {
            $variationsConfig = GridFieldConfig_RelationEditor::create()
                ->removeComponentsByType([
                    GridFieldAddExistingAutocompleter::class,
                    GridFieldPaginator::class,
                    GridFieldPageCount::class,
                    GridFieldSortableHeader::class,
                ])
                ->addComponents([
                    new GridFieldOrderableRows('SortOrder'),
                    new GridFieldTitleHeader(),
                    new GridFieldGroupable(
                        'VariationTypeID',    // The fieldname to set the Group
                        'Variation Type',   // A description of the function of the group
                        'none',         // A title/header for items without a group/unassigned
                        VariationType::get()->sort('SortOrder')->map()->toArray()
                    )
                ]);

            $fields->addFieldToTab(
                'Root.Variations',
                GridField::create(
                    'Variations',
                    'Variations',
                    $this->owner->Variations(),
                    $variationsConfig
                )
            );
        }

        $fields->addFieldsToTab(
            'Root.Inventory',
            [
                NumericField::create('QuantityMax')
                    ->setTitle('Maximum quantity allowed in the cart')
                    ->setDescription('For unlimited enter 0')
                    ->addExtraClass('stacked'),
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
    public function getIsAvailable()
    {
        $available = true;

        if (!$this->owner->Available) {
            $available = false;
        }

        if ($available && $this->owner->Variations()->count()) {
            $available = false;
            foreach ($this->owner->Variations() as $variation) {
                if ($variation->getIsAvailable()) {
                    $available = true;
                }
            }
        }

        $this->owner->extend('updateGetIsAvailable', $available);

        return $available;
    }

    /**
     * @return mixed
     */
    public function isAvailable()
    {
        Deprecation::notice('1.4', 'Use getIsAvailable() instead');
        return $this->getIsAvailable();
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
