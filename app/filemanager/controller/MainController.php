<?php

namespace MINI\Filemanager\Controller;

class MainController extends BaseController
{
    public function indexAction()
    {
        $this->view->setVars(array(

        ));
        $this->view->pick('default/main/index');
    }

    public function detailFileAction()
    {
        $filename = $this->request->getQuery('filename', array('trim'), '');
        if (!$filename) {
            throw new \Phalcon\Exception('File_name là bắt buộc');
        }

        $path = $this->config->application->upload_dir . $filename;

        if (!is_file($path)) {
            throw new \Phalcon\Exception('Không tồn tại file, hoặc đây là 1 thư mục');
        }

        list($width, $height) = getimagesize($path);

        $this->view->setVars(array(
            'src' => $this->config->application->upload_url . $filename,
            'size' => number_format(filesize($path) /  1000 . 'MB'),
            'width' => $width,
            'height' => $height,
            'type' => pathinfo($path, PATHINFO_EXTENSION ),
            'file_name' => pathinfo($path, PATHINFO_BASENAME )
        ));
        $this->view->pick('default/main/detail');
    }
}