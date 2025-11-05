<?php

namespace Drupal\voting_system\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Url;

class VotingQuestionListBuilder extends ConfigEntityListBuilder
{
    public function buildHeader(): array
    {
        $header['id'] = $this->t('ID');
        $header['identifier'] = $this->t('Identificador');
        $header['enabled'] = $this->t('Ativo');
        $header['operations'] = $this->t('Operações');

        return $header;
    }

    public function buildRow(EntityInterface $entity): array
    {
        $row['id'] = $entity->id();
        $row['identifier'] = $entity->get('identifier');
        $row['enabled'] = $entity->isEnabled() ? $this->t('Sim') : $this->t('Não');

        return $row + parent::buildRow($entity);
    }

    public function getOperations(EntityInterface $entity): array
    {
        $operations = parent::getOperations($entity);

        $operations['manage_options'] = [
            'title' => $this->t('Gerenciar opções'),
            'weight' => 20,
            'url' => Url::fromRoute('entity.voting_option.collection', [], [
            'query' => ['question_id' => $entity->id()],
            ]),
        ];

        return $operations;
    }

    public function render(): array
    {
        $build = parent::render();
        $build['#title'] = $this->t('Perguntas de Votação');
        return $build;
    }
}