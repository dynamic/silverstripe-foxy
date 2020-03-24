<?php

namespace Dynamic\Foxy\Controller;

use Dynamic\Foxy\Form\AddToCartForm;
use Dynamic\Foxy\Model\FoxyHelper;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ValidationException;

/**
 * Class FoxyLinkGeneratorController
 */
class FoxyLinkGeneratorController extends Controller
{
    /**
     *
     */
    const URLSEGMENT = 'foxylinkgenerator'; // phpcs:ignore PSR12.Properties.ConstantVisibility.NotFound

    /**
     * @var array
     */
    private static $allowed_actions = [
        'index',
    ];

    /**
     * @return string
     */
    public function getURLSegment()
    {
        return self::URLSEGMENT;
    }

    /**
     * @return string
     * @throws ValidationException
     */
    public function index()
    {
        $request = $this->getRequest();

        if (!$request->getVar('code')) {
            return 'Code is required';
        }

        if (!$request->getVar('price')) {
            return 'Price is required';
        }

        if (!$request->getVar('name')) {
            return 'Name is required';
        }

        $code = $request->getVar('code');
        $props = [];
        foreach ($request->getVars() as $var => $val) {
            $props[$var] = AddToCartForm::getGeneratedValue($code, $var, $val, 'value');
        }

        $post_url = '';
        foreach ($props as $key => $value) {
            $post_url .= $key . '=' . $value . '&';
        }
        $post_url = rtrim($post_url, '&');

        echo FoxyHelper::StoreURL() . '/cart?' . $post_url;
    }
}
