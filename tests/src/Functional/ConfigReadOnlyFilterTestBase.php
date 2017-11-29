<?php

namespace Drupal\Tests\config_readonly_filter\Functional;

use Drupal\config_readonly_filter\ConfigReadOnlyFilterInterface;
use Drupal\Tests\BrowserTestBase;

/**
 * Defines a base class for testing the Configuration Read-only Filter module.
 */
class ConfigReadOnlyFilterTestBase extends BrowserTestBase {

  /**
   * A user with permission to administer feeds and create content.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $adminUser;

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = [
    'block',
    'config',
    'config_readonly',
    'config_readonly_filter',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'access administration pages',
      'administer configuration read-only filter',
      'administer menu',
    ]);
    $this->drupalPlaceBlock('local_tasks_block');
    $this->drupalPlaceBlock('local_actions_block');
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Creates a filter.
   *
   * @param array $values
   *   An array of values.
   */
  protected function createConfigReadOnlyFilter(array $values = array()) {
    $label = 'Global';
    $machine_name = strtolower($this->randomMachineName());
    $values += [
      'label' => $label,
      'id' => $machine_name,
      'type' => ConfigReadOnlyFilterInterface::TYPE_FORM,
      'configuration' => "menu_edit_form\r\nsystem_performance_settings\r\n",
    ];
    $this->drupalPostForm('/admin/config/system/config_readonly_filter/add', $values, 'Save');
  }

  /**
   * Turns on read-only mode.
   */
  protected function turnOnReadOnlySetting() {
    $settings['settings']['config_readonly'] = (object) [
      'value' => TRUE,
      'required' => TRUE,
    ];
    $this->writeSettings($settings);
  }

  /**
   * Turns off read-only mode.
   */
  protected function  turnOffReadOnlySetting() {
    $settings['settings']['config_readonly'] = (object) [
      'value' => FALSE,
      'required' => TRUE,
    ];
    $this->writeSettings($settings);
  }

}
