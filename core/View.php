<?php
namespace app\core;


class View 
{

    public function renderView($view, $params = [])
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();
        echo str_replace('{{content}}', $viewContent, $layoutContent);
    }


    public function renderContent($content)
    {

        $layout = $this->layoutContent();
        echo str_replace('{{content}}', $content, $layout);
        
    }


    protected function layoutContent()
    {
        $layout = Application::$app->layout;
        if (Application::$app->controller) {
            $layout = Application::$app->controller->layout;
        }
        ob_start();
        include_once __DIR__ . "/../views/layouts/$layout.php";
        return ob_get_clean();
    }


    protected function renderOnlyView($view, $params)
    {

        foreach ($params as $key => $value) {
            $$key = $value;
        }

        ob_start();
        include_once __DIR__ . '/../views/' . $view . '.php';
        return ob_get_clean();
    }
}