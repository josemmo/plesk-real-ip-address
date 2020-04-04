<?php
use PleskExt\RealIpAddress\Service;
use PleskExt\RealIpAddress\SettingsForm;

class IndexController extends pm_Controller_Action {
    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        if (!pm_Session::getClient()->isAdmin()) {
            throw new pm_Exception('Permission denied');
        }

        $this->view->pageTitle = "Real IP Address";
        $this->view->tabs = [
            [
                'title' => 'Settings',
                'action' => 'form'
            ],
            [
                'title' => 'Preview',
                'action' => 'preview'
            ]
        ];
    }


    /**
     * Index action
     */
    public function indexAction() {
        $this->_forward('form');
    }


    /**
     * Form action
     */
    public function formAction() {
        $form = new SettingsForm();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            try {
                $form->process();
                Service::apply();
                $this->_status->addInfo('Changes successfully applied.');
            } catch (pm_Exception $e) {
                $this->_status->addError($e->getMessage());
            }
            $this->_helper->json([
                'redirect' => pm_Context::getBaseUrl()
            ]);
        }

        $this->view->form = $form;
    }


    /**
     * Preview action
     */
    public function previewAction() {
        $configuration = Service::getNginxConfiguration();
        if (empty($configuration)) {
            $configuration = "<<< No configuration being applied >>>";
        }

        $form = new pm_Form_Simple();
        $form->addElement('textarea', 'configuration', [
            'label' => 'nginx configuration',
            'value' => $configuration,
            'class' => 'f-max-size code js-auto-resize',
            'rows' => 10
        ]);
        $this->view->form = $form;
    }
}
