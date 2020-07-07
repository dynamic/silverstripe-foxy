<?php

namespace Dynamic\Foxy\Form;

use Dynamic\Foxy\Extension\Shippable;
use Dynamic\Foxy\Model\FoxyHelper;
use Dynamic\Foxy\Model\OptionType;
use Dynamic\Foxy\Model\ProductOption;
use Dynamic\Products\Page\Product;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\GroupedList;

/**
 * Class AddToCartForm
 * @package Dynamic\Foxy\Form
 */
class AddToCartForm extends Form
{
    /**
     * @var
     */
    protected $helper;

    /**
     * @var
     */
    private $product;

    /**
     * @param $helper
     *
     * @return $this
     */
    public function setFoxyHelper($helper)
    {
        $helper = $helper === null ? FoxyHelper::create() : $helper;
        if ($helper instanceof FoxyHelper) {
            $this->helper = $helper;

            return $this;
        }
        throw new \InvalidArgumentException('$helper needs to be an instance of FoxyHelper.');
    }

    /**
     * @return FoxyHelper
     */
    public function getFoxyHelper()
    {
        if (!$this->helper) {
            $this->setFoxyHelper(FoxyHelper::create());
        }

        return $this->helper;
    }

    /**
     * @param $product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        if ($product->isProduct()) {
            $this->product = $product;

            return $this;
        }
        throw new \InvalidArgumentException('$product needs to implement a Foxy DataExtension.');
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * AddToCartForm constructor.
     * @param $controller
     * @param $name
     * @param FieldList|null $fields
     * @param FieldList|null $actions
     * @param null $validator
     * @param null $product
     * @param null $helper
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function __construct(
        $controller,
        $name,
        FieldList $fields = null,
        FieldList $actions = null,
        $validator = null,
        $product = null,
        $helper = null
    ) {
        $this->setProduct($product);
        $this->setFoxyHelper($helper);

        $fields = ($fields != null && $fields->exists()) ?
            $this->getProductFields($fields) :
            $this->getProductFields(FieldList::create());

        $actions = ($actions != null && $actions->exists()) ?
            $this->getProductActions($actions) :
            $this->getProductActions(FieldList::create());

        $validator = (!empty($validator) || $validator != null) ? $validator : RequiredFields::create();

        parent::__construct($controller, $name, $fields, $actions, $validator);

        //have to call after parent::__construct()
        $this->setAttribute('action', FoxyHelper::FormActionURL());
        $this->disableSecurityToken();
        $this->setHTMLID($this->getTemplateHelper()->generateFormID($this) . "_{$product->ID}");
    }

    /**
     * @param FieldList $fields
     *
     * @return FieldList
     */
    protected function getProductFields(FieldList $fields)
    {
        $hiddenTitle = ($this->product->ReceiptTitle) ?
            htmlspecialchars($this->product->ReceiptTitle) :
            htmlspecialchars($this->product->Title);
        $code = $this->product->Code;

        if ($this->product->isAvailable()) {
            $fields->push(
                HiddenField::create('name')
                    ->setValue(
                        self::getGeneratedValue($code, 'name', $hiddenTitle, 'value')
                    )
            );

            $fields->push(
                HiddenField::create('category')
                    ->setValue(
                        self::getGeneratedValue($code, 'category', $this->product->FoxyCategory()->Code, 'value')
                    )
            );

            $fields->push(
                HiddenField::create('code')
                    ->setValue(
                        self::getGeneratedValue($code, 'code', $this->product->Code, 'value')
                    )
            );

            $fields->push(
                HiddenField::create('product_id')
                    ->setValue(
                        self::getGeneratedValue($code, 'product_id', $this->product->ID, 'value')
                    )
                    ->setName('h:product_id')
            );

            $fields->push(
                HiddenField::create('price')
                    ->setValue(
                        self::getGeneratedValue($code, 'price', $this->product->Price, 'value')
                    )
            );

            if ($this->product->hasMethod('AbsoluteLink')) {
                $fields->push(
                    HiddenField::create('url')
                        ->setValue(
                            self::getGeneratedValue($code, 'url', $this->product->AbsoluteLink(), 'value')
                        )
                );
            }

            if ($this->product->hasExtension(Shippable::class)) {
                if ($this->product->Weight > 0) {
                    $fields->push(
                        HiddenField::create('weight')
                            ->setValue(
                                self::getGeneratedValue($code, 'weight', $this->product->Weight, 'value')
                            )
                    );
                }
            }

            $image = null;
            if ($this->product->hasMethod('getImage')) {
                if ($this->product->getImage()) {
                    $image = $this->product->getImage()->CMSThumbnail()->absoluteURL;
                }
                if ($image) {
                    $fields->push(
                        HiddenField::create('image')
                            ->setValue(
                                self::getGeneratedValue($code, 'image', $image, 'value')
                            )
                    );
                }
            }

            $optionsSet = $this->getProductOptionSet();
            $fields->push($optionsSet);
            $quantityMax = (FoxyHelper::config()->get('max_quantity' != null)) ?
                FoxyHelper::config()->get('MaxQuantity') :
                10;
            $fields->push(QuantityField::create('x:visibleQuantity')->setTitle('Quantity')->setValue(1));
            $fields->push(
                HiddenField::create('quantity')
                    ->setValue(
                        self::getGeneratedValue($code, 'quantity', 1, 'value')
                    )
            );

            $fields->push(
                HeaderField::create('submitPrice', '$' . $this->product->Price, 4)
                    ->addExtraClass('submit-price')
            );
        }

        $this->extend('updateProductFields', $fields);

        return $fields;
    }

    /**
     * @param FieldList $actions
     *
     * @return FieldList
     */
    protected function getProductActions(FieldList $actions)
    {
        if (!empty(trim($this->helper->getStoreCartURL())) && $this->product->isAvailable()) {
            $actions->push(
                FormAction::create(
                    'x:submit',
                    _t(__CLASS__ . '.AddToCart', 'Add to Cart')
                )
                    ->addExtraClass('fs-add-to-cart-button')
                    ->setName('x:submit')
            );
        }

        $this->extend('updateProductActions', $actions);

        return $actions;
    }

    /**
     * @param null $productCode
     * @param null $optionName
     * @param null $optionValue
     * @param string $method
     * @param bool $output
     * @param bool $urlEncode
     *
     * @return null|string
     */
    // todo - Purchasable Extension or AddToCartForm? protected in Form
    public static function getGeneratedValue(
        $productCode = null,
        $optionName = null,
        $optionValue = null,
        $method = 'name',
        $output = false,
        $urlEncode = false
    ) {
        $optionName = ($optionName !== null) ? preg_replace('/\s/', '_', $optionName) : $optionName;
        $helper = FoxyHelper::create();

        return $helper::fc_hash_value($productCode, $optionName, $optionValue, $method, $output, $urlEncode);
    }

    /**
     * @return CompositeField
     */
    protected function getProductOptionSet()
    {
        $options = $this->product->Options()->sort('SortOrder');
        $groupedOptions = new GroupedList($options);
        $groupedBy = $groupedOptions->groupBy('Type');

        /** @var CompositeField $optionsSet */
        $optionsSet = CompositeField::create();

        /** @var DataList $set */
        foreach ($groupedBy as $id => $set) {
            $group = OptionType::get()->byID($id);
            $title = $group->Title;
            $fieldName = preg_replace('/\s/', '_', $title);
            $disabled = [];
            $fullOptions = [];

            foreach ($set as $item) {
                $item = $this->setAvailability($item);
                $name = self::getGeneratedValue(
                    $this->product->Code,
                    $group->Title,
                    $item->getGeneratedValue(),
                    'value'
                );

                $fullOptions[$name] = $item->getGeneratedTitle();
                if (!$item->Availability) {
                    array_push($disabled, $name);
                }
            }

            $optionsSet->push(
                $dropdown = DropdownField::create($fieldName, $title, $fullOptions)->setTitle($title)
            );

            if (!empty($disabled)) {
                $dropdown->setDisabledItems($disabled);
            }

            $dropdown->addExtraClass("product-options");
        }

        $optionsSet->addExtraClass('foxycartOptionsContainer');

        return $optionsSet;
    }

    /**
     * @param ProductOption $option
     * @return ProductOption
     */
    protected function setAvailability(ProductOption $option)
    {
        $option->Available = ($option->getAvailability()) ? true : false;

        return $option;
    }
}
