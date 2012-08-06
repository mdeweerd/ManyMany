<?php
class Help extends CComponent
{

    /**
     * @var string PHP expression for the row number in a CGV
     */
    public static $gridRowExp = '$this->grid->dataProvider->pagination->currentPage
			                     * $this->grid->dataProvider->pagination->pageSize + $row + 1';


    /**
	 * Recursively convert an array to html tags.
	 *
	 * @static
	 * @param array $elems Page elements to be converted to HTML tags
	 * @param null $baseClass Class attr of the top level enclosing tag
	 * @param bool $space Set true to put spaces between the tags
	 * @param string $tag The HTML tag to use
	 * @return string HTML tags containing the given elements
	 */
	public static function tags($elems,	$baseClass = null, $tag = 'span', $glue = ', ')
    {
        $tags = array();
        if ($elems) {
            foreach ($elems as $class => $item) {
                if(!empty($item)) {
                    $tags[] = CHtml::tag($tag, array('class'=>$class), array_pop($item));
                }
            }
        }
        return CHtml::tag($tag, array('class'=>$baseClass), implode($glue, $tags));
	}

}