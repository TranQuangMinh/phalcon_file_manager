<?php
namespace MINI\Filemanager\Controller;

class ErrorController extends \MINI\Filemanager\Controller\BaseController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function error404Action()
    {
        $this->response->setStatusCode(404, 'Page not found.');
        $this->view->setVars(array(
            'message' => 'Page not found'
        ));
        $this->view->pick('default/error/error');
    }

    public function errorAction($e)
    {
        $this->view->setVars(array(
            'message' => $e->getMessage()
        ));
        $this->view->pick('default/error/error');
    }
}
