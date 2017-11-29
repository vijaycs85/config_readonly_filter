<?php

namespace Drupal\config_readonly_filter;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a config_readonly_filter entity.
 */
interface ConfigReadOnlyFilterInterface extends ConfigEntityInterface {

  const TYPE_FORM = 'form';

  const TYPE_ROUTE = 'route';

  /**
   * Get configuration.
   *
   * @return string
   *   An array of configuration.
   */
  public function getConfiguration();

}
