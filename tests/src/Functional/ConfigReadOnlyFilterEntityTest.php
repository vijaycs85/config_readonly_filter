<?php

namespace Drupal\Tests\config_readonly_filter\Functional;

use Drupal\config_readonly_filter\ConfigReadOnlyFilterInterface;
use Drupal\Tests\BrowserTestBase;

/**
 * Defines a base class for testing the Configuration Read-only Filter module.
 *
 * @group config_readonly_filter
 */
class ConfigReadOnlyFilterEntityTest extends BrowserTestBase {

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
    ]);
    $this->drupalPlaceBlock('local_tasks_block');
    $this->drupalPlaceBlock('local_actions_block');
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests admin UI.
   */
  public function testAdminUi() {
    $this->drupalGet('/admin/config/system');
    $session = $this->assertSession();
    $session->pageTextContains('Configuration read-only filter');
    $session->linkByHrefExists('/admin/config/system/config_readonly_filter');
  }

  /**
   * Tests adding new filter.
   */
  public function testEntity() {
    // 1. Add Entity.
    $this->drupalGet('/admin/config/system/config_readonly_filter');
    $session = $this->assertSession();
    $session->pageTextContains('There is no Config read-only filter yet.');
    $session->linkByHrefExists('/admin/config/system/config_readonly_filter/add');

    $this->drupalGet('/admin/config/system/config_readonly_filter/add');
    $label = 'Global';
    $machine_name = strtolower($this->randomMachineName());
    $edit = [
      'label' => $label,
      'id' => $machine_name,
      'type' => ConfigReadOnlyFilterInterface::TYPE_FORM,
      'configuration' => "menu_edit_form\r\nsystem_performance_settings\r\n",
    ];
    $this->drupalPostForm(NULL, $edit, 'Save');

    // 2. List entities.
    $session->pageTextContains($label);
    $session->pageTextNotContains('There is no Config read-only filter yet.');

    // 3. Edit Entity.
    $session->linkByHrefExists('admin/config/system/config_readonly_filter/manage/' . $machine_name);
    $edit = [
      'label' => $label . ' new',
    ];
    $this->drupalPostForm('admin/config/system/config_readonly_filter/manage/' . $machine_name, $edit, 'Save');
    $session->pageTextContains($label . ' new');

    // 4. Delete entity.
    $session->linkByHrefExists('admin/config/system/config_readonly_filter/manage/' . $machine_name . '/delete');
    $this->drupalGet('admin/config/system/config_readonly_filter/manage/' . $machine_name . '/delete');
    $session->pageTextContains('This action cannot be undone.');
    $this->drupalPostForm(NULL, [], 'Delete');
    $session->pageTextNotContains($label . 'new');
    $session->pageTextContains('There is no Config read-only filter yet.');
  }

}
