<?php

/**
 * Table attributes:
 * @property string $id
 * @property string $name
 * @property string $artist
 * @property string $album
 *
 * Relation attributes:
 * @property Genre[] $genres
 * @property SongGenre[] $hasGenres
 *
 * Virtual attributes:
 * @property array $genreNames
 */
class Song extends CActiveRecord
{
	/**
	 * @var Genre Used for CGV filter form inputs.
	 */
	public $searchGenre;

	private $_genreNames;

	public static function model($className = __CLASS__)
    {
		return parent::model($className);
	}

	public function tableName()
    {
		return 'song';
	}

	public function rules()
    {
		return array(
			array('name, artist, album', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
    {
		return array(
			'hasGenres' => array(self::HAS_MANY, 'SongGenre', 'song_id'),
			'genres'    => array(self::HAS_MANY, 'Genre', 'genre_id', 'through' => 'hasGenres'),
			'reviews'   => array(self::HAS_MANY, 'Review', 'song_id'),
			'reviewers' => array(self::HAS_MANY, 'Reviewer', 'reviewer_id',	'through' => 'reviews'),
		);
	}

    public function attributeLabels()
    {
        return array(
            'id'        => 'Song ID',
            'name'      => 'Title',
            'artist'    => 'Artist',
            'album'     => 'Album',
        );
    }

	public function getGenreNames()
    {
		if ($this->_genreNames === null) {
            //First we start with a 'pri' array and a 'sec' array.
			$this->_genreNames = array('pri' => array(), 'sec' => array());
			$genres = $this->with('hasGenres.genre')->hasGenres;
			if ($genres) {
				foreach ($genres as $genre)
                {
                    //Then we put the genres into either the 'pri' or 'sec' array, depending on their is_primary attribute.
					$this->_genreNames[$genre->is_primary ? 'pri' : 'sec'][] = $genre->genre->name;
				}
			}
		}
        //This array is used in the component Help::tags()
		return $this->_genreNames;
	}

	public function search()
    {
		$criteria = new CDbCriteria;

        $criteria->with = array('hasGenres.genre');
        $criteria->together = true;

		$criteria->compare('t.name', $this->name, true);
		$criteria->compare('t.artist', $this->artist, true);
		$criteria->compare('t.album', $this->album, true);

		if ($this->searchGenre->name) {
            //enabling search for multiple genres using comma's or spaces
            $genreNames = explode(',', str_replace(' ', ',', $this->searchGenre->name));
            $genreCriteria = new CDbCriteria;
            foreach($genreNames as $genre)
            {
                if(!empty($genre)) {
                    $genreCriteria->compare('genre.name', $genre, true, 'OR');
                }
            }
            //We have to use two CDbCriteria's that we merge, so this one will be between parentheses. ( .. )
            //This way, we can use OR while not affecting the other compares.
            $criteria->mergeWith($genreCriteria);
		}

		return new KeenActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
                'defaultOrder'=>array(
                    'song_id'=>CSort::SORT_ASC,
                ),
                'attributes'=>array(
                    'genre.name'=>array(
                        'asc'=>'genre.name',
                        'desc'=>'genre.name DESC',
                    ),
                    '*',
                ),
            ),
            'pagination'=>array('pageSize'=>10),
			'withKeenLoading' => array('hasGenres.genre'),
		));
	}

}