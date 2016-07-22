<?php
namespace MINI\Filemanager\Controller;

class ApiController extends BaseController
{
    public function getFolderListAction()
    {
        $path = $this->config->application->upload_dir;
        $result = $this->getFolder($path);

        parent::outputJSON(array(
            'status'=> 200,
            'message' => 'Success',
            'result' => $result
        ));
    }

    public function getFolderEntryAction()
    {
        $folderList = array();
        $metaTime = array();
        $path = $this->request->getQuery('path');
        if ($path != '') {
            $path .= '/';
        }
        $cdir = scandir( $this->config->application->upload_dir . $path);

        $this->session->set('PATH_CURRENT', $this->config->application->upload_dir . $path);

        foreach ($cdir as $item) {
            if (!in_array($item, array(".",".."))) {
                $info = new \SplFileInfo($this->config->application->upload_dir . $path . $item);
                if (!in_array($info->getMTime(), $metaTime)) {
                    $metaTime[] = $info->getMTime();
                }
                $folderList[] = array(
                    'name' => $item,
                    'create' => $info->getMTime(),
                    'metadata' => array (
                        'type' => filetype($this->config->application->upload_dir . $path .  $item),
                        'ext' => strtolower($info->getExtension()),
                        'group' => \MINI\Data\Lib\Util::getTypeFile($info->getExtension()),
                        'size' => number_format($info->getSize() / 1000, 0) . 'KB',
                        'link' => $info->isFile() ? $this->config->application->base_url . 'uploads/' . $path . $item : $path . $item,
                        'path' => $path . $item
                    )
                );
            }
        }

        rsort($metaTime, SORT_NUMERIC);

        parent::outputJSON(array(
            'status'=> 200,
            'message' => 'Success: ' . $this->session->get('PATH_CURRENT'),
            'result' => $folderList,
            'meta_time' => $metaTime
        ));
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $meta = $this->request->getPost('meta');

            if (!isset($meta['type']) || !isset($meta['link'])) {
                parent::outputJSON(array(
                    'status'=> \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Dữ liệu xóa không đúng. '
                ));
            }

            if ($meta['type'] == 'file') {
                $filename = str_replace($this->config->application->base_url . 'uploads/' , '' , $meta['link']);
                unlink($this->config->application->upload_dir . $filename);
            } else if ($meta['type'] == 'dir') {
                $filename = $this->config->application->upload_dir . $meta['link'];
                \MINI\Data\Lib\Util::recursiveRemove($filename);
            }

            parent::outputJSON(array(
                'status'=> \MINI\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                'message' => 'Xoá thành công.'
            ));
        } else {
            parent::outputJSON(array(
                'status'=> \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Phương thức lỗi'
            ));
        }
    }

    public function renameAction()
    {
        $old = $this->request->getPost('old');
        $new = $this->request->getPost('new');
        rename($old, $new);
        parent::outputJSON(array(
            'status'=> 200,
            'message' => 'Success'
        ));
    }

    public function addDirAction()
    {
        if ($this->request->isPost()) {
            $dirName = $this->request->getPost('dir-name');
            $dirName = \MINI\Data\Lib\Util::slug($dirName);
            if (is_dir($this->session->get('PATH_CURRENT') . $dirName)) {
                parent::outputJSON(array(
                    'status' => \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Folder is existed'
                ));
            }

            if (is_writable($this->session->get('PATH_CURRENT') . $dirName)) {
                parent::outputJSON(array(
                    'status' => \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Permission denied'
                ));
            }

            $checkMk = @mkdir($this->session->get('PATH_CURRENT') . $dirName);
            if ($checkMk) {
                parent::outputJSON(array(
                    'status' => \MINI\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                    'message' => 'Success ',
                    'result' => array(
                        'path' => str_replace($this->config->application->upload_dir, '', $this->session->get('PATH_CURRENT'))
                    )
                ));
            } else {
                parent::outputJSON(array(
                    'status' => \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Can\'t create dir: ' . $checkMk
                ));
            }
        } else {
            parent::outputJSON(array(
                'status' => \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                'message' => 'Method post'
            ));
        }
    }

    public function uploadFileAction()
    {
        if ($this->request->isAjax()) {
            if ($this->request->hasFiles()) {
                $files = $this->request->getUploadedFiles();
                if (count($files) > 0) {
                    foreach ($files as $file) {
                        if (isset($file) && $file->getName() != '') {
                            if ($file->getError()) {
                                $response = array(
                                    'status' => \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                                    'message' => 'Có lỗi từ file tải lên'
                                );
                                parent::outputJSON($response);
                            }

                            $resource = array(
                                'name' => $this->request->getPost('file-name'),
                                'type' => $file->getType(),
                                'tmp_name' => $file->getTempName(),
                                'error' => $file->getError(),
                                'size' => $file->getSize(),
                                'extension' => $file->getExtension()
                            );

                            $u = new \MINI\Data\Lib\Upload($resource);
                            $u->file_overwrite = true;
                            try {
                                if (!$u->uploaded) {
                                    $response = array(
                                        'status' => \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                                        'message' => 'Lỗi, không thể upload.'
                                    );
                                } else {
                                    $u->process($this->session->get('PATH_CURRENT'));

                                    if ($u->processed) {
                                        $file_name = $u->file_src_name;
                                        $response = array(
                                            'status' => \MINI\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                                            'message' => 'Upload thành công.',
                                            'result' => $file_name
                                        );
                                    } else {
                                        $response = array(
                                            'status' => \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                                            'message' => 'Lỗi, không thể xử lý hình ảnh.'
                                        );
                                    }

                                    parent::outputJSON($response);
                                }
                            } catch (\Phalcon\Exception $e) {
                                $response = array(
                                    'status' => \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                                    'message' => 'Có lỗi xảy ra . ' . $e->getMessage()
                                );
                                parent::outputJSON($response);
                            }
                        }
                    }
                }
            }
        }
    }

    private function getFolder($path)
    {
        $out = array();
        $cdir = scandir($path);

        foreach ($cdir as $item) {
            if (is_dir($path . '/' . $item) && !in_array($item, array(".","..")) ) {
                $out[] = array(
                    'name' => $item,
                    'sub_dir' => $this->getFolder($path . '/' . $item)
                );
            }
        }
        return $out;
    }
}
