<?php

namespace Drupal\voting_system\Entity;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

class VotingOptionListBuilder extends ConfigEntityListBuilder
{

    public function buildHeader()
    {
        $header['title'] = $this->t('Título');
        $header['question'] = $this->t('Pergunta');
        $header['votes_count'] = $this->t('Total de votos');
        $header['operations'] = $this->t('Operações');

        return $header + parent::buildHeader();
    }

    public function buildRow(EntityInterface $entity)
    {
        $row['title'] = $entity->label();
        $questionId = $entity->get('question_id');
        $question = \Drupal::entityTypeManager()->getStorage('voting_question')->load($questionId);
        $row['question'] = $question ? $question->label() : $this->t('Desconhecida');
        $row['votes_count'] = $entity->get('votes_count');
        $row['operations']['data'] = $this->buildOperations($entity);

        return $row + parent::buildRow($entity);
    }

    public function render()
    {
        $build = parent::render();

        if ($questionId = \Drupal::request()->query->get('question_id')) {
            $question = \Drupal::entityTypeManager()
                ->getStorage('voting_question')
                ->load($questionId);

            if ($question) {
                $build['#title'] = $this->t(
                    'Opções de Votação para: %question',
                    [
                        '%question' => $question->label()
                    ]
                );

                $build['add_link'] = [
                    '#type' => 'link',
                    '#title' => $this->t('Adicionar opção de votação'),
                    '#url' => Url::fromRoute('entity.voting_option.add_form', [], [
                        'query' => ['question_id' => $questionId]
                    ]),
                    '#attributes' => [
                        'class' => ['button', 'button--primary'],
                        '#weight' => -10,
                    ],
                ];
            }
        }

        return $build;
    }

    protected function getEntityIds()
    {
        $query = $this->getStorage()->getQuery();

        if ($questionId = \Drupal::request()->query->get('question_id')) {
            $query->condition('question_id', $questionId);
        }

        $query->sort($this->entityType->getKey('id'));

        if ($this->limit) {
            $query->pager($this->limit);
        }

        return $query->execute();
    }
}
