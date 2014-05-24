<?
class StoriesFeedParser
{
   function getStories()
   {
      # The chatty no longer has a concept of MD/ER/etc.  We will just show the 
      # one chatty "story".
      $feed = array();
      
      # Add the latest chatty to the top of the list.
      $feed[] = array(
         'title'    => 'Latest Chatty',
         'story_id' => 0);
   
      return $feed;
   }
}

function StoriesFeedParser()
{
   return new StoriesFeedParser();
}