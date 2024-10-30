<?php

declare(strict_types=1);

namespace Drupal\test_task;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a test task entity type.
 */
interface TestTaskInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
