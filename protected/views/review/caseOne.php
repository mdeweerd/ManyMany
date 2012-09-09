<?php
/**
 * @var Review $review
 * @var Controller $this
 */

echo CHtml::tag('h1', array(), 'Reviews: First Usecase');

$this->renderPartial('_caseOneGrid', array(
    'review' => $review,
));