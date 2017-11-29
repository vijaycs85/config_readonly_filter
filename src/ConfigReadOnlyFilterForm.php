<?php

namespace Drupal\config_readonly_filter;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\ConfigFormBaseTrait;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base form for config_readonly_filter form edit forms.
 *
 * @internal
 */
class ConfigReadOnlyFilterForm extends EntityForm implements ContainerInjectionInterface {
  use ConfigFormBaseTrait;

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['config_readonly_filter.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\config_readonly_filter\Entity\ConfigReadOnlyFilter $filter */
    $filter = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $filter->label(),
      '#description' => $this->t("Menu list."),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $filter->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => [
        'exists' => '\Drupal\config_readonly_filter\Entity\ConfigReadOnlyFilter::load',
      ],
      '#disabled' => !$filter->isNew(),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Selection type'),
      '#default_value' => $filter->getType(),
      '#options' => [
        ConfigReadOnlyFilterInterface::TYPE_FORM => $this->t('Form ID'),
        ConfigReadOnlyFilterInterface::TYPE_ROUTE => $this->t('Route name'),
      ],
      '#description' => $this->t("Select type of whitelist option."),
      '#required' => TRUE,
    ];
    $form['configuration'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Configuration'),
      '#default_value' => $filter->getConfigurationAsString(),
      '#rows' => 10,
      '#description' => $this->t("Select type of whitelist option. one per line."),
      '#required' => TRUE,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // @todo: validate route
    if ($form_state->getValue('type') == 'form') {
      // Make sure form_id is available and belongs to config.
      // 'config_readonly_filter_edit_form' is not part it.
    }
    else {
      // Make sure route name is valid.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $filter = $this->entity;
    $filter->setConfiguration()->save();
    $form_state->setRedirectUrl($filter->urlInfo('collection'));
  }

}
