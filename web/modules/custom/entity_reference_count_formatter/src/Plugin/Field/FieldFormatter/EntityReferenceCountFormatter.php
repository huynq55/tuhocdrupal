<?php

namespace Drupal\entity_reference_count_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

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

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $entityReferenced = $item->entity;

      $field_definition = $item->getFieldDefinition();
      $entityTypeHasField = $field_definition->getTargetEntityTypeId();
      $bundleOfEntity = $field_definition->getTargetBundle();
      $referenceFieldName = $field_definition->getName();

      $query = \Drupal::entityQuery($entityTypeHasField)
        ->accessCheck(FALSE)
        ->condition('type', $bundleOfEntity)
        ->condition($referenceFieldName, $entityReferenced->id())
        ->count();
      $count = $query->execute();

      $url = $entityReferenced->toUrl()->toString();

      $elements[$delta] = [
        '#theme' => 'entity_reference_count_formatter',
        '#entity_name' => $entityReferenced->label(),
        '#count' => $count,
        '#url' => $url,
      ];
    }

    return $elements;
  }
}
