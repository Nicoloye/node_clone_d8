<?php
/**
 * @file
 * Contains \Drupal\node_clone\Forms\CloneForm.
 */

/**
 * @todo
 * From node_clone.pages.inc:
 * - prompt the user to confirm the operation.
 * - Clone a node by directly saving it.
 */


namespace Drupal\node_clone;

use Drupal\node\NodeForm;

/**
 * Implements the node_clone form.
 */
class NodeCloneForm extends NodeForm {

  /**
   * {@inheritdoc}
   * Overrides Drupal\node\NodeForm::prepareEntity
   */
  protected function prepareEntity() {

    parent::prepareEntity();
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->entity;
    // Create clone of this entity
    $this->original_node = clone $this->entity;

    // Make sure this node is treated as a new one.
    $node->enforceIsNew();
    $node->setNewRevision(TRUE);
    $node->setValue(array(
        'nid' => 0,
        'uuid' => \Drupal::service('uuid')->generate(),
        'vid' => 0,
        'log' => NULL,
        'created' => REQUEST_TIME,
        'uid' => $this->currentUser()->id(),
        'title' => $this->t('Clone of !title', array('!title' => $node->getTitle())),
      )
    );

    // clear comments from node
    $node->setValue(array(
        'comment' => array(
          'cid' => '0',
          'last_comment_timestamp' => REQUEST_TIME,
          'last_comment_name' => NULL,
          'comment_count' => '0'
        )
      )
    );

    if ($this->config('node_clone.settings')->get('clone_reset_options')[$node->bundle()]) {
      // Get default settings for this bundle
      $type = entity_load('node_type', $node->bundle());
      $bundle_settings = $type->getModuleSettings('node');
      foreach (array('status', 'promote', 'sticky') as $key) {
        $node->setValue(array(
            $key => (int) $bundle_settings['options'][$key],
          )
        );
      }
    }

    // Clone menu_link if original node had one
    if ($this->config('node_clone.settings')->get('clone_menu_links') && $menu_link = $this->cloneMenuLink($this->original_node)) {
      $node->menu = $menu_link;
    }

    // @todo Override other node properties.
  }



  /**
   * Overrides Drupal\node\NodeForm::form().
   */
  public function form(array $form, array &$form_state) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->entity;
    $form = parent::form($form, $form_state);
    $form['#title'] = $this->t('<em>Clone @type</em> @title', array('@type' => node_get_type_label($node), '@title' => $node->label()));
    // user should not unset 'Create new revision'
    // unset($form['revision']);
    // unset($form['log']);

    return $form;
  }


  /**
   * Overrides Drupal\node\NodeForm::actions().
   */
  protected function actions(array $form, array &$form_state) {
    $element = parent::actions($form, $form_state);
    $element['delete']['#access'] = FALSE;
    return $element;
  }

  /**
   * {@inheritdoc}.
   */
  public function getFormID() {
    return 'node_clone_edit_form';
  }

  /**
   * Create a new menu link cloned from another node.
   *
   * Returns NULL if no existing link, or links are not to be cloned.
   */

  private function cloneMenuLink($node) {
    // This will fetch the existing menu link if the node had one.
    $link_path = 'node/' . $node->id();
    $mlid = \Drupal::entityQuery('menu_link')->condition('link_path', $link_path)->execute();
    if ($mlid) {
      $menu_link = entity_load('menu_link', reset($mlid), FALSE);
      $menu_link->enforceIsNew(TRUE);
      $menu_link['link_title'] = t('Clone of !title', array('!title' => $menu_link['link_title']));
      $menu_link['link_path'] = NULL;
      $menu_link['uuid'] = \Drupal::service('uuid')->generate();
      if (isset($menu_link['options']['attributes']['title'])) {
        $menu_link['options']['attributes']['title'] = t('Clone of !title', array('!title' => $menu_link['options']['attributes']['title']));
      }
      return $menu_link;
    }
    return NULL;
  }
}