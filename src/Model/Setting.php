<?php

namespace Dynamic\Foxy\Model;

use Dynamic\Foxy\Admin\FoxyAdmin;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;
use SilverStripe\View\TemplateGlobalProvider;

/**
 * Class Setting
 * @package Dynamic\Foxy\Model
 *
 * @property string StoreKey
 * @property bool EnableSidecart
 * @property string StoreTitle
 * @property string StoreDomain
 */
class Setting extends DataObject implements PermissionProvider, TemplateGlobalProvider
{
    /**
     * @var string
     */
    private static $singular_name = 'Foxy Setting';

    /**
     * @var string
     */
    private static $plural_name = 'Foxy Settings';

    /**
     * @var string
     */
    private static $description = 'Update the settings for your store';

    /**
     * @var string
     */
    private static $table_name = 'FoxySetting';

    /**
     * @var string
     */
    private static $keyPrefix = "dYnm1c";

    /**
     * @var array
     */
    private static $db = [
        'StoreKey' => 'Varchar(60)',
        'EnableSidecart' => 'Boolean',
    ];

    /**
     * @var array
     */
    private static $defaults = [
        'EnableSidecart' => 1,
    ];

    /**
     * Default permission to check for 'LoggedInUsers' to create or edit pages.
     *
     * @var array
     * @config
     */
    private static $required_permission = ['CMS_ACCESS_CMSMain', 'CMS_ACCESS_LeftAndMain'];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'StoreKey',
            ]);

            $fields->addFieldsToTab('Root.Main', [
                ReadonlyField::create('StoreDomain', 'Store Domain', FoxyHelper::config()->get('cart_url'))
                    ->setDescription('This is a unique FoxyCart subdomain for your cart, checkout, and receipt'),
                CheckboxField::create('CustomSSL', 'Use custom SSL', FoxyHelper::config()->get('custom_ssl'))
                    ->performReadonlyTransformation(),
            ]);

            if (FoxyHelper::config()->get('secret') != null) {
                $key = FoxyHelper::config()->get('secret');
                $description = 'Your secret key as set in config.yml';
            } else {
                $key = $this->StoreKey;
                $description = 'Recommended secret key for your Foxy store. Add to your config.yml to implement';
            }

            $fields->addFieldToTab(
                'Root.Main',
                ReadonlyField::create('Key', 'Store Key', $key)
                    ->setDescription($description)
            );

            if (self::store_name_warning() !== null) {
                $fields->addFieldToTab('Root.Main', LiteralField::create('StoreSubDomainHeaderWarning', _t(
                    'ProductPage.StoreSubDomainHeaderWarning',
                    '<p class="message error">Store domain must be entered in the
                        <a href="/admin/foxy/">Foxy settings</a></p>'
                )), 'StoreDomain');
            }

            $fields->addFieldsToTab(
                'Root.Options.Settings',
                [
                    CheckboxField::create('EnableSidecart', 'Enable Sidecart')
                        ->setDescription('Cart slides in from right side when product added to cart'),
                ]
            );

            $fields->addFieldsToTab(
                'Root.Options.Types',
                [
                    LiteralField::create('OptionGroupsDescrip', _t(
                        __CLASS__ . '.OptionGroupsDescrip',
                        '<p>Product Option Types allow you to group a set of product options by type.</p>'
                    )),
                    GridField::create(
                        'OptionType',
                        _t(__CLASS__ . '.OptionTypeLabel', 'Option Types'),
                        OptionType::get(),
                        GridFieldConfig_RecordEditor::create()
                    ),
                ]
            );

            $fields->addFieldsToTab(
                'Root.Options.VariationTypes',
                [
                    LiteralField::create('VariationDescrip', _t(
                        __CLASS__ . '.VariationDescrip',
                        '<p>Product Variation Types allow you to group a set of product variants by type.</p>'
                    )),
                    GridField::create(
                        'VariationType',
                        _t(__CLASS__ . '.VariationTypeLabel', 'Variation Types'),
                        VariationType::get(),
                        GridFieldConfig_RecordEditor::create()
                    ),
                ]
            );

            $fields->addFieldsToTab('Root.Categories', [
                LiteralField::create('CategoryDescrip', _t(
                    __CLASS__ . '.CategoryDescrip',
                    '<p>FoxyCart Categories offer a way to give products additional behaviors that cannot be
                        accomplished by product options alone, including category specific coupon codes,
                        shipping and handling fees, and email receipts.
                        <a href="https://wiki.foxycart.com/v/2.0/categories" target="_blank">
                            Learn More
                        </a></p>
                        <p>Categories you\'ve created in FoxyStripe must also be created in your
                            <a href="https://admin.foxycart.com/admin.php?ThisAction=ManageProductCategories"
                                target="_blank">FoxyCart Categories</a> admin panel.</p>'
                )),
                GridField::create(
                    'FoxyCategory',
                    _t(__CLASS__ . '.FoxyCategory', 'FoxyCart Categories'),
                    FoxyCategory::get(),
                    GridFieldConfig_RecordEditor::create()
                ),
            ]);
        });

        return parent::getCMSFields();
    }

    /**
     * @return FieldList
     */
    public function getCMSActions()
    {
        if (Permission::check('ADMIN') || Permission::check('EDIT_FOXY_SETTING')) {
            $actions = new FieldList(
                FormAction::create('save_foxy_setting', _t(static::class . '.SAVE', 'Save'))
                    ->addExtraClass('btn-primary font-icon-save')
            );
        } else {
            $actions = FieldList::create();
        }
        $this->extend('updateCMSActions', $actions);
        return $actions;
    }

    /**
     * @return null|string
     * @throws \SilverStripe\ORM\ValidationException
     */
    public static function store_name_warning()
    {
        $warning = null;
        $helper = FoxyHelper::create();
        if (!$helper->getStoreCartURL()) {
            $warning = 'Must define FoxyCart Store Domain in your config';
        }
        return $warning;
    }

    /**
     *
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        if (!self::current_foxy_setting()) {
            self::make_foxy_setting();
            DB::alteration_message('Added default FoxyStripe Setting', 'created');
        }
    }

    /**
     *
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->StoreKey) {
            $key = $this->generateStoreKey();
            while (!ctype_alnum($key)) {
                $key = $this->generateStoreKey();
            }
            $this->StoreKey = $key;
            DB::alteration_message('Created FoxyCart Store Key ' . $key, 'created');
        }
    }

    /**
     * @param int $count
     * @return string
     */
    public function generateStoreKey($count = 0)
    {
        $length = $this->obj('StoreKey')->getSize() - strlen($this->config()->get('keyPrefix'));
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' . strtotime('now');
        $strLength = strlen($charset);
        $str = '';

        while ($count < $length) {
            $str .= $charset[mt_rand(0, $strLength - 1)];
            $count++;
        }
        return $this->config()->get('keyPrefix') . substr(base64_encode($str), 0, $length);
    }

    /**
     * @return string
     */
    public function CMSEditLink()
    {
        return FoxyAdmin::singleton()->Link();
    }

    /**
     * @param \SilverStripe\Security\Member|null $member
     *
     * @return bool|int|null
     */
    public function canEdit($member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }
        $extended = $this->extendedCan('canEdit', $member);
        if ($extended !== null) {
            return $extended;
        }
        return Permission::checkMember($member, 'EDIT_FOXY_SETTING');
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            'EDIT_FOXY_SETTING' => [
                'name' => _t(
                    __CLASS__ . '.PERMISSION_MANAGE_FOXY_DESCRIPTION',
                    'Manage Foxy settings'
                ),
                'category' => _t(
                    __CLASS__ . '.PERMISSIONS_CATEGORY',
                    'Foxy'
                ),
                'help' => _t(
                    __CLASS__ . '.PERMISSION_MANAGE_FOXY_HELP',
                    'Ability to edit the settings of a Foxy Store'
                ),
                'sort' => 400,
            ],
        ];
    }

    /**
     * Get the current sites {@link GlobalSiteSetting}, and creates a new one
     * through {@link make_global_config()} if none is found.
     *
     * @return self|DataObject
     */
    public static function current_foxy_setting()
    {
        if ($config = self::get()->first()) {
            return $config;
        }
        return self::make_foxy_setting();
    }

    /**
     * Create {@link GlobalSiteSetting} with defaults from language file.
     *
     * @return self
     */
    public static function make_foxy_setting()
    {
        $config = self::create();
        try {
            $config->write();
        } catch (ValidationException $e) {
        }
        return $config;
    }

    /**
     * Add $GlobalConfig to all SSViewers.
     *
     * @return array
     */
    public static function get_template_global_variables()
    {
        return [
            'FoxyStripe' => 'current_foxy_setting',
        ];
    }
}
