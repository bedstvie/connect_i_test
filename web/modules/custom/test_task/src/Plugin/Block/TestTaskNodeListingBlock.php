<?php

namespace Drupal\test_task\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Node Listing' block.
 *
 * @Block(
 *   id = "test_task_node_listing_block",
 *   admin_label = @Translation("Node Listing"),
 * )
 */
class TestTaskNodeListingBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new NodeListingBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'node_count' => 20,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Add a configuration setting for the number of nodes to display.
    $form['node_count'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of nodes to display'),
      '#description' => $this->t('Enter the number of nodes you would like to display in this block.'),
      '#default_value' => $this->configuration['node_count'],
      '#min' => 1,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    // Save the configuration setting.
    $this->configuration['node_count'] = $form_state->getValue('node_count');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Using entity query to load published nodes.
    $node_storage = $this->entityTypeManager->getStorage('node');
    $query = $node_storage->getQuery()
      ->accessCheck()
    // Only published nodes.
      ->condition('status', 1)
      ->range()
      ->sort('type')
      ->sort('created', 'DESC');
    $nids = $query->execute();

    // Load node entities.
    $nodes = $node_storage->loadMultiple($nids);

    // Prepare grouped data by node type.
    $grouped_nodes = [];
    foreach ($nodes as $node) {
      $grouped_nodes[$node->bundle()][] = $node;
    }

    $table = [];
    foreach ($grouped_nodes as $type => $type_nodes) {
      // Add a header row for each type.
      $table[] = [
        [
          'data' => $this->t('Content Type: @type', ['@type' => $type]),
          'colspan' => 3,
          'class' => ['node-type-header'],
        ],
      ];

      // Define the headers for each group.
      $table[] = [
        ['data' => $this->t('Title'), 'class' => ['table-header']],
        ['data' => $this->t('Trimmed Title'), 'class' => ['table-header']],
        ['data' => $this->t('Created Date'), 'class' => ['table-header']],
      ];

      // Add rows for each node within the type.
      foreach ($type_nodes as $node) {
        $table[] = [
          $node->toLink($node->label())->toString(),
          $node->getTrimmedTitle(),
          date('Y-m-d', $node->getCreatedTime()),
        ];
      }
    }

    return [
      '#type' => 'table',
      '#rows' => $table,
      '#attributes' => ['class' => ['node-listing']],
      '#empty' => $this->t('No nodes available.'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // Invalidate this block cache when any node is updated or created.
    return Cache::mergeTags(parent::getCacheTags(), ['node_list']);
  }

  /**
   * {@inheritdoc}
   *
   * This implementation overrides
   * the parent to show the block only for anonymous user.
   */
  public function access(AccountInterface $account, $return_as_object = FALSE) {
    $access = AccessResult::allowedIf($account->hasPermission('access content'));

    // This function can return an AccessResult object,
    // or the value it represents.
    return $return_as_object ? $access : $access->isAllowed();
  }

}
