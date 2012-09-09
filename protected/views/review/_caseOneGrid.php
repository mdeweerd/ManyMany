<?php
/**
 * @var Review $review
 * @var Controller|CController $this
 */

######################################################################
//using the searchOne() method for the 1st usecase


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
		'value' => 'Help::tags($data->song->genreNames, "genres")',
		'filter' => CHtml::activeTextField($review->searchGenre, 'name'),
		//'filter' => CHtml::activedropDownList($genre, 'id', CHtml::listData(Genre::model()->findAll(array('order'=>'name')),'id','name'), array('empty'=>'Select') ),
	),
);

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'review-grid',
    'dataProvider' => $review->searchOne(),
    'filter' => $review,
    'columns' => $columns,
));