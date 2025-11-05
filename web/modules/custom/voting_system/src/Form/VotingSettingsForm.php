<?php

namespace Drupal\voting_system\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class VotingSettingsForm extends ConfigFormBase
{
    protected function getEditableConfigNames(): array
    {
        return ['voting_system.settings'];
    }

    public function getFormId(): string
    {
        return 'voting_system_settings_form';
    }

    public function buildForm(array $form, FormStateInterface $formState): array
    {
        $config = $this->config('voting_system.settings');

        $form['allow_anonymous'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Habilitar votação para usuários anônimos'),
            '#default_value' => $config->get('allow_anonymous') ?? FALSE,
            '#description' => $this->t('Se marcado, usuários que não estão logados poderão votar.'),
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
