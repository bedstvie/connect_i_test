<?php

namespace Drupal\test_task\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Backend Info' block.
 *
 * @Block(
 *   id = "backend_info_block",
 *   admin_label = @Translation("Backend Info"),
 * )
 */
class TestTaskBackendInfoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Add an empty div as a placeholder for AJAX content.
    $build = [
      '#markup' => '<div id="delayed-ajax-content-block"></div>',
      '#attached' => [
        'library' => ['test_task/backend_ajax_block_content'],
      ],
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    // Every new route this block will rebuild.
    return Cache::mergeContexts(parent::getCacheContexts(), ['user.roles:anonymous']);
  }

  /**
   * {@inheritdoc}
   *
   * This implementation overrides
   * the parent to show the block only
   * for user with permission 'View published content'.
   */
  public function access(AccountInterface $account, $return_as_object = FALSE) {
    $access = AccessResult::allowedIf(!$account->isAuthenticated());

    // This function can return an AccessResult object,
    // or the value it represents.
    return $return_as_object ? $access : $access->isAllowed();
  }

}
