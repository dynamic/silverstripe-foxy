<?php

namespace Dynamic\Foxy\Extension;

use Dynamic\Foxy\Form\AddToCartForm;
use Dynamic\Foxy\Model\FoxyHelper;
use Dynamic\Foxy\Model\Setting;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Class PurchasableExtension
 * @package Dynamic\Foxy\Extension
 */
class PurchasableExtension extends Extension
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'AddToCartForm',
    ];

    /**
     *
     */
    public function onAfterInit()
    {
        // todo - can we rework as to not call getIsAvailable()? It's resource intensive
        if ($this->owner->hasMethod('getIsAvailable')) {
            if ($this->owner->data()->getIsAvailable()) {
                if ($this->owner->data()->Variations()->count()) {
                    Requirements::javascript('silverstripe/admin: thirdparty/jquery/jquery.js');
                    Requirements::javascript('dynamic/silverstripe-foxy: client/dist/javascript/product_options.js');
                }
            }
        }

        $config = Setting::current_foxy_setting();
        $helper = FoxyHelper::create();

        if ($config->EnableSidecart) {
            Requirements::javascript(
                "https://cdn.foxycart.com/" . $helper->getStoreCartURL() . "/loader.js",
                [
                    "async" => true,
                    "defer" => true,
                ]
            );
        }
    }

    /**
     * @return AddToCartForm
     */
    public function AddToCartForm()
    {
        $form = AddToCartForm::create($this->owner, __FUNCTION__, null, null, null, $this->owner->data());

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
