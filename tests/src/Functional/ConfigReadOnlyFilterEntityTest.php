<?php

namespace Drupal\Tests\config_readonly_filter\Functional;

use Drupal\config_readonly_filter\ConfigReadOnlyFilterInterface;

/**
 * Defines a class for testing the Configuration Read-only Filter entity.
 *
 * @group config_readonly_filter
 */
class ConfigReadOnlyFilterEntityTest extends ConfigReadOnlyFilterTestBase {

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
   * Tests filter entity CRUD operations.
   */
  public function testEntity() {
    // 1. List entities.
    $this->drupalGet('/admin/config/system/config_readonly_filter');
    $session = $this->assertSession();
    $session->pageTextContains('There is no Config read-only filter yet.');
    $session->linkByHrefExists('/admin/config/system/config_readonly_filter/add');

    // 2. Add Entity.
    $this->drupalGet('/admin/config/system/config_readonly_filter/add');
    $label = 'Global';
    $machine_name = strtolower($this->randomMachineName());
    $this->createConfigReadOnlyFilter(['label' => $label, 'id' => $machine_name]);
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
