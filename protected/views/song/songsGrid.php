<?php
/**
 * @var Song $song
 * @var BaseController $this
 */


echo CHtml::tag('h1', array(), 'Songs');

$this->renderPartial('_songsGrid', array(
    'song' => $song,
));