<?php

/**
 * @file
 * Contains \Drupal\commerce_order\Form\OrderTypeDeleteForm.
 */

namespace Drupal\commerce_order\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds the form to delete an order type.
 */
class OrderTypeDeleteForm extends EntityDeleteForm {

  /**
   * The query factory to create entity queries.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * Constructs a new OrderTypeDeleteForm object.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $queryFactory
   *   The entity query object.
   */
  public function __construct(QueryFactory $queryFactory) {
    $this->queryFactory = $queryFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $numOrders = $this->queryFactory->get('commerce_order')
      ->condition('type', $this->entity->id())
      ->count()
      ->execute();
    if ($numOrders) {
      $caption = '<p>' . $this->formatPlural($numOrders, '%type is used by 1 order on your site. You can not remove this order type until you have removed all of the %type orders.', '%type is used by @count orders on your site. You may not remove %type until you have removed all of the %type orders.', ['%type' => $this->entity->label()]) . '</p>';
      $form['#title'] = $this->getQuestion();
      $form['description'] = ['#markup' => $caption];
      return $form;
    }

    return parent::buildForm($form, $form_state);
  }

}
