<?php
namespace ContentBundle\Util;

use ContentBundle\Entity\Category;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;

/**
 * Category management
 *
 * [CREATE, UPDATE, GET, DELETE]
 */
class CategoryManager
{
    private $em;
    private $slugify;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->slugify = new Slugify();
    }

    /**
     * Create a new category
     * @param  String $name
     * @param  Integer $parentId the ID of the parent category
     * @return void
     */
    public function create(
        $name,
        $parentId = NULL
    ) {
        // @todo Make the create method
        //       Create a new category
        $category = new Category();
        $category->setName($name);
        $category->setParentId($parentId);
        $category->setSlug($this->slugify->slugify($category->getName()));
        $this->em->persist($category);
        $this->em->flush();
    }

    public function update($category)
    {
        $this->em->flush();
    }

    /**
     * Get a category or all categories
     * @param  Integer $id
     * @return Category|Category[]
     */
    public function get($id = NULL)
    {
        // @todo Make the get method
        //       Find a category from ID or if no ID find all categories, then return
        if($id = NULL){


            return   $this->em->getRepository('ContentBundle:Category')->findAll();

        }
        else{

            return $this->em->getRepository('ContentBundle:Category')->find($id);


        }

    }

    /**
     * Delete a specific category
     *
     * @param integer $id
     * @return void
     */
    public function delete($id)
    {
        // @todo Make the create method
        //       Find the category and delete it
        $this->em->remove($this->get($id));
        $this->em->flush();
    }
}
