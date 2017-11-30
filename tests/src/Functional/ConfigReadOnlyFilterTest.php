<?php

namespace Drupal\Tests\config_readonly_filter\Functional;

/**
 * Defines a class for testing the Configuration Read-only Filter module.
 *
 * @group config_readonly_filter
 */
class ConfigReadOnlyFilterTest extends ConfigReadOnlyFilterTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';

  /**
   * Tests functional aspects.
   */
  public function testConfigReadOnlyFilter() {
    // Enable read-only mode.
    $this->turnOnReadOnlySetting();
    $session = $this->assertSession();
    // Make sure menu is not editable.
    $this->drupalGet('/admin/structure/menu/manage/admin');
    $session->pageTextContains('This form will not be saved because the configuration active store is read-only.');

    // Turn off to create filter.
    $this->turnOffReadOnlySetting();
    $this->createConfigReadOnlyFilter();

    // Enable read-only mode again.
    $this->turnOnReadOnlySetting();

    // Make sure menu is editable.
    $this->drupalGet('/admin/structure/menu/manage/admin');
    $session->pageTextNotContains('This form will not be saved because the configuration active store is read-only.');
  }

  /**
   * Tests module with filter.
   */
  public function testConfigReadOnlyFilterModule() {
    // Enable read-only mode.
    $this->turnOnReadOnlySetting();
    $session = $this->assertSession();
    // Make sure menu is not editable.
    $this->drupalGet('/admin/structure/menu/manage/admin');
    $session->pageTextContains('This form will not be saved because the configuration active store is read-only.');

    // Disable read-only mode to enable test module.
    $this->turnOffReadOnlySetting();
    // Install test module with filter.
    \Drupal::service('module_installer')->install(['config_readonly_filter_test']);
    $this->rebuildContainer();

    // Enable read-only mode.
    $this->turnOnReadOnlySetting();
    // Make sure menu is editable.
    $this->drupalGet('/admin/structure/menu/manage/admin');
    $session->pageTextNotContains('This form will not be saved because the configuration active store is read-only.');

  }

}
