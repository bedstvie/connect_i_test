<?php

namespace Drupal\test_task\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;

/**
 * Checks access for displaying configuration translation page.
 */
class TestTaskUserAccessibility implements AccessInterface {

  /**
   * A custom access check.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\Core\Entity\EntityInterface $user
   *   The user entity being accessed.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, EntityInterface $user) {
    // Allow access for the superadmin (user ID 1).
    if ($account->id() === 1) {
      return AccessResult::allowed();
    }

    if ($user instanceof User) {
      // Check if the user entity has the "keep private" option enabled.
      $accessibility = $user->get('field_accessibility')->value;
      if ($accessibility == 'private') {
        // Deny access for other users if "Keep private" is selected.
        if (!$account->isAuthenticated() || $account->id() !== $user->id()) {
          return AccessResult::forbidden();
        }
      }
    }

    return AccessResult::allowed();
  }

}
