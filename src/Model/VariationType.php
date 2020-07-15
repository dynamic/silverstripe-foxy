<?php

namespace Dynamic\Foxy\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

/**
 * Class VariationType
 * @package Dynamic\Foxy\Model
 */
class VariationType extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'VariationType';

    /**
     * @var string
     */
    private static $singular_name = 'Variation Type';

    /**
     * @var string
     */
    private static $plural_name = 'Variation Types';

    /**
     * @var string[]
     */
    private static $db = [
        'Title' => 'Varchar',
        'SortOrder' => 'Int',
    ];

    /**
     * @var string[]
     */
    private static $has_many = [
        'Variations' => Variation::class,
    ];

    /**
     * @var string
     */
    private static $default_sort = 'SortOrder';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'VariationSets',
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
