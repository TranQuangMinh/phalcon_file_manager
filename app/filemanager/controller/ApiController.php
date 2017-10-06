<?php
namespace MINI\Filemanager\Controller;

use MINI\Data\Lib\Constant;

class ApiController extends BaseController
{
    private $folderCount = 0;
    private $fileCount = 0;
    private $sizeCount = 0;

    public function getFolderListAction()
    {
        $path = $this->config->application->upload_dir;
        $result = $this->getFolder($path);

        parent::outputJSON(array(
            'status'=> 200,
            'message' => 'Success',
            'result' => $result,
            'meta_count' => array(
                'folder_count' => number_format($this->folderCount, 0 , ',', '.'),
                'file_count' => number_format($this->fileCount, 0 , ',', '.'),
                'size_count' => number_format($this->sizeCount / 1000, 0 , ',', '.') . 'MB'
            )
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

                $type = filetype($this->config->application->upload_dir . $path .  $item);
                $size = $info->getSize() / 1000;

                $folderList[] = array(
                    'name' => $item,
                    'create' => $info->getMTime(),
                    'metadata' => array (
                        'type' => $type,
                        'ext' => strtolower($info->getExtension()),
                        'group' => \MINI\Data\Lib\Util::getTypeFile($info->getExtension()),
                        'size' => $size . 'KB',
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
            'meta_time' => $metaTime,
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

    public function uploadFileFromAnywhereAction()
    {
        $response = array();

        $response['status'] = \MINI\Data\Lib\Constant::STATUS_CODE_SUCCESS;
        $response['message'] = 'Success.';
        $warning = [];

        if ($this->request->isPost()) {
            $content    = $this->request->getPost('content');
            $from       = $this->request->getPost('from');
            $user_phone = $this->request->getPost('user_phone');
            
            if ($from == '') {
                $response['status'] = \MINI\Data\Lib\Constant::STATUS_CODE_ERROR;
                $response['message'] = 'Error.';
                $response['result'][] = array(
                    'from' => 'This param is require'
                );
                parent::outputJSON($response);
            }

            if ($user_phone == '') {
                $response['status'] = \MINI\Data\Lib\Constant::STATUS_CODE_ERROR;
                $response['message'] = 'Error.';
                $response['result'][] = array(
                    'user_phone' => 'This param is require'
                );
                parent::outputJSON($response);
            }

            $filename = $from . '-' . $user_phone . '-' . uniqid();
            $pathUpload = $this->config->application->upload_dir . $from . '/' . date('Y') . '/' . date('m') . '/' . date('d');

            $handle = new \MINI\Data\Lib\Upload('data:'.$content);
            $handle->file_overwrite = true;
            $handle->file_new_name_body = $filename;
            $handle->file_max_size = $this->config->limit->size;
            $handle->allowed = array('image/*');
            $handle->image_convert = 'jpg';
            $handle->jpeg_quality = $this->config->limit->jpeg_quality;

            if ($handle->image_src_x  > $this->config->limit->width ) {
                $warning['width'] = 'Chiều ngang lớn hơn qui định (1000px), sẽ bị điều chỉnh về 1000px. Origin: ' . $handle->image_src_x . 'px';

                $handle->image_resize = true;
                $handle->image_x = $this->config->limit->width;
                $handle->image_ratio_y = true;
            } 
            if ($handle->image_y  > $this->config->limit->height ) {
                $warning['height'] = 'Chiều cao lớn hơn qui định (1000px), sẽ bị điều chỉnh về 1000px. Origin: ' . $handle->image_src_y . 'px';

                $handle->image_resize = true;
                $handle->image_y = $this->config->limit->height;
                $handle->image_ratio_x = true;
            }
            if ($handle->file_src_size  > $this->config->limit->size ) {
                $response = array(
                    'status' => \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Lỗi, Dung lượng hình lớn hơn quy định (tối đa: ' .  number_format($this->config->limit->size / 1000 / 1000)  . 'MB).',
                    'resulf' => array(
                        'size' => $handle->file_src_size
                    ),
                    'warning' => $warning
                );
                parent::outputJSON($response);
            }

            try {
                if (!$handle->uploaded) {
                    $response = array(
                        'status' => \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                        'message' => 'Lỗi, không thể upload.',
                        'warning' => $warning
                    );
                    parent::outputJSON($response);

                } else {
                    $handle->process($pathUpload);

                    if ($handle->processed) {
                        $response = array(
                            'status' => \MINI\Data\Lib\Constant::STATUS_CODE_SUCCESS,
                            'message' => 'Upload thành công.',
                            'result' => array(
                                'file_name_body' => $handle->file_dst_name_body,
                                'file_name_ext' => $handle->file_dst_name_ext,
                                'file_name' => $handle->file_dst_name,
                                'file_url' => str_replace('\\', '/', str_replace($this->config->application->upload_dir, $this->config->application->upload_url, $handle->file_dst_pathname)),
                                'file_path' => str_replace('\\', '/', str_replace($this->config->application->upload_dir, '', $handle->file_dst_pathname)),
                                'image_dst_type' => $handle->image_dst_type,
                                'image_width' => $handle->image_dst_x,
                                'image_height' => $handle->image_dst_y,

                            ),
                            'warning' => $warning
                        );

                        $response['thumbnail'] = $this->createThumbnail($handle);


                    } else {
                        $response = array(
                            'status' => \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                            'message' => 'Lỗi, không thể xử lý hình ảnh.',
                            'warning' => $warning,
                            'result' => $handle->error
                        );
                    }

                    parent::outputJSON($response);
                }

            } catch (\Phalcon\Exception $e) {
                $response = array(
                    'status' => \MINI\Data\Lib\Constant::STATUS_CODE_ERROR,
                    'message' => 'Có lỗi xảy ra . ' . $e->getMessage(),
                    'warning' => $warning
                );
                parent::outputJSON($response);
            }  
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

                                        $response['thumbnail'] = $this->createThumbnail($u);

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
            if (!in_array($item, array(".", ".."))) {
                $size = filesize($path . '/' . $item) / 1000;
                $this->sizeCount = $this->sizeCount + $size;
                if (is_dir($path . '/' . $item)) {
                    $out[] = array(
                        'name' => $item,
                        'sub_dir' => $this->getFolder($path . '/' . $item)
                    );
                    $this->folderCount ++;
                } else {
                    $this->fileCount ++;
                }
            }
        }
        return $out;
    }

    private function createThumbnail(\MINI\Data\Lib\Upload $handle)
    {
        $output = array();

        $thumbDir = $this->config->application->thumbnail_dir;
        if( !is_dir($thumbDir) ) {
            mkdir($thumbDir);
        }

        $sizes = array(
            20, 150, 250, 500
        );

        $handle->file_overwrite = true;


        $pathUpload = str_replace($this->config->application->upload_dir, '', $handle->file_dst_path);
        foreach ($sizes as $width) {
            $_handle = $handle;
            if( !is_dir($thumbDir . $width . '/') ) {
                @mkdir($thumbDir . $width . '/');
            }

            $_handle->file_new_name_body = $handle->file_dst_name_body;
            $_handle->image_convert = 'jpg';
            $_handle->jpeg_quality = $this->config->limit->jpeg_quality;
            $_handle->image_resize = true;
            $_handle->image_x = $width;
            $_handle->image_ratio_y = true;
            $_handle->process($thumbDir . $width . '/' . $pathUpload);

            $waring = '';
            if($_handle->image_src_x > $width) {
                $waring = 'Resize.';
            } else {
                $waring = 'No Resize.';
            }

            if ($_handle->processed) {
                $output[$width ] = array(
                    'status' => Constant::STATUS_CODE_SUCCESS,
                    'url' => $this->config->application->thumbnail_url . $width . '/' . $pathUpload . $_handle->file_dst_name,
                    'warning' => $waring,
                );
            } else {
                $output[$width ] = array(
                    'status' => Constant::STATUS_CODE_ERROR,
                    'message' => 'Không tạo được thumbnail ' . $width
                );
            }
        }
        $output['pathUpload'] = $pathUpload;
        return $output;
    }
}
