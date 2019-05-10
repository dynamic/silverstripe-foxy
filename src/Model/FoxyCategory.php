<?php

namespace Dynamic\Foxy\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataObject;

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

        if (FoxyCategory::get()->filter('Code', $this->Code)->exclude('ID', $this->ID)->first()) {
            $result->addError('Code must be unique for each category.');
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
}
