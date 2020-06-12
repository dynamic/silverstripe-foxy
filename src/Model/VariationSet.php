<?php

namespace Dynamic\Foxy\Model;

use SilverStripe\ORM\DataObject;

/**
 * Class VariationSet
 * @package Dynamic\Foxy\Model
 */
class VariationSet extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'VariationSet';

    /**
     * @var string
     */
    private static $singular_name = 'Variation Set';

    /**
     * @var string
     */
    private static $plural_name = 'Variation Sets';

    /**
     * @var string[]
     */
    private static $has_one = [
        'Variation' => Variation::class,
        'VariationType' => VariationType::class,
    ];
}
