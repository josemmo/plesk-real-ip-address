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
        $presetsForm->setLegend($this->lmsg('presets'));
        $presetsForm->addElement('description', 'description', [
            'description' => $this->lmsg('presetsDesc')
        ]);
        foreach (Settings::PRESETS as $preset=>$data) {
            $presetsForm->addElement('checkbox', $preset, [
                'label' => $this->lmsg('enableForPreset', ['preset' => $data['name']]),
                'value' => in_array($preset, $enabledPresets)
            ]);
        }
        $this->addSubForm($presetsForm, 'presets');

        // Add custom provider subform
        $customForm = new \pm_Form_SubForm();
        $customForm->setLegend($this->lmsg('customProvider'));
        $customForm->addElement('description', 'description', [
            'description' => $this->lmsg('customProviderDesc')
        ]);
        $customForm->addElement('checkbox', 'enabled', [
            'label' => $this->lmsg('enableCustomProvider'),
            'value' => $customProvider['enabled']
        ]);
        $customForm->addElement('textarea', 'ip_ranges', [
            'label' => $this->lmsg('customIpRanges'),
            'description' => $this->lmsg('customIpRangesDesc'),
            'class' => 'f-max-size code js-auto-resize',
            'rows' => 6,
            'value' => implode("\n", $customProvider['ipRanges']),
            'required' => true
        ]);
        $this->addSubForm($customForm, 'custom');

        // Add advanced subform
        $advancedForm = new \pm_Form_SubForm();
        $advancedForm->setLegend($this->lmsg('advancedSettings'));
        $advancedForm->addElement('description', 'description', [
            'description' => $this->lmsg('advancedSettingsDesc')
        ]);
        $advancedForm->addElement('text', 'header', [
            'label' => $this->lmsg('headerField'),
            'description' => $this->lmsg('headerFieldDesc'),
            'placeholder' => $this->lmsg('headerFieldHelp'),
            'value' => $advanced['header'],
            'required' => true
        ]);
        $advancedForm->addElement('checkbox', 'recursive', [
            'label' => $this->lmsg('enableRecursiveMode'),
            'description' => $this->lmsg('enableRecursiveModeDesc'),
            'value' => $advanced['recursive']
        ]);
        $this->addSubForm($advancedForm, 'advanced');

        // Add save button
        $this->addControlButtons([
            'sendTitle' => $this->lmsg('saveAndApply')
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
            $customForm->getElement('ip_ranges')->addError($this->lmsg('notValidRange'));
            return false;
        }

        // Validate header field
        if (!preg_match('/^[a-z0-9\-]+$/i', $data['advanced']['header'])) {
            $advancedForm->getElement('header')->addError($this->lmsg('notValidHeaderField'));
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
