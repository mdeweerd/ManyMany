<?php

class ReviewController extends Controller
{
    public $layout = '//layouts/column1';

    /**
     * Grid of all song reviews - first UseCase
     */
    public function actionCaseOne()
    {
        //First we create the Models we use to search and unset their default attributes.
        $review = new Review('search');
        $song   = new Song('search');
        $genre  = new Genre('search');
        $review->unsetAttributes();
        $song->unsetAttributes();
        $genre->unsetAttributes();
        
        //Then we set the attributes on the event the controller is send data using _GET
        if (isset($_GET['Review'])) {
            $review->attributes = $_GET['Review'];
        }
        if (isset($_GET['Song'])) {
            $song->attributes = $_GET['Song'];
        }
        if (isset($_GET['Genre'])) {
            $genre->attributes = $_GET['Genre'];
        }
        
        //Then we put the related Models in their respective properties that have been
        //set in the Review model.
        $review->searchSong = $song;
        $review->searchGenre = $genre;
        
        //CGridView uses ajax to load changes (when you sort/filter/next page)
        //It's more efficient and faster for the browser if we do a renderPartial when
        //the CGridView wants to get data using an ajax update.
        if(!isset($_GET['ajax'])) {
            $this->render('caseOne', array(
                'review' => $review,
            ));
        }
        elseif($_GET['ajax']==='review-grid') {
            $this->renderPartial('_caseOneGrid', array(
                'review' => $review,
            ));
        }
    }

    /**
     * Grid of all song reviews - second UseCase
     */
    public function actionCaseTwo()
    {
        $review = new Review('search');
        $song   = new Song('search');
        $genre  = new Genre('search');
        $review->unsetAttributes();
        $song->unsetAttributes();
        $genre->unsetAttributes();

        if (isset($_GET['Review'])) {
            $review->attributes = $_GET['Review'];
        }
        if (isset($_GET['Song'])) {
            $song->attributes = $_GET['Song'];
        }
        if (isset($_GET['Genre'])) {
            $genre->attributes = $_GET['Genre'];
        }

        $review->searchSong = $song;
        $review->searchGenre = $genre;

        if(!isset($_GET['ajax'])) {
            $this->render('caseTwo', array(
                'review' => $review,
            ));
        }
        elseif($_GET['ajax']==='review-grid-2') {
            $this->renderPartial('_caseTwoGrid', array(
                'review' => $review,
            ));
        }
    }

    /**
     * Grid of all song reviews - second UseCase
     */
    public function actionCaseThree()
    {
        $review = new Review('search');
        $song   = new Song('search');
        $genre  = new Genre('search');
        $review->unsetAttributes();
        $song->unsetAttributes();
        $genre->unsetAttributes();

        if (isset($_GET['Review'])) {
            $review->attributes = $_GET['Review'];
        }
        if (isset($_GET['Song'])) {
            $song->attributes = $_GET['Song'];
        }
        if (isset($_GET['Genre'])) {
            $genre->attributes = $_GET['Genre'];
        }

        $review->searchSong = $song;
        $review->searchGenre = $genre;

        if(!isset($_GET['ajax'])) {
            $this->render('caseThree', array(
                'review' => $review,
            ));
        }
        elseif($_GET['ajax']==='review-grid-3') {
            $this->renderPartial('_caseThreeGrid', array(
                'review' => $review,
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
        $review = Review::model()->findByPk($id);
        if ($review === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $review;
    }

}
