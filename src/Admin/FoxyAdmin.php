<?php

namespace Dynamic\Foxy\Admin;

use Dynamic\Foxy\Model\Setting;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Director;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\ValidationException;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Versioned\RecursivePublishable;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;

/**
 * Class FoxyAdmin
 */
class FoxyAdmin extends LeftAndMain
{
    /**
     * @var string
     */
    private static $url_segment = 'foxy';

    /**
     * @var string
     */
    private static $url_rule = '/$Action/$ID/$OtherID';

    /**
     * @var int
     */
    private static $menu_priority = 0;

    /**
     * @var string
     */
    private static $menu_title = 'Foxy';

    /**
     * @var string
     */
    private static $menu_icon_class = 'font-icon-cog';

    /**
     * @var string
     */
    private static $tree_class = Setting::class;

    /**
     * @var array
     */
    private static $required_permission_codes = ['EDIT_FOXY_SETTING'];

    /**
     * Initialises the {@link TemplateConfigSetting} controller.
     */
    public function init()
    {
        parent::init();
        if (class_exists(SiteTree::class)) {
            Requirements::javascript('silverstripe/cms: client/dist/js/bundle.js');
        }
    }

    /**
     * @param int|null $id
     * @param \SilverStripe\Forms\FieldList|null $fields
     *
     * @return $this|Form
     */
    public function getEditForm($id = null, $fields = null)
    {
        $config = Setting::current_foxy_setting();
        $fields = $config->getCMSFields();
        // Tell the CMS what URL the preview should show
        $home = Director::absoluteBaseURL();
        $fields->push(new HiddenField('PreviewURL', 'Preview URL', $home));
        // Added in-line to the form, but plucked into different view by LeftAndMain.Preview.js upon load
        $fields->push($navField = new LiteralField(
            'SilverStripeNavigator',
            $this->getSilverStripeNavigator()
        ));
        $navField->setAllowHTML(true);
        // Retrieve validator, if one has been setup (e.g. via data extensions).
        if ($config->hasMethod('getCMSValidator')) {
            $validator = $config->getCMSValidator();
        } else {
            $validator = null;
        }
        $actions = $config->getCMSActions();
        $negotiator = $this->getResponseNegotiator();
        $form = Form::create(
            $this,
            'EditForm',
            $fields,
            $actions,
            $validator
        )->setHTMLID('Form_EditForm');
        $form->setValidationResponseCallback(function (ValidationResult $errors) use ($negotiator, $form) {
            $request = $this->getRequest();
            if ($request->isAjax() && $negotiator) {
                $result = $form->forTemplate();
                return $negotiator->respond($request, array(
                    'CurrentForm' => function () use ($result) {
                        return $result;
                    },
                ));
            }
        });
        $form->addExtraClass('flexbox-area-grow fill-height cms-content cms-edit-form');
        $form->setAttribute('data-pjax-fragment', 'CurrentForm');
        if ($form->Fields()->hasTabSet()) {
            $form->Fields()->findOrMakeTab('Root')->setTemplate('SilverStripe\\Forms\\CMSTabSet');
        }
        $form->setHTMLID('Form_EditForm');
        $form->loadDataFrom($config);
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));
        // Use <button> to allow full jQuery UI styling
        $actions = $actions->dataFields();
        if ($actions) {
            /** @var FormAction $action */
            foreach ($actions as $action) {
                $action->setUseButtonTag(true);
            }
        }
        $this->extend('updateEditForm', $form);
        return $form;
    }

    /**
     * Save the current sites {@link GlobalSiteSetting} into the database.
     *
     * @param array $data
     * @param Form  $form
     *
     * @return string
     * @throws ValidationException
     */
    public function save_foxy_setting($data, $form)
    {
        $settings = Setting::current_foxy_setting();
        $form->saveInto($settings);
        $settings->write();
        if ($settings->hasExtension(RecursivePublishable::class)) {
            $settings->publishRecursive();
        }
        $this->response->addHeader(
            'X-Status',
            rawurlencode(_t(LeftAndMain::class . '.SAVEDUP', 'Saved.'))
        );
        return $form->forTemplate();
    }

    /**
     * @param bool $unlinked
     *
     * @return ArrayList
     */
    public function Breadcrumbs($unlinked = false)
    {
        return new ArrayList(array(
            new ArrayData(array(
                'Title' => static::menu_title(),
                'Link' => $this->Link(),
            )),
        ));
    }
}
