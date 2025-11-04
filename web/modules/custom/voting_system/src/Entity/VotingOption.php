<?php

namespace Drupal\voting_system\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Voting Option entity.
 *
 * @ConfigEntityType(
 *   id = "voting_option",
 *   label = @Translation("Voting Option"),
 *   handlers = {
 *     "list_builder" = "Drupal\voting_system\Entity\VotingOptionListBuilder",
 *     "form" = {
 *       "add" = "Drupal\voting_system\Form\VotingOptionForm",
 *       "edit" = "Drupal\voting_system\Form\VotingOptionForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     }
 *   },
 *   config_prefix = "voting_option",
 *   admin_permission = "Administer voting options",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "collection" = "/admin/content/voting-option",
 *     "add-form" = "/admin/content/voting-option/add",
 *     "edit-form" = "/admin/content/voting-option/{voting_option}/edit",
 *     "delete-form" = "/admin/content/voting-option/{voting_option}/delete"
 *   },
 *   config_export = {
 *     "id",
 *     "title",
 *     "description",
 *     "image",
 *     "votes_count",
 *     "question_id"
 *   }
 * )
 */
class VotingOption extends ConfigEntityBase
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
    protected $description;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var int
     */
    protected $votes_count = 0;

    /**
     * @var string
     */
    protected $question_id;

    public function getQuestionId()
    {
        return $this->question_id;
    }

    public function setQuestionId($question_id)
    {
        $this->question_id = $question_id;

        return $this;
    }

    public function getVotesCount()
    {
        return $this->votes_count;
    }

    public function setVotesCount($votes_count)
    {
        $this->votes_count = $votes_count;

        return $this;
    }

    public function incrementVotes()
    {
        $this->votes_count++;

        return $this;
    }
}
