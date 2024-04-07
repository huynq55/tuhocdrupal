<?php

namespace Drupal\entity_reference_count_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'entity_reference_count_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "entity_reference_count_formatter",
 *   label = @Translation("Entity Reference Count Formatter"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class EntityReferenceCountFormatter extends FormatterBase {

  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, protected EntityTypeManagerInterface $entityTypeManager, protected CacheBackendInterface $cacheBackend) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'], $configuration['label'], $configuration['view_mode'], $configuration['third_party_settings'], $container->get('entity_type.manager'), $container->get('cache.default'));
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $filterByBundle = $this->getSetting('filter_by_bundle');

    foreach ($items as $delta => $item) {
      $referencedEntity = $item->entity;
      $fieldDefinition = $item->getFieldDefinition();
      $entityType = $fieldDefinition->getTargetEntityTypeId();
      $entityBundle = $fieldDefinition->getTargetBundle();
      $referenceField = $fieldDefinition->getName();
      $cacheId = "entity_reference_count:{$entityType}:{$entityBundle}:{$referenceField}:{$referencedEntity->id()}";

      $cache = $this->cacheBackend->get($cacheId);
      if ($cache) {
        $referenceCount = $cache->data;
      }
      else {
        $query = $this->entityTypeManager->getStorage($entityType)->getQuery()
          ->accessCheck(FALSE)
          ->condition($referenceField, $referencedEntity->id());

        if ($filterByBundle) {
          $query->condition('type', $entityBundle);
        }

        $referenceCount = $query->count()->execute();
        $this->cacheBackend->set($cacheId, $referenceCount, Cache::PERMANENT, ['entity_reference_count:' . $entityType]);
      }

      $entityUrl = $referencedEntity->toUrl()->toString();

      $elements[$delta] = [
        '#theme' => 'entity_reference_count_formatter',
        '#entity_name' => $referencedEntity->label(),
        '#count' => $referenceCount,
        '#url' => $entityUrl,
        '#attached' => [
          'library' => [
            'entity_reference_count_formatter/entity_reference_count_formatter',
          ],
        ],
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    // Thêm một checkbox để cho phép lọc theo bundle.
    $elements['filter_by_bundle'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Filter by bundle'),
      '#default_value' => $this->getSetting('filter_by_bundle'),
      '#description' => $this->t('If checked, the reference count will be filtered by the entity bundle.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $filterByBundle = $this->getSetting('filter_by_bundle') ? $this->t('Yes') : $this->t('No');
    $summary[] = $this->t('Filter by bundle: @filter_by_bundle', ['@filter_by_bundle' => $filterByBundle]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
    // Giá trị mặc định, thay đổi thành FALSE nếu bạn muốn.
      'filter_by_bundle' => TRUE,
    ] + parent::defaultSettings();
  }

}
