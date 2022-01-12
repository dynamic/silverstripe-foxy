<?php

namespace Dynamic\Foxy\Form;

use Dynamic\Foxy\Model\Setting;
use Dynamic\Products\Page\Product;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\NumericField;
use SilverStripe\View\Requirements;

/**
 * Class QuantityField
 * @package Dynamic\FoxyStripe\Form
 */
class QuantityField extends NumericField
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'newvalue',
    ];

    /**
     * @param array $properties
     * @return string
     */
    public function Field($properties = [])
    {
        Requirements::javascript('silverstripe/admin: thirdparty/jquery/jquery.min.js');
        Requirements::javascript('dynamic/silverstripe-foxy: client/dist/javascript/quantity.js');
        Requirements::css('dynamic/silverstripe-foxy: client/dist/css/quantityfield.css');

        $this->setAttribute('data-link', $this->Link('newvalue'));
        $this->setAttribute('data-code', $this->getProduct()->Code);
        $this->setAttribute('data-id', $this->getProduct()->ID);
        $this->setAttribute('id', 'quantity-toggle-field');
        if ($this->getProduct()->QuantityMax > 0) {
            $this->setAttribute('data-limit', $this->getProduct()->QuantityMax);
        }

        return parent::Field($properties);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->getForm()->getProduct();
    }

    /**
     * @param HTTPRequest $request
     * @return bool|string
     */
    public function newvalue(HTTPRequest $request)
    {
        $value = $request->getVar('value');
        if (!$value && $value != 0) {
            return '';
        }

        if (!$code = $request->getVar('code')) {
            return '';
        }

        $this->extend('updateQuantity', $value);

        $data = [
            'quantity' => $value,
            'quantityGenerated' => AddToCartForm::getGeneratedValue($code, 'quantity', $value, 'value'),
        ];

        if ($this->getProduct()->QuantityMax > 0) {
            $data['limit'] = $this->getProduct()->QuantityMax;
        }

        $this->extend('updateData', $data);

        return json_encode($data);
    }
}
