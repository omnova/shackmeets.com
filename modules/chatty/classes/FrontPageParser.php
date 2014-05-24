<?
class FrontPageParser extends Parser
{
   public function getStories($page = 1)
   {
      $html = $this->download("http://www.shacknews.com/?page=$page", true);
      return $this->parseStories($html);
   }
   
   private function parseStories($html)
   {
      $articleParser = ArticleParser();
      $stories       = array();
   
      $this->init($html);
      $this->seek(1, '<div class="news">');
      
      while ($this->peek(1, '<div class="story">') !== false)
      {
         $story     = $articleParser->parseArticle($this);
         $stories[] = $story;
      }
      
      return $stories;
   }
}

function FrontPageParser()
{
   return new FrontPageParser();
}