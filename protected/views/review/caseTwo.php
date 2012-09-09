<?php
/**
 * @var Review $review
 * @var Controller $this
 */

echo CHtml::tag('h1', array(), 'Reviews: Second Usecase');

$this->renderPartial('_caseTwoGrid', array(
    'review' => $review,
));