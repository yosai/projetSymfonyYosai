<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Users_Manager extends ModuleInterface
{
    public $name = "admin_users_manager";
    public $displayName = "Admin Users Manager";
    public $description = "Administrator panel user manager page";
    public $pages = array(
        "admin_content",
    );

    public function install()
    {
        if (parent::install() &&
            $this->registerHook("displayContentMain"))
            return true;
        return false;
    }

    public function hook_displayContentMain()
    {
        if (!array_key_exists("options", $this->route_params))
            return;

        $pattern = "/users|(writer|admin)-create|user-(\d+)-(\w+)-(edit|delete)/";
        preg_match_all($pattern, $this->route_params["options"], $matches, PREG_SET_ORDER);

        if (count($matches) == 0)
            return;

        $user_manager = $this->container->get('content.manager')->getUserManager();

        // Create writer page
        switch ($matches[0][0]) {
            case "writer-create":
                return $this->createWriter($user_manager);
            case "admin-create":
                return $this->createAdmin($user_manager);
            default:
                if(count($matches[0]) > 1) {
                    $user_id = $matches[0][2];
                    $action = $matches[0][4];

                    switch ($action) {
                        case "edit":
                            return $this->editUser($user_manager, $user_id);
                        case "delete":
                            return $this->deleteUser($user_manager, $user_id);
                    }
                }
                // no break
        }

        $params = array();

        $users = $user_manager->get();
        $params["users_writer"] = array();
        $params["users_admin"] = array();

        foreach ($users as $user) {
            if (in_array("ROLE_ADMIN", $user->getRoles()))
                $params["users_admin"][] = $user;
            elseif (in_array("ROLE_WRITER", $user->getRoles()))
                $params["users_writer"][] = $user;
        }

        return array(
            "template" => __DIR__."/admin_users_manager.html.twig",
            "params" => $params
        );
    }

    private function createWriter($user_manager)
    {
        $this->createUser($user_manager, array("ROLE_WRITER"));

        return array(
            "template" => __DIR__."/admin_users_manager_create.html.twig",
            "params" => array(
                "title" => "Create a writer user"
            )
        );
    }

    private function createAdmin($user_manager)
    {
        $this->createUser($user_manager, array("ROLE_ADMIN"));

        return array(
            "template" => __DIR__."/admin_users_manager_create.html.twig",
            "params" => array(
                "title" => "Create an administrator user"
            )
        );
    }

    public function editUser($user_manager, $user_id)
    {
        $title = NULL;
        $user = $user_manager->get($user_id);

        if (in_array("ROLE_ADMIN", $user->getRoles()))
            $title = "Edit an administrator user";
        elseif (in_array("ROLE_WRITER", $user->getRoles()))
            $title = "Edit a writer user";

        $this->updateUser($user_manager, $user);

        return array(
            "template" => __DIR__."/admin_users_manager_create.html.twig",
            "params" => array(
                "title" => $title,
                "username" => $user->getUsername(),
                "email" => $user->getEmail(),
                "nopassword" => true
            )
        );
    }

    public function deleteUser($user_manager, $user_id)
    {
        $user = $user_manager->get($user_id);

        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request->getMethod() == "POST") {
            $data = $request->request->all();

            if (array_key_exists("yes", $data))
                $user_manager->delete($user_id);

            $this->redirectToRoute("admin_content", array(
                "options" => "users"
            ));
        }

        return array(
            "template" => __DIR__."/admin_users_manager_delete.html.twig",
            "params" => array(
                "user" => $user
            )
        );
    }

    private function createUser($user_manager, $roles)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request->getMethod() == "POST") {
            $data = $request->request->all();
            $keys = array("username", "email", "password");

            $valid = true;
            foreach ($keys as $key)
                $valid = $valid AND isset($data[$key]);

            if ($valid) {
                $user_manager->create(
                    $data["username"],
                    $data["email"],
                    $data["password"],
                    $roles,
                    true
                );

                $this->redirectToRoute("admin_content", array(
                    "options" => "users"
                ));
            }
        }
    }

    private function updateUser($user_manager, $user)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request->getMethod() == "POST") {
            $data = $request->request->all();

            // Update user
            if (isset($data["username"]))
                $user->setUsername($data["username"]);
            if (isset($data["email"]))
                $user->setEmail($data["email"]);
            if (isset($data["password"]))
                $user->setPlainPassword($data["password"]);
            $user_manager->update($user);

            $this->redirectToRoute("admin_content", array(
                "options" => "users"
            ));
        }
    }
}
