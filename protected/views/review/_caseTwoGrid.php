<?php
/**
 * @var Review $review
 * @var Controller|CController $this
 */

######################################################################
//using the searchTwo() method for the 2nd usecase


$columns = array(
	array(
		'header' => 'Num',
		'value' => Help::$gridRowExp,
	),
	array(
		'name' => 'review',
	),
	array(
		'name' => 'song.name',
		'filter' => CHtml::activeTextField($review->searchSong, 'name'),
	),
	array(
		'name' => 'song.artist',
		'filter' => CHtml::activeTextField($review->searchSong, 'artist'),
	),
	array(
		'name' => 'song.album',
		'filter' => CHtml::activeTextField($review->searchSong, 'album'),
	),
    array(
        'name' => 'genres.name',
        'type' => 'raw',
        'header' => 'Genres',
        'value' => '$data->allGenres',
//        'filter' => CHtml::activeTextField($review->searchGenre, 'name'),
        'filter' => CHtml::activedropDownList($review->searchGenre, 'id', CHtml::listData(Genre::model()->findAll(array('order'=>'name')),'id','name'), array('empty'=>'Select') ),
    ),
);

$this->widget('zii.widgets.grid.CGridView', array(
    'ajaxUpdate'=>false,
    'id' => 'review-grid-2',
    'dataProvider' => $review->searchTwo(),
    'filter' => $review,
    'columns' => $columns,
));