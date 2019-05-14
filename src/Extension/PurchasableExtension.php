<?php

namespace Dynamic\Foxy\Extension;

use Dynamic\Foxy\Form\AddToCartForm;
use Dynamic\Foxy\Model\FoxyHelper;
use SilverStripe\Core\Extension;
use SilverStripe\Dev\Debug;

class PurchasableExtension extends Extension
{
    /**
     * @var array
     */
    private static $allowed_actions = array(
        'AddToCartForm',
    );

    /**
     * @return FoxyStripePurchaseForm
     */
    public function AddToCartForm()
    {
        $form = AddToCartForm::create($this->owner, __FUNCTION__, null, null, null, $this->owner->data());

        $this->owner->extend('updateAddToCartForm', $form);

        Debug::show($this->getStoreURL());

        return $form;
    }

    /**
     * @return string
     */
    public function getStoreURL()
    {
        $helper = FoxyHelper::create();
        return $helper::StoreURL();
    }
}
