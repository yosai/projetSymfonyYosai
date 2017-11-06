<?php

use ModuleBundle\Util\ModuleInterface;
use Symfony\Component\Security\Core\Security;

class Login extends ModuleInterface
{
    public $name = "login";
    public $displayName = "Login Page";
    public $description = "The login form to connect to back end";
    public $pages = array("front_login");

    public function install()
    {
        if (parent::install() &&
            $this->registerHook("displayContentMain"))
            return true;
        return false;
    }

    public function hook_displayContentMain()
    {
        $params = array();

        $this->request = $this->container->get('request_stack')->getCurrentRequest();
        $this->session = $this->container->get('session');

        // START error handling
        if ($this->request->attributes->has(Security::AUTHENTICATION_ERROR))
            $error = $this->request->attributes->get(Security::AUTHENTICATION_ERROR);
        elseif (null !== $this->session && $this->session->has(Security::AUTHENTICATION_ERROR))
            $error = $this->session->get(Security::AUTHENTICATION_ERROR);
        else
            $error = '';

        if ($error)
            $params["error"] = $error->getMessage();
        // END error handling

        // START csrf token
        $token = $this->container->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        $params["csrf_token"] = $token;
        // END csrf token

        return array(
            "template" => __DIR__."/login.html.twig",
            "params" => $params
        );
    }
}
