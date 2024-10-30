<?php

namespace Drupal\test_task\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\test_task_contrib\FetchObjectServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Fetch Object' block.
 *
 * @Block(
 *   id = "test_task_fetch_object_block",
 *   admin_label = @Translation("Fetch Object"),
 * )
 */
class TestTaskFetchObjectBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Fetch Object service.
   *
   * @var \Drupal\test_task_contrib\FetchObjectServiceInterface
   */
  protected $fetchObject;

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new NodeListingBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\test_task_contrib\FetchObjectServiceInterface $fetch_object
   *   The fetch object service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match interface.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FetchObjectServiceInterface $fetch_object, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->fetchObject = $fetch_object;
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('test_task_contrib.fetch_object'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $elements = [];

    foreach ($this->getObjectTypes() as $object_type) {
      // Fetch object by route.
      $elements[] = $this->getObjectLabelElement($object_type);
    }

    return $elements;
  }

  /**
   * Get Argument label element.
   *
   * @param string $object_type
   *   Entity type.
   *
   * @return array
   *   Renderer element.
   */
  public function getObjectLabelElement(string $object_type) {
    if ($this->fetchObject->fetchObjectMethodExist($object_type)) {
      $object_label = $this->fetchObject->fetchObjectByRoute($object_type);
      if (empty($object_label)) {
        $object_label = $this->t('not available on this page');
      }

      return [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $this->t('Current @type argument: @label.', ['@type' => $object_type, '@label' => $object_label]),
      ];

    }

    return [];
  }

  /**
   * Get available object types.
   *
   * @return string[]
   *   List of entity types.
   */
  public function getObjectTypes() {
    return [
      'user',
      'node',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $tags = [];

    foreach ($this->getObjectTypes() as $object_type) {
      if ($object = $this->routeMatch->getParameter($object_type)) {
        $tags[] = $object_type . ':' . $object->id();
      }
    }

    return Cache::mergeTags(parent::getCacheTags(), $tags);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    // Every new route this block will rebuild.
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }

}
