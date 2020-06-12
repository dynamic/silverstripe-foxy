<?php

namespace Dynamic\Foxy\Model;

use Bummzack\SortableFile\Forms\SortableUploadField;
use Dynamic\Products\Page\Product;
use SilverStripe\Assets\File;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;

/**
 * Class Variation
 * @package Dynamic\Foxy\Model
 */
class Variation extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'Variation';

    /**
     * @var string
     */
    private static $singular_name = 'Variation';

    /**
     * @var string
     */
    private static $plural_name = 'Variations';

    /**
     * @var string[]
     */
    private static $db = [
        'Title' => 'Varchar(255)',
        'Content' => 'HTMLText',
        'WeightModifier' => 'Decimal(9,3)',
        'CodeModifier' => 'Text',
        'PriceModifier' => 'Currency',
        'WeightModifierAction' => "Enum('Add,Subtract,Set', null)",
        'CodeModifierAction' => "Enum('Add,Subtract,Set', null)",
        'PriceModifierAction' => "Enum('Add,Subtract,Set', null)",
        'Available' => 'Boolean',
        'Type' => 'Int',
        'OptionModifierKey' => 'Varchar(255)',
        'SortOrder' => 'Int',
    ];

    /**
     * @var array
     */
    private static $many_many = [
        'Images' => File::class,
    ];

    /**
     * @var \string[][]
     */
    private static $many_many_extraFields = [
        'Images' => [
            'SortOrder' => 'Int',
        ],
    ];

    /**
     * @var string[]
     */
    private static $owns = [
        'Images',
    ];

    /**
     * The relation name was established before requests for videos.
     * The relation has subsequently been updated from Image::class to File::class
     * to allow for additional file types such as mp4
     *
     * @var array
     */
    private static $allowed_images_extensions = [
        'gif',
        'jpeg',
        'jpg',
        'png',
        'bmp',
        'ico',
        'mp4',
    ];


    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            // Images tab
            $images = SortableUploadField::create('Images')
                ->setSortColumn('SortOrder')
                ->setIsMultiUpload(true)
                ->setAllowedExtensions($this->config()->get('allowed_images_extensions'))
                ->setFolderName('Uploads/Products/Images');

            $fields->addFieldsToTab('Root.Images', [
                $images,
            ]);
        });

        return parent::getCMSFields();
    }
}
