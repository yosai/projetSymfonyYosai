<?php
namespace ContentBundle\Util;

use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use ContentBundle\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * User management
 *
 * [CREATE, UPDATE, GET, DELETE]
 */
class UserManager
{
    private $um;
    private $em;
    private $currentUser;

    public function __construct(FOSUserManager $um, EntityManager $em, $currentUser)
    {
        $this->um = $um;
        $this->em = $em;
        $this->currentUser = $currentUser;
    }

    /**
     * Create a user
     * @param  String $username
     * @param  String $email
     * @param  String $plain_password
     * @param  array  $roles
     * @return void
     */
    public function create(
        $username,
        $email,
        $plain_password,
        $roles = array(),
        $enabled = false
    ) {
        // @todo Make the create method
        //       Create a user using the FOSUserManager ($this->um)
        $user = $this->um->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($plain_password);
        $user->setRoles($roles);
        $user->setEnabled(true);
    }

    public function update($user)
    {
        $this->um->updateUser($user);
    }

    /**
     * Get a user or all users
     * @param  Integer $id
     * @return User|User[]
     */
    public function get($id = NULL)
    {
        // @todo Make the get method
        //       Find a user from ID or get all users, then return
        if($id = NULL){


            return   $this->em->getRepository('ContentBundle:User')->findAll();

        }
        else{

            return $this->em->getRepository('ContentBundle:User')->find($id);


        }
    }

    public function getCurrent()
    {
        return $this->currentUser;
    }

    /**
     * Delete a user
     * @param  Integer $id
     * @return void
     */
    public function delete($id)
    {
        // @todo Make the delete method
        //       Delete a user
        $this->em->remove($this->get($id));
        $this->em->flush();
    }
}
