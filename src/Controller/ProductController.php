<?php

namespace Dynamic\Foxy\Controller;

use Dynamic\Foxy\Form\AddToCartForm;
use Dynamic\Foxy\Model\FoxyHelper;
use Dynamic\Foxy\Model\Setting;
use SilverStripe\View\Requirements;

/**
 *
 */
class ProductController extends \PageController
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
    public function init()
    {
        parent::init();

        if ($this->hasMethod('getIsAvailable')) {
            if ($this->data()->getIsAvailable()) {
                if ($this->data()->Variations()->count()) {
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
        if ($this->data()->getIsAvailable()) {
            $form = AddToCartForm::create($this, __FUNCTION__, null, null, null, $this->data());
        } else {
            $form = false;
        }
        $this->extend('updateAddToCartForm', $form);

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
