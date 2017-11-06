<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Articles_Manager extends ModuleInterface
{
    public $name = "admin_articles_manager";
    public $displayName = "Admin Articles Manager";
    public $description = "Administrator panel articles manager page";
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

        $pattern = "/articles|article-create|article-(\d+)-(.+)-(edit|delete)/";
        preg_match_all($pattern, $this->route_params["options"], $matches, PREG_SET_ORDER);

        if (count($matches) == 0)
            return;

        $content_manager = $this->container->get('content.manager');
        $article_manager = $content_manager->getArticleManager();

        // Create writer page
        switch ($matches[0][0]) {
            case "article-create":
                return $this->createArticle($article_manager);
            default:
                if(count($matches[0]) > 1) {
                    $article_id = $matches[0][1];
                    $action = $matches[0][3];

                    switch ($action) {
                        case "edit":
                            return $this->editArticle($article_manager, $article_id);
                        case "delete":
                            return $this->deleteArticle($article_manager, $article_id);
                    }
                }
                // no break
        }

        $ids = array();
        $categories = $content_manager->getCategoryManager()->get();
        foreach ($categories as $category)
            $ids[$category->getId()] = $category;

        $params = array(
            "articles" => $article_manager->get(),
            "categories" => $ids
        );

        return array(
            "template" => __DIR__."/admin_articles_manager.html.twig",
            "params" => $params
        );
    }

    private function editArticle($article_manager, $article_id)
    {
        $article = $article_manager->get($article_id);
        $categories = $this->container->get('content.manager')->getCategoryManager()->get();

        $this->updateArticle($article_manager, $article);

        return array(
            "template" => __DIR__."/admin_articles_manager_create.html.twig",
            "params" => array(
                "article" => $article,
                "categories" => $categories
            )
        );
    }

    private function deleteArticle($article_manager, $article_id)
    {
        $article = $article_manager->get($article_id);
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request->getMethod() == "POST") {
            $data = $request->request->all();

            if (array_key_exists("yes", $data)) {
                unlink(__DIR__ . "/cover/" . $article->getCover());
                $article_manager->delete($article_id);
            }

            $this->redirectToRoute("admin_content", array(
                "options" => "articles"
            ));
        }

        return array(
            "template" => __DIR__."/admin_articles_manager_delete.html.twig",
            "params" => array(
                "article" => $article
            )
        );
    }

    private function createArticle($article_manager)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $categories = $this->container->get('content.manager')->getCategoryManager()->get();

        if ($request->getMethod() == "POST") {
            $data = $request->request->all();
            $keys = array("name", "cover", "text", "category");

            // Manage file
            $cover = $request->files->get('cover');
            if (isset($cover)) {
                $cover_name = md5(uniqid()).".".$cover->guessExtension();
                $cover->move(__DIR__."/cover", $cover_name);
                $data["cover"] = $cover_name;
            }

            $valid = true;
            foreach ($keys as $key)
                $valid = $valid AND isset($data[$key]);

            if ($valid) {
                $article_manager->create(
                    $data["name"],
                    $data["cover"],
                    $data["text"],
                    $data["category"]
                );

                $this->redirectToRoute("admin_content", array(
                    "options" => "articles"
                ));
            }
        }

        return array(
            "template" => __DIR__."/admin_articles_manager_create.html.twig",
            "params" => array(
                "categories" => $categories
            )
        );
    }

    private function updateArticle($article_manager, $article)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request->getMethod() == "POST") {
            $data = $request->request->all();

            if (isset($data["name"]))
                $article->setName($data["name"]);
            if (isset($data["text"]))
                $article->setText($data["text"]);
            if (isset($data["category"]))
                $article->setCategoryId($data["category"]);

            $cover = $request->files->get('cover');
            if (isset($cover)) {
                $cover_name = md5(uniqid()).".".$cover->guessExtension();
                $cover->move("cover", $cover_name);
                $article->setCover($cover_name);
            }

            $article_manager->update($article);

            $this->redirectToRoute("admin_content", array(
                "options" => "articles"
            ));
        }
    }
}
