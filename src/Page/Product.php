<?php

namespace Dynamic\Foxy\Page;

use Dynamic\Foxy\Controller\ProductController;
use Dynamic\Foxy\Model\FoxyCategory;
use Dynamic\Foxy\Model\Variation;
use Dynamic\Foxy\Model\VariationType;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;

/**
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
 */
class Product extends \Page
{
    /**
     * @var string
     */
    private static $table_name = 'FoxyProduct';

    /**
     * Force default variant's title to match the Product page's title if the variant reset is called.
     *
     * @var bool
     */
    private static $force_default_variant_title_update = false;

    /**
     * @var array
     */
    private static $db = [
        'Price' => 'Currency(9,4)',
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
        //'Image.CMSThumbnail',
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
     * @param $includeRelations
     * @return array
     */
    public function fieldLabels($includeRelations = true)
    {
        $labels = parent::fieldLabels($includeRelations);

        $labels['Title'] = _t(__CLASS__ . '.TitleLabel', 'Product Name');
        $labels['Code'] = _t(__CLASS__ . '.CodeLabel', 'Code');
        $labels['Price'] = _t(__CLASS__ . '.PriceLabel', 'Price');
        $labels['Price.Nice'] = _t(__CLASS__ . '.PriceLabel', 'Price');
        $labels['Available'] = _t(__CLASS__ . '.AvailableLabel', 'Available for purchase');
        $labels['Available.Nice'] = _t(__CLASS__ . '.AvailableLabelNice', 'Available');
        //$labels['Image.CMSThumbnail'] = _t(__CLASS__ . '.ImageLabel', 'Image');
        $labels['ReceiptTitle'] = _t(__CLASS__ . '.ReceiptTitleLabel', 'Product title for receipt');
        $labels['FoxyCategoryID'] = _t(__CLASS__ . '.FoxyCategoryLabel', 'Foxy Category');

        return $labels;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'SKU',
            ]);

            $fields->addFieldsToTab(
                'Root.Ecommerce',
                [
                    TextField::create('Price')
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
                        ->setTitle($this->fieldLabel('FoxyCategoryID'))
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

            if ($this->exists()) {
                $variationsConfig = GridFieldConfig_RelationEditor::create()
                    ->removeComponentsByType([
                            GridFieldAddExistingAutocompleter::class,
                            GridFieldPaginator::class,
                            GridFieldPageCount::class,
                            GridFieldSortableHeader::class,
                            GridFieldDeleteAction::class,
                        ])
                    ->addComponents([
                            new GridFieldOrderableRows('SortOrder'),
                            new GridFieldTitleHeader(),
                            new GridFieldDeleteAction(),
                        ]);

                $fields->addFieldToTab(
                    'Root.Variations',
                    GridField::create(
                        'Variations',
                        'Variations',
                        $this->Variations(),
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
        });

        return parent::getCMSFields();
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

        if (!$this->Available) {
            $available = false;
        }

        if ($available && $this->Variations()->count()) {
            $available = false;
            foreach ($this->Variations() as $variation) {
                if ($variation->Available) {
                    $available = true;
                }
            }
        }

        $this->extend('updateGetIsAvailable', $available);

        return $available;
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
     * @param $context
     * @return bool|int
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
     * @return bool|int
     */
    public function canEdit($member = null)
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
    public function canDelete($member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        return Permission::checkMember($member, 'MANAGE_FOXY_PRODUCTS');
    }

    /**
     * @param $member
     * @return bool|int|mixed|null
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
     * @return void
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // trim spaces and replace duplicate spaces
        $trimmed = trim((string) $this->Code);
        $this->Code = preg_replace('/\s+/', ' ', $trimmed);
    }

    /**
     * @return void
     * @throws ValidationException
     */
    protected function onAfterWrite()
    {
        parent::onAfterWrite();

        if (!$this->getDefaultVariation() || $this->baseProductChange()) {
            $this->resetDefaultVariant();
        }
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return ProductController::class;
    }

    /**
     * @return bool
     */
    protected function baseProductChange()
    {
        return $this->isChanged('Code');
    }

    /**
     * @return DataObject|null
     */
    public function getDefaultVariation()
    {
        return $this->Variations()->filter('IsDefault', true)->first();
    }

    /**
     * @return void
     * @throws ValidationException
     */
    protected function resetDefaultVariant()
    {
        if (!$variant = $this->getDefaultVariation()) {
            $variant = Variation::create();

            foreach (Variation::singleton()->config()->get('default_variation_mapping') as $productField => $variationField) {
                $variant->$variationField = $this->$productField;
            }

            $variant->ProductID = $this->ID;
            $variant->IsDefault = true;

            $variant->write();
        }

        if (!$variant->Title || $this->config()->get('force_default_variant_title_update')) {
            $variant->Title = $this->Title;
        }

        $variant->CodeModifier = $this->Code;
        $variant->CodeModifierAction = 'Set';

        $variant->write();
    }
}
