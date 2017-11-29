<?php

namespace Drupal\config_readonly_filter\Entity;

use Drupal\config_readonly_filter\ConfigReadOnlyFilterInterface;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the configured configuration read-only filter entity.
 *
 * @ConfigEntityType(
 *   id = "config_readonly_filter",
 *   label = @Translation("Config read-only filter"),
 *   handlers = {
 *     "list_builder" = "Drupal\config_readonly_filter\ConfigReadOnlyFilterListBuilder",
 *     "form" = {
 *       "add" = "Drupal\config_readonly_filter\ConfigReadOnlyFilterForm",
 *       "edit" = "Drupal\config_readonly_filter\ConfigReadOnlyFilterForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "filter",
 *   admin_permission = "administer configuration read-only filter",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "delete-form" = "/admin/config/system/config_readonly_filter/manage/{config_readonly_filter}/delete",
 *     "edit-form" = "/admin/config/system/config_readonly_filter/manage/{config_readonly_filter}",
 *     "collection" = "/admin/config/system/config_readonly_filter",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "type",
 *     "configuration",
 *   }
 * )
 */
class ConfigReadOnlyFilter extends ConfigEntityBase implements ConfigReadOnlyFilterInterface {

  /**
   * The name (plugin ID) of the action.
   *
   * @var string
   */
  protected $id;

  /**
   * The label of the action.
   *
   * @var string
   */
  protected $label;

  /**
   * The action type.
   *
   * @var string
   */
  protected $type;

  /**
   * The configuration of the action.
   *
   * @var array
   */
  protected $configuration = [];

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration() {
    if (isset($this->configuration)) {
      $this->configuration = array_filter(explode("\r\n", $this->configuration));
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * Provides configuration values as array.
   *
   * @todo: refactor to use conditions.
   *
   * @see \Drupal\system\Plugin\Condition\RequestPath()
   */
  public function getConfigurationAsString() {
    return implode("\r\n", array_filter($this->configuration));
  }

}
