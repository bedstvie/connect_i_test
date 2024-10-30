<?php

namespace Drupal\test_task\Entity;

use Drupal\node\Entity\Node;

/**
 * Added method to Node class.
 */
class ImprovedNode extends Node {

  /**
   * Returns the first 10 characters of the title.
   *
   * @return string
   *   The trimmed title.
   */
  public function getTrimmedTitle() {
    // Get the title and trim it to the first 10 characters.
    return substr($this->getTitle(), 0, 10);
  }

}
