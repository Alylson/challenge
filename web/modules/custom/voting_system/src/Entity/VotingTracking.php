<?php

namespace Drupal\voting_system\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Voting Tracking entity.
 *
 * @ContentEntityType(
 *   id = "voting_tracking",
 *   label = @Translation("Voting Tracking"),
 *   base_table = "voting_trackings",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */
class VotingTracking extends ContentEntityBase {

  public static function baseFieldDefinitions(EntityTypeInterface $entityType)
  {
    $fields = parent::baseFieldDefinitions($entityType);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('ID do usuário'))
      ->setDescription(t('Usuário que votou.'))
      ->setSetting('target_type', 'user')
      ->setRequired(TRUE);

    $fields['question_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ID da Pergunta'))
      ->setDescription(t('ID da pergunta.'))
      ->setRequired(TRUE);

    $fields['option_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ID da Opção'))
      ->setDescription(t('ID da opção de votação que foi selecionada.'))
      ->setRequired(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Criado em'))
      ->setDescription(t('A data e hora em que o voto foi registrado.'));

    return $fields;
  }
}