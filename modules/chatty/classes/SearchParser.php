<?
class SearchParser extends Parser
{
   public function search($terms, $author, $parentAuthor, $category, $page)
   {
      if (empty($category))
         $category = 'all';
   
      $url = 'http://www.shacknews.com/search'
         . '?chatty=1'
         . '&type=4'
         . '&chatty_term=' . urlencode($terms)
         . '&chatty_user=' . urlencode($author)
         . '&chatty_author=' . urlencode($parentAuthor) 
         . '&chatty_filter=' . urlencode($category)
         . '&page=' . urlencode($page)
         . '&result_sort=postdate_desc';
      
      $this->init($this->download($url));
      
      $results = array();
      
      while ($this->peek(1, '<li class="result chatty">') !== false)
      {
         $o = array(
            'id' => false,
            'preview' => false,
            'author' => false,
            'date' => false,
            
            # Not provided by the Shacknews search:
            'parentAuthor' => '',
            'category' => 'ontopic',
            'story_id' => 0,
            'story_name' => '',
            'thread_id' => 0);
            
         # <span class="chatty-author">ECO:</span> 
         $o['author'] = $this->clip(
            array('<span class="chatty-author"', '>'),
            ':</span>');
         
         # <a href="/chatty/25556173">Preview text...</a>
         $o['id'] = $this->clip(
            array('<a href="/chatty', 'chatty/', '/'),
            '"');
         $o['preview'] = $this->clip(
            '>',
            '</a>');
         
         # <span class="chatty-posted">Posted Mar 24, 2011 3:50pm PDT</span>
         $o['date'] = $this->clip(
            array('<span class="chatty-posted"', '>'),
            '</span>');
         
         $results[] = $o;
      }
      
      return $results;
   }
}

function SearchParser()
{
   return new SearchParser();
}