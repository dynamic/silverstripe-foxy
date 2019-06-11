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

class OptionType extends DataObject
{
    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)',
        'SortOrder' => 'Int',
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
            'WeightModifier' => 'Decimal',
            'CodeModifier' => 'Text',
            'PriceModifier' => 'Currency',
            'WeightModifierAction' => "Enum('Add,Subtract,Set', null)",
            'CodeModifierAction' => "Enum('Add,Subtract,Set', null)",
            'PriceModifierAction' => "Enum('Add,Subtract,Set', null)",
            'Available' => 'Boolean',
            'SortOrder' => 'Int',
        ],
    ];

    /**
     * @var string
     */
    private static $table_name = 'OptionType';

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'SortOrder',
                'ProductID',
                'Options',
            ]);

            if ($this->ID) {
                $config = GridFieldConfig_RelationEditor::create();
                $config
                    ->addComponents([
                        new GridFieldAddExistingSearchButton(),
                        new GridFieldOrderableRows('SortOrder'),
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
                $fields->addFieldToTab('Root.Main', $options);
            }
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
