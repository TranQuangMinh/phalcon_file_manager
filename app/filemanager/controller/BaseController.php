<?php
namespace MINI\Filemanager\Controller;

use Phalcon\Mvc\Controller;

class BaseController extends Controller
{
    public function initialize()
    {
        if (!$this->session->has('PATH_CURRENT')){
            $this->session->set('PATH_CURRENT', $this->config->application->upload_dir);
        }

        if (!$this->session->has('USER') && $this->dispatcher->getActionName() != 'login'){
            $this->response->redirect(array(
               'for' => 'login'
            ));
        }

        if ($this->session->has('USER') && $this->dispatcher->getActionName() == 'login'){
            $this->response->redirect(array(
               'for' => 'index'
            ));
        }
    }

    public function loginAction()
    {
        $mess = '';
        if ($this->request->isPost()) {
            $user = $this->request->getPost('username', array('trim', 'striptags'), '');
            $pass = $this->request->getPost('pass', array('trim', 'striptags'), '');

            if (md5($user) == $this->config->application->token_user && md5($pass) == $this->config->application->token) {
                $this->session->set('USER', 'true');
                $this->response->redirect(array(
                    'for' => 'index'
                ));
            } else {
                $mess = 'Tài khoản hoặc mật khẩu không đúng.';
            }

        }

        $this->view->setVar('mess', $mess);
        $this->view->pick('default/main/login');
    }

    public function logoutAction()
    {
        if ($this->session->has('USER')){
            $this->session->remove('USER');
            $this->response->redirect(array(
                'for' => 'login'
            ));
        }
    }

    public function outputJSON($response)
    {
        $this->view->disable();

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent($response);
        $this->response->send();
    }
}