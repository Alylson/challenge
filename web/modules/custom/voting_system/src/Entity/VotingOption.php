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
 *   "route_provider" = {
 *     "default" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
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
 *     "votesCount",
 *     "questionId"
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
    protected $votesCount = 0;

    /**
     * @var string
     */
    protected $questionId;

    public function getQuestionId()
    {
        return $this->questionId;
    }

    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;

        return $this;
    }

    public function getVotesCount()
    {
        return $this->votesCount;
    }

    public function setVotesCount($votesCount)
    {
        $this->votesCount = $votesCount;

        return $this;
    }

    public function incrementVotes()
    {
        $this->votesCount++;

        return $this;
    }
}
