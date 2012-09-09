<?php $this->pageTitle=Yii::app()->name; ?>

<article class="markdown-body entry-content" itemprop="mainContentOfPage"><h1>
    <a name="manymany" class="anchor" href="#manymany"><span class="mini-icon mini-icon-link"></span></a>ManyMany</h1>

<h2>
    <a name="a-yii-app-for-experimenting-with-complex-grid-views" class="anchor" href="#a-yii-app-for-experimenting-with-complex-grid-views"><span class="mini-icon mini-icon-link"></span></a>A Yii app for experimenting with complex grid views</h2>

<p>The app shows four ways of how to display related data in a fully functional gridview. We (<em>tom[]</em> and <em>yjeroen</em>) have been working on this to show fellow Yii users what the possibilities are.</p>

<p>All gridviews have working implementations of <a href="#1-cgridview-paging-sorting-and-filtering">paging, sorting and filtering</a>.</p>

<ol>
    <li>
        <strong><a href="#21-usecase-one---lazy-loading">UseCase One</a></strong>: Only primary data is loaded with the <code>CActiveDataProvider</code>, related data is Lazy loaded.</li>
    <li>
        <strong><a href="#22-usecase-two---group_concat">UseCase Two</a></strong>: Related data is loaded using a GROUP_CONCAT query. This is the most data efficient way, but you can't do any manipulation using the join-model or related-model.</li>
    <li>
        <strong><a href="#23-usecase-three---custom-cactivefinder">UseCase Three</a></strong>: You normally can't use Yii's Eager Loading method in gridviews in combination with a pager. (If you don't use the pager, you can Eager load without problems.)<br>
        The reason why it won't work with the pager is because the pager adds LIMIT and OFFSET to the query, but those are static. In combination with JOIN statements, this becomes a problem. I made some changes to <em>CActiveFinder</em> so the correct LIMIT and OFFSET numbers are calculated using two seperate COUNT queries. This will enable you to use Yii's Eager loading without any problems.</li>
    <li>
        <strong><a href="#24-usecase-four---keenloading">UseCase Four - KeenLoading</a></strong>: This method uses a custom <code>KeenActiveDataProvider</code>, which loads all related data in a Keen way using a seperate query.</li>
</ol><p>With these four methods, it might be hard to choose which one to use. Here are some considerations:</p>

<ol>
    <li><p><strong>UseCase One</strong><br>
        Pro: Default Yii lazy loading<br>
        Con: A lot of queries(!)</p></li>
    <li><p><strong>UseCase Two</strong><br>
        Pro: Most data efficient<br>
        Pro: Only one query<br>
        Con: No manipulation of data of the related Model(s)</p></li>
    <li><p><strong>UseCase Three</strong><br>
        Pro: Default Yii eager loading<br>
        Neutral: Up to two extra COUNT queries. (For a max of three queries)<br>
        Con: Like all eager loading, this can become data inefficient</p></li>
    <li><p><strong>KeenLoading</strong><br>
        Pro: Able to manipulate data of the related Model(s)<br>
        Pro: Still very efficient<br>
        Neutral: One extra query for loading the related Model(s)  </p></li>
</ol><p>⌇  </p>

<hr><h2>
    <a name="1-cgridview-paging-sorting-and-filtering" class="anchor" href="#1-cgridview-paging-sorting-and-filtering"><span class="mini-icon mini-icon-link"></span></a>1. CGridView: Paging, Sorting and Filtering</h2>

<h3>
    <a name="11-paging" class="anchor" href="#11-paging"><span class="mini-icon mini-icon-link"></span></a>1.1 Paging</h3>

<p>The <code>CPagination</code> object in your <code>CActiveDataProvider</code> adds LIMIT and OFFSET to the SQL query that Yii performs. This can become a problem when you do queries with JOIN in them(if you set together=true for eager loading), because the database returns multiple rows for one model, while Yii expects one row returned for each model.<br>
    The easiest way to fix this, is to group by the primary key(s) of your main model. The UseCases shown below all do this in one way or another.</p>

<div class="highlight"><pre><span class="cp">&lt;?php</span>
    <span class="nv">$criteria</span> <span class="o">=</span> <span class="k">new</span> <span class="nx">CDbCriteria</span><span class="p">;</span>
    <span class="nv">$criteria</span><span class="o">-&gt;</span><span class="na">with</span> <span class="o">=</span> <span class="k">array</span><span class="p">(</span><span class="s1">'song'</span><span class="p">);</span>
    <span class="nv">$criteria</span><span class="o">-&gt;</span><span class="na">group</span> <span class="o">=</span> <span class="s1">'t.id'</span><span class="p">;</span>
    <span class="nv">$criteria</span><span class="o">-&gt;</span><span class="na">together</span> <span class="o">=</span> <span class="k">true</span><span class="p">;</span>
</pre></div>

<p>Reference: <a href="https://github.com/tom--/ManyMany/blob/yj-KeenLoading/protected/models/Review.php#L62">Review::searchOne()</a></p>

<h3>
    <a name="12-sorting" class="anchor" href="#12-sorting"><span class="mini-icon mini-icon-link"></span></a>1.2 Sorting</h3>

<p>When you have a column in your CGridView that isn't an attribute of the model, Yii doesn't automatically know how to sort. But we can tell the <code>sort</code> parameter of the CActiveDataProvider how.<br>
    First, you have one or more columns in the View that show related data. Those columns have a 'name' attribute<br>
    (example: <code>array('name' =&gt; 'song.album')</code>), and we have to tell Yii how to sort that song's attribute called album.</p>

<p>You have to add the attribute called <code>song.album</code> to the attributes array of 'sort'. Then you tell Yii how to sort that attribute ascending, and descending. Like this:</p>

<div class="highlight"><pre><span class="cp">&lt;?php</span>
    <span class="k">return</span> <span class="k">new</span> <span class="nx">CActiveDataProvider</span><span class="p">(</span><span class="nv">$this</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span>
        <span class="s1">'criteria'</span> <span class="o">=&gt;</span> <span class="nv">$criteria</span><span class="p">,</span>
        <span class="s1">'sort'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span>
            <span class="s1">'attributes'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span>
                <span class="s1">'song.album'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span>
                    <span class="s1">'asc'</span><span class="o">=&gt;</span><span class="s1">'song.album'</span><span class="p">,</span>
                    <span class="s1">'desc'</span><span class="o">=&gt;</span><span class="s1">'song.album DESC'</span><span class="p">,</span>
                <span class="p">),</span>
                <span class="s1">'*'</span><span class="p">,</span>
            <span class="p">),</span>
        <span class="p">),</span>
    <span class="p">));</span>
</pre></div>

<p>Reference:  <a href="https://github.com/tom--/ManyMany/blob/yj-KeenLoading/protected/views/review/_caseOneGrid.php#L28">views/review/_caseOneGrid</a>,
    <a href="https://github.com/tom--/ManyMany/blob/yj-KeenLoading/protected/models/Review.php#L88">Review::searchOne()</a></p>

<h3>
    <a name="13-filtering" class="anchor" href="#13-filtering"><span class="mini-icon mini-icon-link"></span></a>1.3 Filtering</h3>

<p>This one will be a little bit more complex to implement. Think of the filters on the top of the CGridView as normal &lt;INPUT&gt; fields (because they are!), just like you would make them with <code>CHtml::activeTextField($review, 'review')</code>. Now of course, such a textfield wants a $model in the first parameter, and an attribute name in the second parameter.</p>

<p>We are going to base the filter's &lt;INPUT&gt; field on the related Model. The advantage of this is that you keep Yii's default functionality, like validation of the input.</p>

<p>First, in the Controller, we create such a model for the column with the related data: <code>$song   = new Song('search');</code><br>
    Then we unset its attributes, just like we do for the main model: <code>$song-&gt;unsetAttributes();</code></p>

<p>Okay, so now we have a $song model variable that we could use in an activeTextField. We have to pass this variable to the View. We use a more elegant approach to this, and put this variable $song inside a property of the main model Review. To do this, we first have to declare this property in the Review model: <code>public $searchSong;</code><br>
    Now, back to the controller, we put the Song model into that property: <code>$review-&gt;searchSong = $song;</code></p>

<p>In the view, we create a column with a self defined filter, like this:</p>

<div class="highlight"><pre><span class="cp">&lt;?php</span>
    <span class="k">array</span><span class="p">(</span>
        <span class="s1">'name'</span> <span class="o">=&gt;</span> <span class="s1">'song.name'</span><span class="p">,</span>
        <span class="s1">'filter'</span> <span class="o">=&gt;</span> <span class="nx">CHtml</span><span class="o">::</span><span class="na">activeTextField</span><span class="p">(</span><span class="nv">$review</span><span class="o">-&gt;</span><span class="na">searchSong</span><span class="p">,</span> <span class="s1">'name'</span><span class="p">),</span>
    <span class="p">),</span>
</pre></div>

<p>As you can see, we pass the Song model into the first parameter, and an attribute of that model into the second parameter. So far, so good.. If we refresh the page it shows an &lt;INPUT&gt; field on top of the column and we can type in there. But what happens if we type and then press <em>ENTER</em>? A submit action will be performed back to the Controller.</p>

<p>In the Controller, we have to catch the send data and place it into the $song model. We do that in the same way as you'd do that for the main Model:</p>

<div class="highlight"><pre><span class="cp">&lt;?php</span>
    <span class="k">if</span> <span class="p">(</span><span class="nb">isset</span><span class="p">(</span><span class="nv">$_GET</span><span class="p">[</span><span class="s1">'Song'</span><span class="p">]))</span> <span class="p">{</span>
        <span class="nv">$song</span><span class="o">-&gt;</span><span class="na">attributes</span> <span class="o">=</span> <span class="nv">$_GET</span><span class="p">[</span><span class="s1">'Song'</span><span class="p">];</span>
    <span class="p">}</span>
</pre></div>

<p>Now we have to go to the place where CGridView's searching magic actually happens, the method (usually <code>$model-&gt;search()</code>) in the main Model that providers a DataProvider to the CGridView.</p>

<p>Here, we simply add extra <code>$criteria-&gt;compare()</code>'s for the column we want to filter. We use the model inside the $searchXxx property to make this work, since we added the searched value earlier in the Controller.</p>

<div class="highlight"><pre><span class="cp">&lt;?php</span>
    <span class="nv">$criteria</span><span class="o">-&gt;</span><span class="na">compare</span><span class="p">(</span><span class="s1">'song.name'</span><span class="p">,</span> <span class="nv">$this</span><span class="o">-&gt;</span><span class="na">searchSong</span><span class="o">-&gt;</span><span class="na">name</span><span class="p">,</span> <span class="k">true</span><span class="p">);</span>
</pre></div>

<p>Reference:  <a href="https://github.com/tom--/ManyMany/blob/yj-KeenLoading/protected/controllers/ReviewController.php#L11">ReviewController::actionCaseOne()</a>,
    <a href="https://github.com/tom--/ManyMany/blob/yj-KeenLoading/protected/models/Review.php#L14">Review::$searchSong</a>,
    <a href="https://github.com/tom--/ManyMany/blob/yj-KeenLoading/protected/views/review/_caseOneGrid.php#L21">views/review/_caseOneGrid</a>,
    <a href="https://github.com/tom--/ManyMany/blob/yj-KeenLoading/protected/models/Review.php#L68">Review::searchOne()</a></p>

<h2>
    <a name="2-usecases" class="anchor" href="#2-usecases"><span class="mini-icon mini-icon-link"></span></a>2. UseCases</h2>

<h3>
    <a name="21-usecase-one---lazy-loading" class="anchor" href="#21-usecase-one---lazy-loading"><span class="mini-icon mini-icon-link"></span></a>2.1 UseCase One - Lazy Loading</h3>

<p><strong>Files/Methods</strong></p>

<ul>
    <li>models/Review::searchOne()</li>
    <li>controllers/ReviewController::actionCaseOne()</li>
    <li>views/review/caseOne</li>
    <li>views/review/_caseOneGrid</li>
</ul><p><strong>Explanation</strong><br>
    You group the primary keys of Review, and set together to true. You don't select any data from Genre, because then its lazy loaded for each row.<br>
    Because you're grouping the primary keys, the database returns only one row for each primary Model. This is why the pager doesn't break even though you set <code>$criteria-&gt;together</code> to <code>true</code>.<br>
    Additionally, make sure to set the relations in <code>$criteria-&gt;with</code> that are lazy loaded to <code>array('select'=&gt;false)</code>. This is more efficient since you're loading this data in a lazy way, so you don't need it in the first <code>SELECT</code> that the <code>CActiveDataProvider</code> performs..  </p>

<h3>
    <a name="22-usecase-two---group_concat" class="anchor" href="#22-usecase-two---group_concat"><span class="mini-icon mini-icon-link"></span></a>2.2 UseCase Two - GROUP_CONCAT</h3>

<p><strong>Files/Methods</strong></p>

<ul>
    <li>models/Review::searchTwo()</li>
    <li>controllers/ReviewController::actionCaseTwo()</li>
    <li>views/review/caseTwo</li>
    <li>views/review/_caseTwoGrid</li>
</ul><p><strong>Explanation</strong><br>
    Same as the explanation of UseCase One. In addition:<br>
    You set a <code>$criteria-&gt;select</code>, that selects a GROUP_CONCAT of the data from Genre. Don't forget to set the attributes of the main Model here or else those aren't loaded. Note that you don't have to include the primary keys in this select statement. Those are automatically added by Yii.<br>
    An example:  </p>

<div class="highlight"><pre><span class="cp">&lt;?php</span>
    <span class="nv">$criteria</span><span class="o">-&gt;</span><span class="na">select</span> <span class="o">=</span> <span class="k">array</span><span class="p">(</span>
        <span class="c1">//This attribute (allGenres) has to be added in the Model as a public property!</span>
        <span class="s1">'GROUP_CONCAT(genres.name ORDER BY genres.name SEPARATOR \', \') AS allGenres'</span><span class="p">,</span> 
        <span class="s1">'t.review'</span><span class="p">,</span>
   <span class="p">);</span>
</pre></div>

<p><em>Note: In addition, you can look at _caseTwoGrid.php to see how you can use a dropDownList in a gridview filter to search for a genre.</em>  </p>

<h3>
    <a name="23-usecase-three---custom-cactivefinder" class="anchor" href="#23-usecase-three---custom-cactivefinder"><span class="mini-icon mini-icon-link"></span></a>2.3 UseCase Three - Custom CActiveFinder</h3>

<p><strong>Files/Methods</strong></p>

<ul>
    <li>extensions/classMap/CActiveFinder</li>
    <li>/index.php</li>
    <li>models/Review::searchThree()</li>
    <li>controllers/ReviewController::actionCaseThree()</li>
    <li>views/review/caseThree</li>
    <li>views/review/_caseThreeGrid</li>
</ul><p><strong>Explanation</strong><br>
    Using classMap you import a custom CActiveFinder that enhances the eager loading magic of Yii. Easiest, but the disadvantage is that it does another 2 COUNT queries for the pager to work. Like the normal Eager loading way of Yii, this can become data inefficient in some cases.<br>
    The extra COUNT queries will only be performed when:
    1. the primary table is joined with HAS_MANY or MANY_MANY relations
    2. Columns of those relations are selected
    3. $criteria-&gt;group has been set
    4. $criteria-&gt;together has been set to true  </p>

<h3>
    <a name="24-usecase-four---keenloading" class="anchor" href="#24-usecase-four---keenloading"><span class="mini-icon mini-icon-link"></span></a>2.4 UseCase Four - KeenLoading</h3>

<p><strong>Files/Methods</strong></p>

<ul>
    <li>components/KeenActiveDataProvider</li>
    <li>models/Song::search()</li>
    <li>controllers/SongController::actionSongs()</li>
    <li>controllers/SongController::setSearchInputs()</li>
    <li>views/song/songsGrid</li>
    <li>views/song/_songsGrid</li>
</ul><p><strong>Explanation</strong><br>
    Related data is loaded in a keen fashion. Using KeenActiveDataProvider, the related models are loaded in a separate query and then put into the relation properties of the earlier loaded models.<br>
    In your Models search function, you return a new KeenActiveDataProvider, instead of a CActiveDataProvider. The KeenActiveDataProvider has another option named 'withKeenLoading', where you can set the relations that you want to load in a second(or multiple) queries.
    An example:</p>

<div class="highlight"><pre><span class="cp">&lt;?php</span>
    <span class="k">return</span> <span class="k">new</span> <span class="nx">KeenActiveDataProvider</span><span class="p">(</span><span class="nv">$this</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span>
        <span class="s1">'criteria'</span> <span class="o">=&gt;</span> <span class="nv">$criteria</span><span class="p">,</span>
        <span class="s1">'withKeenLoading'</span> <span class="o">=&gt;</span> <span class="k">array</span><span class="p">(</span><span class="s1">'hasGenres.genre'</span><span class="p">),</span>
    <span class="p">));</span>
</pre></div>

<p><em>Note: In addition, you can look at Song::search() to see how you can enable the gridviews filter to search for multiple Genres using a comma or space in the input field.</em><br><em>2nd Note: You can also look at SongController::setSearchInputs() to take a look at a method that generalizes a way to set search inputs.</em>  </p>

<h2>
    <a name="3-extra-full-explanation-of-keenactivedataprovider" class="anchor" href="#3-extra-full-explanation-of-keenactivedataprovider"><span class="mini-icon mini-icon-link"></span></a>3. Extra: Full explanation of KeenActiveDataProvider</h2>

<p>KeenActiveDataProvider implements a data provider based on ActiveRecord and is extended from CActiveDataProvider.</p>

<p>KeenActiveDataProvider provides data in terms of ActiveRecord objects. It uses
    the <code>CActiveRecord::findAll</code> method to retrieve the data from database.
    The criteria property can be used to specify various query options. If
    you add a 'with' option to the criteria, and the same relations are added to the
    'withKeenLoading' option, they will be automatically set to select no columns.<br>
    ie. <code>array('author'=&gt;array('select'=&gt;false)</code></p>

<p>HAS_ONE and BELONG_TO type relations shouldn't be set in withKeenLoading,
    but in the $criteria-&gt;with, because its more efficient to load them in the
    normal query.</p>

<p>There will be a <code>CDbCriteria-&gt;group</code> set automatically, that groups the model
    to its own primary keys.</p>

<p>The relation names you specify in the 'withKeenLoading' property of the
    configuration array will be loaded in a keen fashion. A separate database
    query will be done to pull the data of those specified related models.</p>

<p>For example,</p>

<div class="highlight"><pre><span class="cp">&lt;?php</span>
    <span class="nv">$dataProvider</span><span class="o">=</span><span class="k">new</span> <span class="nx">KeenActiveDataProvider</span><span class="p">(</span><span class="s1">'Post'</span><span class="p">,</span> <span class="k">array</span><span class="p">(</span>
        <span class="s1">'criteria'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span>
            <span class="s1">'condition'</span><span class="o">=&gt;</span><span class="s1">'status=1'</span><span class="p">,</span>
            <span class="s1">'with'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span><span class="s1">'author'</span><span class="p">),</span>
        <span class="p">),</span>
        <span class="s1">'pagination'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span>
            <span class="s1">'pageSize'</span><span class="o">=&gt;</span><span class="mi">20</span><span class="p">,</span>
        <span class="p">),</span>
        <span class="s1">'withKeenLoading'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span>
            <span class="s1">'author'</span><span class="p">,</span>
            <span class="s1">'comments'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span><span class="s1">'condition'</span><span class="o">=&gt;</span><span class="s1">'approved=1'</span><span class="p">,</span> <span class="s1">'order'</span><span class="o">=&gt;</span><span class="s1">'create_time'</span><span class="p">),</span>
        <span class="p">)</span>
<span class="p">));</span>
</pre></div>

<p>The property withKeenLoading can be set as a string with comma separated relation names,
    or an array. The array keys are relation names, and the array values are
    the corresponding query options.</p>

<p>In some cases, you don't want all relations to be Keenly loaded in a single
    query because of data efficiency. In that case, you can group relations in multiple queries
    using a multidimensional array. (Arrays inside an array.) Each array will
    be keenly loaded in a separate query.
    Example:</p>

<div class="highlight"><pre><span class="cp">&lt;?php</span>
    <span class="s1">'withKeenLoading'</span><span class="o">=&gt;</span><span class="k">array</span><span class="p">(</span>
        <span class="k">array</span><span class="p">(</span><span class="s1">'relationA'</span><span class="p">,</span><span class="s1">'relationB'</span><span class="p">),</span>
        <span class="k">array</span><span class="p">(</span><span class="s1">'relationC'</span><span class="p">)</span>
    <span class="p">)</span>
</pre></div></article>