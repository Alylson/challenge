<?php

namespace Drupal\voting_system\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

class VotingQuestionListBuilder extends EntityListBuilder
{
    public function buildHeader(): array
    {
        $header['id'] = $this->t('ID');
        $header['title'] = $this->t('Title');
        $header['enabled'] = $this->t('Enabled');

        return $header + parent::buildHeader();
    }

    public function buildRow(EntityInterface $entity): array
    {
        $row['id'] = $entity->id();
        $row['title'] = $entity->label();
        $row['enabled'] = $entity->isEnabled() ? $this->t('Yes') : $this->t('No');

        return $row + parent::buildRow($entity);
    }
}
