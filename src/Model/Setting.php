<?php

namespace Dynamic\Foxy\Model;

use Dynamic\Foxy\Admin\FoxyAdmin;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\ReadonlyField;
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
 */
class Setting extends DataObject  implements PermissionProvider, TemplateGlobalProvider
{
    /**
     * @var string
     */
    private static $singular_name = 'FoxyStripe Setting';
    /**
     * @var string
     */
    private static $plural_name = 'FoxyStripe Settings';
    /**
     * @var string
     */
    private static $description = 'Update the settings for your store';

    /**
     * @var string
     */
    private static $table_name = 'FoxyStripeSetting';

    /**
     * @var string
     */
    private static $keyPrefix = "dYnm1c";

    /**
     * @var array
     */
    private static $db = [
        'StoreKey' => 'Varchar(60)',
        // TODO
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
            // TODO
            $fields->addFieldsToTab('Root.Main', [
            ]);

            $fields->addFieldsToTab('Root.Advanced', [
                ReadonlyField::create('StoreKey', 'Store Key', $this->StoreKey),
            ]);
        });

        return parent::getCMSFields();
    }

    /**
     * @return FieldList
     */
    public function getCMSActions()
    {
        if (Permission::check('ADMIN') || Permission::check('EDIT_FOXYSTRIPE_SETTING')) {
            $actions = new FieldList(
                FormAction::create('save_foxystripe_setting', _t('FoxyStripeSetting.SAVE', 'Save'))
                    ->addExtraClass('btn-primary font-icon-save')
            );
        } else {
            $actions = FieldList::create();
        }
        $this->extend('updateCMSActions', $actions);
        return $actions;
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        $config = self::current_foxy_setting();
        if (!$config) {
            $config = self::make_foxy_setting();
            DB::alteration_message('Added default FoxyStripe Setting', 'created');
        }
        if (!$config->StoreKey) {
            $key = $this->generateStoreKey();
            while (!ctype_alnum($key)) {
                $key = $this->generateStoreKey();
            }
            $config->StoreKey = $key;
            $config->write();
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
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'.strtotime('now');
        $strLength = strlen($charset);
        $str = '';

        while($count < $length){
            $str .= $charset[mt_rand(0, $strLength-1)];
            $count++;
        }
        return $this->config()->get('keyPrefix') . substr(base64_encode($str),0, $length);
    }

    /**
     * @return string
     */
    public function CMSEditLink()
    {
        return FoxyAdmin::singleton()->Link();
    }

    /**
     * @param null $member
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
     */
    public static function get_template_global_variables()
    {
        return [
            'FoxyStripe' => 'current_foxy_setting',
        ];
    }
}