<?php

namespace Dynamic\Foxy\Extension;

use Dynamic\Foxy\Form\AddToCartForm;
use Dynamic\Foxy\Model\FoxyHelper;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class PurchasableExtension extends Extension
{
    /**
     * @var array
     */
    private static $allowed_actions = array(
        'AddToCartForm',
    );

    public function onAfterInit()
    {
        if ($this->owner->data()->isAvailable()) {
            if ($this->owner->data()->OptionTypes()->exists()) {
                Requirements::javascript('silverstripe/admin: thirdparty/jquery/jquery.js');
                Requirements::javascript('dynamic/silverstripe-foxy: client/dist/javascript/product_options.js');
            }
            Requirements::customScript(<<<JS
		        var productID = {$this->owner->data()->ID};
JS
            );
        }
    }

    /**
     * @return FoxyStripePurchaseForm
     */
    public function AddToCartForm()
    {
        if ($this->owner->data()->isAvailable()) {
            $form = AddToCartForm::create($this->owner, __FUNCTION__, null, null, null, $this->owner->data());
        } else {
            $form = false;
        }
        $this->owner->extend('updateAddToCartForm', $form);
        return $form;
    }

    /**
     * @return string
     */
    public function StoreURL()
    {
        $helper = FoxyHelper::create();
        return $helper::StoreURL();
    }
}
