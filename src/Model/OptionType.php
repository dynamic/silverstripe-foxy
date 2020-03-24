<?php

namespace Dynamic\Foxy\Model;

use Dynamic\Products\Page\Product;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Class OptionType
 * @package Dynamic\Foxy\Model
 *
 * @property string Title
 */
class OptionType extends DataObject
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
    private static $table_name = 'OptionType';

    /**
     * @var string
     */
    private static $default_sort = 'Title';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'SortOrder',
                'ProductID',
                'Options',
            ]);
        });

        return parent::getCMSFields();
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
}
