<?php

declare(strict_types=1);

namespace Drupal\test_task;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the test task entity type.
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
 *
 * @see https://www.drupal.org/project/coder/issues/3185082
 */
final class TestTaskAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    if ($account->hasPermission($this->entityType->getAdminPermission())) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    return match($operation) {
      'view' => AccessResult::allowedIfHasPermission($account, 'view test_task'),
      'update' => AccessResult::allowedIfHasPermission($account, 'edit test_task'),
      'delete' => AccessResult::allowedIfHasPermission($account, 'delete test_task'),
      'delete revision' => AccessResult::allowedIfHasPermission($account, 'delete test_task revision'),
      'view all revisions', 'view revision' => AccessResult::allowedIfHasPermissions($account, ['view test_task revision', 'view test_task']),
      'revert' => AccessResult::allowedIfHasPermissions($account, ['revert test_task revision', 'edit test_task']),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIfHasPermissions($account, ['create test_task', 'administer test_task'], 'OR');
  }

}
