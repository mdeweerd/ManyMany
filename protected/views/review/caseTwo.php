<?php
/**
 * @var Review $review
 * @var BaseController $this
 */

echo CHtml::tag('h1', array(), 'Reviews: Second Usecase');

$this->renderPartial('_caseTwoGrid', array(
    'review' => $review,
));