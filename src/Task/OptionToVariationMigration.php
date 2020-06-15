<?php

namespace Dynamic\Foxy\Task;

use Dynamic\Foxy\Model\FoxyHelper;
use Dynamic\Foxy\Model\OptionType;
use Dynamic\Foxy\Model\Variation;
use Dynamic\Foxy\Model\VariationType;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\ValidationException;

class OptionToVariationMigration extends BuildTask
{
    /**
     * @var string
     */
    protected $title = 'Foxy - Option to Variation Migration';

    /**
     * @var string
     */
    private static $segment = 'foxy-option-to-variation-migration';

    /**
     * @var array
     */
    private $variation_fields = [
        'Title',
        'WeightModifier',
        'CodeModifier',
        'PriceModifier',
        'WeightModifierAction',
        'CodeModifierAction',
        'PriceModifierAction',
        'Available',
        'OptionModifierKey',
        'SortOrder',
    ];

    /**
     * @var array
     */
    private $type_map = [];

    public function run($request)
    {
        foreach ($this->yieldSingle(FoxyHelper::singleton()->getProducts()) as $product) {
            foreach ($this->yieldSingle($product->Options()) as $option) {
                $this->createProductVariation($product, $option);
            }
        }
    }

    /**
     * @return \Generator
     */
    protected function yieldSingle($list)
    {
        foreach ($list as $item) {
            yield $item;
        }
    }

    /**
     * @param $product
     * @param $option
     * @return int
     * @throws ValidationException
     */
    protected function createProductVariation($product, $option)
    {
        $variation = Variation::create();

        foreach ($this->yieldSingle($this->variation_fields) as $fieldName) {
            $variation->{$fieldName} = $option->{$fieldName};
        }

        $variation->VariationTypeID = $this->findOrMakeVariationType($option);
        $variation->ProductID = $product->ID;

        return $variation->write();
    }

    /**
     * @param $option
     * @return mixed
     * @throws ValidationException
     */
    protected function findOrMakeVariationType($option)
    {
        if (!array_key_exists($option->Type, $this->type_map)) {
            $optionType = OptionType::get()->byID($option->Type);
            $variationType = VariationType::create();
            $variationType->Title = $optionType->Title;
            $variationType->write();

            $this->type_map[$optionType->ID] = $variationType->ID;
        }

        return $this->type_map[$optionType->ID];
    }
}
