<?
require_once 'Config.php';
require_once 'Bookmarks.php';

require_once 'Parser.php';
require_once 'ChattyParser.php';
require_once 'ThreadParser.php';
require_once 'SearchParser.php';
require_once 'StoriesFeedParser.php';
require_once 'FrontPageParser.php';
require_once 'ArticleParser.php';
require_once 'MessageParser.php';
require_once 'ProfileParser.php';

require_once 'ClassicAdapter.php';
require_once 'SearchEngine.php';

error_reporting(E_ALL);

function check_set($array, $key)
{
   if (isset($array[$key]))
      return $array[$key];
   else
      die("Missing parameter $key.");
}

function array_top(&$array)
{
   return $array[count($array) - 1];
}

function collapse_whitespace($str)
{
	$str = str_replace("\n", ' ', $str);
	$str = str_replace("\t", ' ', $str);
	$str = str_replace("\r", ' ', $str);
	
	while (strpos($str, '  ') !== false)
		$str = str_replace('  ', ' ', $str);
	
	return $str;
}
