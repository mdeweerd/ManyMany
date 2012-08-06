<?php

class SongController extends BaseController
{
	public $layout = '//layouts/column2'; 
	
	/**
	 * Set up models with CGV search form input.
     * 
     * This method is the same as manually doing the following in the action:
     *  $song->unsetAttributes();
     *  $genre  = new Genre('search');
     *  $genre->unsetAttributes();
     * 
     *  if (isset($_GET['Song'])) {
     *      $genre->attributes = $_GET['Song'];
     *  }
     *  if (isset($_GET['Genre'])) {
     *      $genre->attributes = $_GET['Genre'];
     *  }
     *  $song->searchGenre = $song;
	 *
	 * @param CActiveRecord $model
	 */
	protected function setSearchInputs($model)
    {
		foreach (array('Reviewer', 'Review', 'Song', 'SongGenre', 'Genre') as $class)
        {
			if (get_class($model) === $class) {
				$model->unsetAttributes();
				if (isset($_GET[$class])) {
					$model->attributes = $_GET[$class];
				}
			} else {
				$prop = 'search' . $class;
				if (property_exists($model, $prop)) {
					$model->$prop = new $class('search');
					$model->$prop->unsetAttributes();
					if (isset($_GET[$class])) {
						$model->$prop->attributes = $_GET[$class];
					}
				}
			}
		}
	}

	/**
	 * Grid of all songs including genres column
	 */
	public function actionSongs()
    {
		$song = new Song('search');
		$this->setSearchInputs($song);

        if(!isset($_GET['ajax'])) {
            $this->render('songsGrid', array(
                'song'=>$song,
            ));
        } else {
            $this->renderPartial('_songsGrid', array(
                'song'=>$song,
            ));
        }

	}


	/**
	 * Returns a Song model given its primary key.
	 *
	 * @param integer $id the ID of the Song to be loaded
	 * @throws CHttpException If the Song model is not found.
	 * @return CActiveRecord
	 */
	public function loadModel($id)
    {
		$song = Song::model()->findByPk($id);
		if ($song === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $song;
	}

}
