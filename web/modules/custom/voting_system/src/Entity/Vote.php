<?php

namespace Drupal\voting_system\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Vote entity.
 *
 * @ContentEntityType(
 *   id = "voting_vote",
 *   label = @Translation("Vote"),
 *   base_table = "voting_votes",
 *   admin_permission = "administer votes",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid"
 *   },
 *   handlers = {
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler"
 *   }
 * )
 */
class Vote extends ContentEntityBase {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
  {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setSetting('target_type', 'user')
      ->setRequired(FALSE);

    $fields['ip_address'] = BaseFieldDefinition::create('string')
      ->setLabel(t('IP Address'))
      ->setRequired(FALSE);

    $fields['option_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Voting Option'))
      ->setSetting('target_type', 'voting_option')
      ->setRequired(TRUE);

    return $fields;
  }
}
