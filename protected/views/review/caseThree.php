<?php
/**
 * @var Review $review
 * @var BaseController $this
 */

echo CHtml::tag('h1', array(), 'Reviews: Third Usecase');

$this->renderPartial('_caseThreeGrid', array(
    'review' => $review,
));