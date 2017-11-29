<?php

namespace Drupal\config_readonly_filter\EventSubscriber;

use Drupal\config_readonly\EventSubscriber\ReadOnlyFormSubscriber;
use Drupal\config_readonly\ReadOnlyFormEvent;
use Drupal\config_readonly_filter\ConfigReadOnlyFilterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Read-only subscriber to exclude whitelisted forms and routes.
 */
class ConfigReadOnlyFilterSubscriber extends ReadOnlyFormSubscriber {

  /**
   * Entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Entity manager.
   *
   * @var \Drupal\config_readonly_filter\Entity\ConfigReadOnlyFilter[]
   */
  protected $filters;

  /**
   * Creates a new EntityRevisionRouteAccessChecker instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function onFormAlter(ReadOnlyFormEvent $event) {
    $build_info = $event->getFormState()->getBuildInfo();
    $form_object = $build_info['callback_object'];
    if (in_array($form_object->getFormId(), $this->getFormIds())) {
      $event->stopPropagation();
    }
  }

  /**
   * Helper to get the current form Id.
   *
   * @param \Drupal\config_readonly\ReadOnlyFormEvent $event
   *   Event.
   *
   * @return string
   *   String form ID.
   */
  protected function getCurrentFormId(ReadOnlyFormEvent $event) {
    $build_info = $event->getFormState()->getBuildInfo();
    $form_object = $build_info['callback_object'];
    return $form_object->getFormId();
  }

  /**
   * Get form IDs from filter entity.
   *
   * @return array
   *   An array of form ids.
   */
  protected function getFormIds() {
    $form_ids = [];
    foreach ($this->getFilters() as $filter) {
      if ($filter->getType() == ConfigReadOnlyFilterInterface::TYPE_FORM) {
        $form_ids = $filter->getConfiguration();
      }
    }
    return $form_ids;
  }

  /**
   * List of filters available.
   *
   * @return \Drupal\config_readonly_filter\Entity\ConfigReadOnlyFilter[]|null
   *   An array of config read-only filter entities.
   */
  protected function getFilters() {
    if (!isset($this->filters)) {
      $filter_storage = $this->entityTypeManager->getStorage('config_readonly_filter');
      $this->filters = $filter_storage->loadMultiple();
    }
    return $this->filters;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[ReadOnlyFormEvent::NAME][] = ['onFormAlter', 201];
    return $events;
  }

}
