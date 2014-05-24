<?
class ArticleParser extends Parser
{
   public function getArticle($storyID)
   {
      $storyID = intval($storyID);
      $this->init($this->download("http://www.shacknews.com/onearticle.x/$storyID"));
      return $this->parseArticle($this);
   }

   public function parseArticle(&$p)
   {
      $story = array(
         'preview'       => false,
         'name'          => false,
         'body'          => false,
         'date'          => false,
         'comment_count' => false,
         'id'            => false);
   
      $p->seek(1, '<div class="story">');
      
      $story['id'] = $p->clip(
         array('<a href="http://www.shacknews.com/onearticle.x/', 'onearticle.x/', '/'),
         '">');

      $story['name'] = $p->clip(
         '>',
         '</a>');

      $story['date'] = $p->clip(
         array('<span class="date">', '>'),
         '</span>');
         
      $story['body'] = trim($p->clip(
         array('<div class="body">', '>'),
         '<div class="comments">'));
      
      # Trim the extra </div> from the end of the body.
      $story['body'] = substr($story['body'], 0, -6);
      
      $story['preview'] = substr(strip_tags($story['body']), 0, 500);
      
      $story['comment_count'] = $p->clip(
         array('<span class="commentcount">', '>'),
         ' ');
      
      if ($story['comment_count'] == 'No')
         $story['comment_count'] = '0';
      else
         $story['comment_count'] = intval($story['comment_count']);
      
      return $story;
   }
}

function ArticleParser()
{
   return new ArticleParser();
}