<?php
  jimport( 'joomla.application.component.model' );
              
                     
  class SkautisModelArticles extends JModel{
    
    /**
     *  Funkce vracející obsah konkrétního článku uloženého v joomle
     */         
    public function getArticleContent($articleId){
      $article=$this->getArticle($articleId);
      return $article->introtext.$article->fulltext;
    }
    
    /**
     *  Funkce vracející z DB záznam konkrétního článku v joomle
     */         
    public function getArticle($articleId){
      $db=$this->getDBO();
      $db->setQuery('SELECT * FROM #__content WHERE id='.$db->quote($articleId).' LIMIT 1;')
      return $db->loadObject();
    }
    
  }
?>
