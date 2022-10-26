<?php
namespace app\core;

use Exception;

class Application
{

    public static string $ROOT_DIR;

    public string $userClass;

    public string $layout = 'main';

    public Router $router;

    public Request $request;

    public Response $response;

    public static Application $app;

    public Session $session;

    public Database $db;

    public ?UserModel $user;

    public ?Controller $controller = null;

    public View $view;

    public function __construct($rootPath, array $config)
    {
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();
        $this->db = new Database($config['db']);
        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }
    }


    public static function isGuest()
    {
        return !self::$app->user;
    }


    public function run()
    {
        try {
            $this->router->resolve();
        } catch (Exception $e) {
            echo $this->view->renderView('_404', [
                'exception' => $e
            ]);
        }
    }

    /**
     * Get the value of controller
     */ 
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set the value of controller
     *
     * @return  self
     */ 
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }


    public function login(DBModel $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }


    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }
}