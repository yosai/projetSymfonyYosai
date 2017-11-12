<?php

namespace RouteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="front_home")
     * @Route("/category/[slug]", name="front_category")
     * @Route("/category/[category]/article/[slug]/", name="front_article")
     * @Route("/search", name="front_search")
     * @Route("/login", name="front_login")
     */
    public function frontAction()
    {

        // @todo Make routes
        //       Set front_category route to be /category/[slug]
        //       Set front_article  route to be /category/[category]/article/[slug]
        //       Set front_search   route to be /search
        //       Set front_login    route to be /login

        return $this->render("::front.html.twig");
    }

    /**
     * @Route("/admin", name="admin_home")
     * @Route("/admin/settings/[options]", name="admin_settings")
     * @Route("/admin/modules/[options]", name="admin_modules")
     * @Route("/admin/content/[options]", name="admin_content")
     */
    public function adminAction()
    {
        // @todo Make routes
        //       Set admin_settings route to be /admin/settings/[options] with [options] set to null by default
        //       Set admin_modules  route to be /admin/modules/[options]  with [options] set to null by default
        //       Set admin_content  route to be /admin/content/[options]  with [options] set to null by default

        return $this->render("::admin.html.twig");
    }
}
