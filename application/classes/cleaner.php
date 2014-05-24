<?php defined('SYSPATH') OR die('No direct access allowed.');

class Cleaner
{
  public static function strip_entities($text)
  {
    return htmlentities($text);
  }
  
  public static function strip_and_replace($text)
  {
    $stripped_text = htmlentities($text);
    
    return preg_replace('/\n/', '<br/>', $stripped_text);		
  }
  
  public static function strip_with_shacktags($text)
  {
    $stripped_text = htmlentities($text);
  
    $text = preg_replace('/\n/', '<br/>', $stripped_text);	
  
    $matches = array('/r{/', '/}r/',
                     '/g{/', '/}g/',
                     '/b{/', '/}b/',
                     '/y{/', '/}y/',
                     '/l\[/', '/\]l/',
                     '/n\[/', '/\]n/',
                     '/p\[/', '/\]p/',
                     '/e\[/', '/\]e/',
                     '%/\[%', '%\]/%', 
                     '%b\[%', '%\]b%', 
                     '%\*\[%', '%\]\*%',
                     '%q\[%', '%\]q%',
                     '%s\[%', '%\]s%',
                     '%_\[%', '%\]_%',
                     '%-\[%', '%\]-%',
                     '%o\[%', '%\]o%',
                     '%/{{%', '%}}/%'
                     );
                     
    $replacements = array('<span class="tag-red">', '</span>',
                          '<span class="tag-green">', '</span>',
                          '<span class="tag-blue">', '</span>',
                          '<span class="tag-yellow">', '</span>',
                          '<span class="tag-lime">', '</span>',
                          '<span class="tag-orange">', '</span>',
                          '<span class="tag-pink">', '</span>',
                          '<span class="tag-olive">', '</span>',
                          '<span class="tag-italic">', '</span>',
                          '<span class="tag-bold">', '</span>',
                          '<span class="tag-bold">', '</span>',
                          '<span class="tag-quote">', '</span>',
                          '<span class="tag-sample">', '</span>',
                          '<span class="tag-underline">', '</span>',
                          '<span class="tag-strike">', '</span>',
                          '<span class="tag-spoiler">', '</span>',
                          '<span class="tag-code">', '</span> (who uses the code tag for a shackmeet?)'
                          );
    
    $text = preg_replace($matches, $replacements, $text);
    
    return $text;
  }
  
  // For shackmessages
  public static function strip_shacktags($text)
  {
    //$text = htmlentities($text);
  
    //$text = preg_replace('/\n/', '<br/>', $text);	
  
    $matches = array('/r{/', '/}r/',
                     '/g{/', '/}g/',
                     '/b{/', '/}b/',
                     '/y{/', '/}y/',
                     '/l\[/', '/\]l/',
                     '/n\[/', '/\]n/',
                     '/p\[/', '/\]p/',
                     '/e\[/', '/\]e/',
                     '%/\[%', '%\]/%', 
                     '%b\[%', '%\]b%', 
                     '%\*\[%', '%\]\*%',
                     '%q\[%', '%\]q%',
                     '%s\[%', '%\]s%',
                     '%_\[%', '%\]_%',
                     '%-\[%', '%\]-%',
                     '%o\[%', '%\]o%',
                     '%/{{%', '%}}/%'
                     );
                     
    $replacements = array('', '',
                          '', '',
                          '', '',
                          '', '',
                          '', '',
                          '', '',
                          '', '',
                          '', '',
                          '', '',
                          '', '',
                          '', '',
                          '', '',
                          '', '',
                          '', '',
                          '', '',
                          '', '',
                          '', ''
                          );
    
    $text = preg_replace($matches, $replacements, $text);
    
    return $text;
  }
}

?>