<?php

namespace Dynamic\Foxy\Form;

use Dynamic\Foxy\Extension\Purchasable;
use Dynamic\Foxy\Extension\Shippable;
use Dynamic\Foxy\Model\Foxy;
use Dynamic\Foxy\Model\FoxyHelper;
use Dynamic\Foxy\Model\ProductOption;
use Dynamic\Foxy\Model\Setting;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\RequiredFields;

class AddToCartForm extends Form
{
    /**
     * @var
     */
    protected $foxy_setting;

    /**
     * @var
     */
    private $product;

    /**
     * @param $foxySetting
     *
     * @return $this
     */
    public function setFoxySetting($foxySetting)
    {
        $foxySetting = $foxySetting === null ? Setting::current_foxy_setting() : $foxySetting;
        if ($foxySetting instanceof Setting) {
            $this->foxy_setting = $foxySetting;
            return $this;
        }
        throw new \InvalidArgumentException('$foxySetting needs to be an instance of Foxy Setting.');
    }

    /**
     * @return FoxyStripeSetting
     */
    public function getFoxySetting()
    {
        if (!$this->foxy_setting) {
            $this->setFoxySetting(Setting::current_foxy_setting());
        }
        return $this->foxy_setting;
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
     * @return ProductPage
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * AddToCartForm constructor.
     *
     * @param ContentController $controller
     * @param string $name
     * @param FieldList|null $fields
     * @param FieldList|null $actions
     * @param null $validator
     * @param null $product
     * @param null $foxySetting
     *
     */
    public function __construct(
        $controller,
        $name,
        FieldList $fields = null,
        FieldList $actions = null,
        $validator = null,
        $product = null,
        $foxySetting = null
    ) {
        $this->setProduct($product);
        $this->setFoxySetting($foxySetting);

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
            $fields->push(
                $unavailable = HeaderField::create('unavailableText', 'Selection unavailable', 4)
                    ->addExtraClass('unavailable-text')
            );
            if (!empty(trim($this->foxy_setting->StoreDomain)) && $this->product->isAvailable()) {
                $unavailable->addExtraClass('hidden');
            }
        } else {
            $fields->push(HeaderField::create('unavailableText', 'currently unavaiable', 4));
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
        if (!empty(trim($this->foxy_setting->StoreDomain)) && $this->product->isAvailable()) {
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
        /** @var CompositeField $optionsSet */
        $optionsSet = CompositeField::create();

        $types = $this->product->OptionTypes();

        foreach ($types as $type) {
            $title = $type->Title;
            $fieldName = preg_replace('/\s/', '_', $title);
            $disabled = [];
            $fullOptions = [];

            foreach ($type->Options() as $option) {
                $option = $this->setAvailability($option);
                $name = self::getGeneratedValue(
                    $this->product->Code,
                    $type->Title,
                    $option->getGeneratedValue(),
                    'value'
                );

                $fullOptions[$name] = $option->getGeneratedTitle();
                if (!$option->Availability) {
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
     * @param OptionItem $option
     * @return OptionItem
     */
    protected function setAvailability(ProductOption $option)
    {
        $option->Available = ($option->getAvailability()) ? true : false;
        return $option;
    }
}
