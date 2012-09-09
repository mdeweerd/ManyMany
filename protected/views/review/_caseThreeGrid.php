<?php
/**
 * @var Review $review
 * @var Controller|CController $this
 */


######################################################################
//using the searchThree() method for the 3rd usecase


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
        'name' => 'genre.name',
        'type' => 'raw',
        'header' => 'Genres',
        'value' => 'Help::tags($data->song->genreNames, "genres")',
        'filter' => CHtml::activeTextField($review->searchGenre, 'name'),
        //'filter' => CHtml::activedropDownList($genre, 'id', CHtml::listData(Genre::model()->findAll(array('order'=>'name')),'id','name'), array('empty'=>'Select') ),
    ),
);

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'review-grid-3',
    'dataProvider' => $review->searchThree(),
    'filter' => $review,
    'columns' => $columns,
));