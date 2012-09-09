<?php
/**
 * @var Song $song
 * @var Controller $this
 */

$columns = array(
    array(
        'header' => 'Num',
        'value' => Help::$gridRowExp,
    ),
    array(
        'name' => 'name',
        'filter' => CHtml::activeTextField($song, 'name'),
    ),
    array(
        'name' => 'artist',
        'filter' => CHtml::activeTextField($song, 'artist'),
    ),
    array(
        'name' => 'album',
        'filter' => CHtml::activeTextField($song, 'album'),
    ),
    array(
        'type' => 'raw',
        'name' => 'genre.name',
        'header' => 'Genres',
        'value' => 'Help::tags($data->genreNames, "genres")',
        'filter' => CHtml::activeTextField($song->searchGenre, 'name'),
    ),
);

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'song-grid',
    'dataProvider' => $song->search(),
    'filter' => $song,
    'columns' => $columns,
));