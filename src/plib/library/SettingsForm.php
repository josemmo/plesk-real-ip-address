<?php
namespace PleskExt\RealIpAddress;

class SettingsForm extends \pm_Form_Simple {
    /**
     * Initialize form
     */
    public function init() {
        $enabledPresets = Settings::getEnabledPresets();
        $customProvider = Settings::getCustomProvider();
        $advanced = Settings::getAdvanced();

        // Add presets subform
        $presetsForm = new \pm_Form_SubForm();
        $presetsForm->setLegend('Presets');
        $presetsForm->addElement('description', 'description', [
            'description' => 'If your server is running behind one or more than one of these providers, just check their respective checkboxes to automatically configure and enable them.'
        ]);
        foreach (Settings::PRESETS as $preset=>$data) {
            $presetsForm->addElement('checkbox', $preset, [
                'label' => "Enable for {$data['name']}",
                'value' => in_array($preset, $enabledPresets)
            ]);
        }
        $this->addSubForm($presetsForm, 'presets');

        // Add custom provider subform
        $customForm = new \pm_Form_SubForm();
        $customForm->setLegend('Custom Provider');
        $customForm->addElement('description', 'description', [
            'description' => 'If your server is running behind a provider not natively supported by this extension, you can manually configure it from here.'
        ]);
        $customForm->addElement('checkbox', 'enabled', [
            'label' => 'Enable custom provider',
            'value' => $customProvider['enabled']
        ]);
        $customForm->addElement('textarea', 'ip_ranges', [
            'label' => 'Custom IP Ranges',
            'description' => 'In CIDR notation, one per line',
            'class' => 'f-max-size code js-auto-resize',
            'rows' => 6,
            'value' => implode("\n", $customProvider['ipRanges']),
            'required' => true
        ]);
        $this->addSubForm($customForm, 'custom');

        // Add advanced subform
        $advancedForm = new \pm_Form_SubForm();
        $advancedForm->setLegend('Advanced Settings');
        $advancedForm->addElement('description', 'description', [
            'description' => 'For experienced users only. Do not change any of these settings unless you know what you are doing.'
        ]);
        $advancedForm->addElement('text', 'header', [
            'label' => 'Header field',
            'description' => 'Header field containing the real IP address of the client',
            'placeholder' => 'E.g.: X-Forwarded-For',
            'value' => $advanced['header'],
            'required' => true
        ]);
        $advancedForm->addElement('checkbox', 'recursive', [
            'label' => 'Enable recursive mode',
            'description' => 'If recursive search is enabled, the original client address that matches one of the trusted addresses is replaced by the last non-trusted address sent in the request header field',
            'value' => $advanced['recursive']
        ]);
        $this->addSubForm($advancedForm, 'advanced');

        // Add save button
        $this->addControlButtons([
            'sendTitle' => 'Save and apply'
        ]);
    }


    /**
     * @inheritdoc
     */
    public function isValid($data) {
        $customForm = $this->getSubForm('custom');
        $advancedForm = $this->getSubForm('advanced');

        // Validate custom IP ranges
        if (!$data['custom']['enabled']) {
            $customForm->getElement('ip_ranges')->setRequired(false);
        }
        $customIpRanges = $data['custom']['ip_ranges'];
        if (!empty($customIpRanges) && !CidrUtils::validate($customIpRanges)) {
            $customForm->getElement('ip_ranges')->addError('Not a valid list of CIDR ranges.');
            return false;
        }

        // Validate header field
        if (!preg_match('/^[a-z0-9\-]+$/i', $data['advanced']['header'])) {
            $advancedForm->getElement('header')->addError('Not a valid header field.');
            return false;
        }

        // Validate rest of fields
        return parent::isValid($data);
    }


    /**
     * Process form
     */
    public function process() {
        $presetsForm = $this->getSubForm('presets');
        $customForm = $this->getSubForm('custom');
        $advancedForm = $this->getSubForm('advanced');

        // Get list of enabled presets
        $enabledPresets = [];
        foreach (array_keys(Settings::PRESETS) as $preset) {
            if ($presetsForm->getValue($preset)) {
                $enabledPresets[] = $preset;
            }
        }

        // Save settings
        Settings::setEnabledPresets($enabledPresets);
        Settings::setCustomProvider(
            (bool) $customForm->getValue('enabled'),
            CidrUtils::parse($customForm->getValue('ip_ranges'))
        );
        Settings::setAdvanced(
            $advancedForm->getValue('header'),
            (bool) $advancedForm->getValue('recursive')
        );
    }
}
