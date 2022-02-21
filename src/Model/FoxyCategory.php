<?php

namespace Dynamic\Foxy\Model;

use Dynamic\Foxy\Page\Product;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

/**
 * Class FoxyCategory
 * @package Dynamic\Foxy\Model
 *
 * @property string Title
 * @property string Code
 */
class FoxyCategory extends DataObject
{
    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)',
        'Code' => 'Varchar(50)',
    ];

    /**
     * @var string[]
     */
    private static $has_many = [
        'Products' => Product::class,
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title' => 'Name',
        'Code' => 'Code',
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
     * @var string
     */
    private static $table_name = 'FoxyCategory';

    /**
     * @param bool $includerelations
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);

        $labels['Title'] = _t(__CLASS__ . '.TitleLabel', 'Title');
        $labels['Code'] = _t(__CLASS__ . '.CodeLabel', 'Code');

        return $labels;
    }

    /**
     * @return FieldList|void
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            if ($this->ID) {
                if ($this->Code == 'DEFAULT') {
                    $fields->replaceField(
                        'Title',
                        ReadonlyField::create('Title')
                    );

                    $fields->replaceField(
                        'Code',
                        ReadonlyField::create('Code')
                    );
                }
            }
        });

        return parent::getCMSFields();
    }

    /**
     * @return \SilverStripe\ORM\ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();

        if (!$this->Code) {
            $result->addError(
                _t(__CLASS__ . '.CodeRequired', 'You must set a product price in the Foxy tab')
            );
        }

        if (FoxyCategory::get()->filter('Code', $this->Code)->exclude('ID', $this->ID)->first()) {
            $result->addError(
                _t(__CLASS__ . '.CodeUnique', 'Code must be unique for each category.')
            );
        }

        return $result;
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        $allCats = self::get();
        if (!$allCats->count()) {
            $cat = new self();
            $cat->Title = 'Default';
            $cat->Code = 'DEFAULT';
            $cat->write();
        }
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
