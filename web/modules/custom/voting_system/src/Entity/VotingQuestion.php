<?php

namespace Drupal\voting_system\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Voting Question entity.
 *
 * @ConfigEntityType(
 *   id = "voting_question",
 *   label = @Translation("Voting Question"),
 *   handlers = {
 *     "list_builder" = "Drupal\voting_system\Entity\VotingQuestionListBuilder",
 *     "form" = {
 *       "add" = "Drupal\voting_system\Form\VotingQuestionForm",
 *       "edit" = "Drupal\voting_system\Form\VotingQuestionForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     }
 *   },
 *   config_prefix = "voting_question",
 *   admin_permission = "administer voting options",
 *   links = {
 *     "collection" = "/admin/content/voting-question",
 *     "add-form" = "/admin/content/voting-question/add",
 *     "edit-form" = "/admin/content/voting-question/{voting_question}/edit",
 *     "delete-form" = "/admin/content/voting-question/{voting_question}/delete"
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "title",
 *     "identifier",
 *     "enabled",
 *     "show_results"
 *   }
 * )
 */
class VotingQuestion extends ConfigEntityBase
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var bool
     */
    protected $enabled = TRUE;

    /**
     * @var bool
     */
    protected $show_results = TRUE;

    public function isEnabled()
    {
        return (bool) $this->enabled;
    }

    public function getOptions()
    {
        $storage = \Drupal::entityTypeManager()->getStorage('voting_option');
        $options = $storage->loadByProperties(['question_id' => $this->id()]);

        return $options;
    }

    public function getOptionCount()
    {
        $options =  $this->getOptions();

        return count($options);
    }
}
