<?php
/**
 * @file
 * Contains \Drupal\demo_form\Forms\BasicForm.
 */

namespace Drupal\node_clone\Forms;

use Drupal\Core\Form\ConfigFormBase;

/**
 * Implements an example form.
 */
class CloneSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormID() {
    return 'node_clone_settings_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, array &$form_state) {

    // Get the configuration
    $config = $this->config('node_clone.settings');

    $form['basic'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('General settings'),
    );
    $form['basic']['clone_method'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Method to use when cloning a node'),
      '#options' => array('prepopulate' => $this->t('Pre-populate the node form fields'), 'save-edit' => $this->t('Save as a new node then edit')),
      '#default_value' => $config->get('clone_method'),
    );
    $form['basic']['clone_nodes_without_confirm'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Confirmation mode when using the "Save as a new node then edit" method'),
      '#default_value' => $config->get('clone_nodes_without_confirm'),
      '#options' => array($this->t('Require confirmation (recommended)'), $this->t('Bypass confirmation')),
      '#description' => $this->t('A new node may be saved immediately upon clicking the "clone" link when viewing a node, bypassing the normal confirmation form.'),
    );
    $form['basic']['clone_menu_links'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Clone menu links'),
      '#default_value' => $config->get('clone_menu_links'),
      '#description' => $this->t('Should any menu link for a node also be cloned?'),
    ];
    $form['basic']['clone_use_node_type_name'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Use node type name in clone link'),
      '#default_value' => $config->get('clone_use_node_type_name'),
      '#description' => $this->t('If checked, the link to clone the node will contain the node type name, for example, "Clone this article", otherwise it will read "Clone content".'),
    );

    /*
     * @TODO settings with variable names
     */
    $form['publishing'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Should the publishing options ( e.g. published, promoted, etc) be reset to the defaults?'),
    );

    $types = node_type_get_names();

    $form['publishing']['clone_reset_options'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Reset publishing options'),
      '#default_value' => $config->get('clone_reset_options') ? $config->get('clone_reset_options') : array(),
      '#options' => $types,
      '#description' => $this->t('Select any node types which should <em>reset</em> the publishing options when cloned. In other words, all node where publishing options should be as confured for the content type.'),
    );

    // Need the variable default key to be something that's never a valid node type.
    $form['omit'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Content types that are not to be cloned - omitted due to incompatibility'),
    );
    $form['omit']['clone_omitted'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Omitted content types'),
      '#default_value' => $config->get('clone_omitted') ? $config->get('clone_omitted') : array(),
      '#options' => $types,
      '#description' => $this->t('Select any node types which should <em>never</em> be cloned. In other words, all node types where cloning will fail.'),
    );

    return parent::buildForm($form, $form_state);
 }

  /**
   * Compares the submitted settings to the defaults and unsets any that are equal. This was we only store overrides.
   */
  public function submitForm(array &$form, array &$form_state) {

    // Get config factory
    $config = $this->config('node_clone.settings');

    $form_values = $form_state['values'];

    $config
      ->set('clone_method', $form_values['clone_method'])
      ->set('clone_nodes_without_confirm', $form_values['clone_nodes_without_confirm'])
      ->set('clone_menu_links', $form_values['clone_menu_links'])
      ->set('clone_use_node_type_name', $form_values['clone_use_node_type_name'])
      ->set('clone_reset_options', $form_values['clone_reset_options'])
      ->set('clone_omitted', $form_values['clone_omitted'])
      ->save();

    parent::submitForm($form, $form_state);

  }
}