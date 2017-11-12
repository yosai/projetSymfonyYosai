<?php
namespace ContentBundle\Util;

use ContentBundle\Entity\Article;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;

/**
 * Article management
 *
 * [CREATE, UPDATE, GET, DELETE]
 */
class ArticleManager
{
    private $em;
    private $slugify;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->slugify = new Slugify();
    }

    /**
     * Create an article in database
     * @param  String $name
     * @param  String $cover       file path
     * @param  String $text
     * @param  Integer $category_id
     * @return void
     */
    public function create(
        $name,
        $cover,
        $text,
        $category_id
    ) {
        // @todo Make the creation method


        $article = new Article();

        $article->setName($name);
        $article->setCategoryId($category_id);
        $article->setCover($cover);
        $article->setText($text);
        $article->setSlug($this->slugify->slugify($article->getName()));
       // $this->slugify->slugify($article->getName());

        //       Create an article using the entity manager
       $this->em->persist($article);


         $this->em->flush();
    }

    public function update($article)
    {
        $this->em->flush();
    }

    /**
     * Get an article from database
     * @param  Integer $id
     * @return Article|Article[]
     */
    public function get($id = NULL)
    {
        // @todo Make the get method
        //       Find an article from ID or if no ID find all articles, then return
       // $em =this.$this->em;
        if($id = NULL){


            return   $this->em->getRepository('ContentBundle:Article')->findAll();

        }
        else{

                return $this->em->getRepository('ContentBundle:Article')->find($id);


        }
    }

    /**
     * Get an article and delete it
     * @param  Integer $id
     * @return void
     */
    public function delete($id)
    {
        // @todo Make the delete method
        //       Find the article and delete it
        $this->em->remove($this->get($id));
        $this->em->flush();



    }
}
