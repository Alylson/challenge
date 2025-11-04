<?php

namespace Drupal\voting_system\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class VotingSettingsForm extends ConfigFormBase
{
    protected function getEditableConfigNames()
    {
        return ['voting_system.settings'];
    }

    public function getFormId()
    {
        return 'voting_system_settings_form';
    }

    public function buildForm(array $form, FormStateInterface $formState)
    {
        $config = $this->config('voting_system.settings');

        $form['allow_anonymous'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Allow anonymous users to vote'),
            '#default_value' => $config->get('allow_anonymous') ?? FALSE,
        ];

        return parent::buildForm($form, $formState);
    }

    public function submitForm(array &$form, FormStateInterface $formState)
    {
        $this->config('voting_system.settings')
            ->set('allow_anonymous', $formState->getValue('allow_anonymous'))
            ->save();

        parent::submitForm($form, $formState);
    }
}
