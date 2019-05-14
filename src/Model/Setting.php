<?php

namespace Dynamic\Foxy\Model;

use Dynamic\Foxy\Admin\FoxyAdmin;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
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
 * @property string $StoreKey
 * @property string $StoreTitle
 * @property string $StoreDomain
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
     * @return mixed|null
     * @throws \SilverStripe\ORM\ValidationException
     */
    public static function getStoreKey()
    {
        if ($storeKey = FoxyHelper::config()->get('StoreKey')) {
            return $storeKey;
        }
        return false;
    }

    /**
     * @return mixed|null
     * @throws \SilverStripe\ORM\ValidationException
     */
    public static function getStoreDomain()
    {
        if ($storeDomain = FoxyHelper::config()->get('cart_url')) {
            return $storeDomain;
        }
        return false;
    }

    /**
     * @return null|string
     * @throws \SilverStripe\ORM\ValidationException
     */
    // todo - move to Setting
    public static function store_name_warning()
    {
        $warning = null;
        if (!self::getStoreDomain()) {
            $warning = 'Must define FoxyCart Store Name or Store Remote Domain in your site settings in the cms';
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
                    static::class . '.EDIT_FOXY_SETTING',
                    'Manage FoxyStripe settings'
                ),
                'category' => _t(
                    static::class . '.PERMISSIONS_FOXY_SETTING',
                    'FoxyStripe'
                ),
                'help' => _t(
                    static::class . '.EDIT_PERMISSION_FOXY_SETTING',
                    'Ability to edit the settings of a FoxyStripe Store.'
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
