<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Categories_Manager extends ModuleInterface
{
    public $name = "admin_categories_manager";
    public $displayName = "Admin Categories Manager";
    public $description = "Administrator panel categories manager page";
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

        $pattern = "/categories|category-create|category-(\d+)-(.+)-(edit|delete)/";
        preg_match_all($pattern, $this->route_params["options"], $matches, PREG_SET_ORDER);

        if (count($matches) == 0)
            return;

        $category_manager = $this->container->get('content.manager')->getCategoryManager();

        // Create writer page
        switch ($matches[0][0]) {
            case "category-create":
                return $this->createArticle($category_manager);
            default:
                if(count($matches[0]) > 1) {
                    $category_id = $matches[0][1];
                    $action = $matches[0][3];

                    switch ($action) {
                        case "edit":
                            return $this->editArticle($category_manager, $category_id);
                        case "delete":
                            return $this->deleteArticle($category_manager, $category_id);
                    }
                }
                // no break
        }

        $params = array();
        $params["categories"] = $category_manager->get();

        return array(
            "template" => __DIR__."/admin_categories_manager.html.twig",
            "params" => $params
        );
    }

    public function editArticle($category_manager, $category_id)
    {
        $category = $category_manager->get($category_id);

        $this->updateArticle($category_manager, $category);

        return array(
            "template" => __DIR__."/admin_categories_manager_create.html.twig",
            "params" => array(
                "category" => $category,
                "categories" => $category_manager->get()
            )
        );
    }

    public function deleteArticle($category_manager, $category_id)
    {
        $category = $category_manager->get($category_id);
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request->getMethod() == "POST") {
            $data = $request->request->all();

            if (array_key_exists("yes", $data))
                $category_manager->delete($category_id);

            $this->redirectToRoute("admin_content", array(
                "options" => "categories"
            ));
        }

        return array(
            "template" => __DIR__."/admin_categories_manager_delete.html.twig",
            "params" => array(
                "category" => $category
            )
        );
    }

    private function createArticle($category_manager)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request->getMethod() == "POST") {
            $data = $request->request->all();
            $keys = array("name");

            $valid = true;
            foreach ($keys as $key)
                $valid = $valid AND isset($data[$key]);
            $data["parent"] = (isset($data["parent"]) AND !empty($data["parent"]))? $data["parent"]: NULL;

            if ($valid) {
                $category_manager->create($data["name"], $data["parent"]);

                $this->redirectToRoute("admin_content", array(
                    "options" => "categories"
                ));
            }
        }

        return array(
            "template" => __DIR__."/admin_categories_manager_create.html.twig",
            "params" => array(
                "categories" => $category_manager->get()
            )
        );
    }

    private function updateArticle($category_manager, $category)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request->getMethod() == "POST") {
            $data = $request->request->all();

            if (isset($data["name"]))
                $category->setName($data["name"]);
            if (isset($data["parent"])) {
                $data["parent"] = empty($data["parent"])? NULL: $data["parent"];
                $category->setParentId($data["parent"]);
            }

            $category_manager->update($category);

            $this->redirectToRoute("admin_content", array(
                "options" => "categories"
            ));
        }
    }
}
