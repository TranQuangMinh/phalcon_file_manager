<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <title>File manager</title>
    <link rel="stylesheet" href="<?= $this->config->application->base_url ?>asset/css/bootstrap.css">
    <link rel="stylesheet" href="<?= $this->config->application->base_url ?>asset/fancybox/jquery.fancybox.css">
    <link rel="stylesheet" href="<?= $this->config->application->base_url ?>asset/css/style.css">

    <script>
        var router = {
            'list_folder': '<?= $this->url->get(['for' => 'get_folder_list']) ?>',
            'entry_folder': '<?= $this->url->get(['for' => 'get_folder_entry']) ?>',
            'add_dir': '<?= $this->url->get(['for' => 'add_dir']) ?>',
            'upload_url': '<?= $this->url->get(['for' => 'upload_file']) ?>',
            'delete_url': '<?= $this->url->get(['for' => 'delete']) ?>',
            'detail_url': '<?= $this->url->get(['for' => 'detail_file']) ?>'
        };

        var config = {
            'upload_url' : '<?= $this->config->application->upload_url ?>',
            'asset_url' : '<?= $this->config->application->base_url ?>asset/',
            'allowParent' : '<?= $this->config->application->allow_parent ?>'
        };
        var current_path = '<?= $this->session->get('RELATIVE_PATH_CURRENT') ?>';
    </script>

    <script src="<?= $this->config->application->base_url ?>asset/js/jquery.js"></script>
    <script src="<?= $this->config->application->base_url ?>asset/js/bootstrap.min.js"></script>
    <script src="<?= $this->config->application->base_url ?>asset/fancybox/jquery.fancybox.pack.js"></script>
    <script src="<?= $this->config->application->base_url ?>asset/js/ImageTools.js"></script>
</head>
<body>
    
    <script>var requestGet = <?= json_encode($this->request->getQuery()) ?></script>
    <script src="<?= $this->config->application->base_url ?>/asset/js/app.js"></script>
    <div class="wrap">
        <div class="list-dir">

            <div class="alert alert-info text-center" style="padding: 5px">
                <b class="total-size"></b> / <b class="total-dir"></b> folder - <b class="total-file"></b> file</div>
            <div class="wrap-list">
                <ul></ul>
            </div>
            <hr>
            <div>
                <a class="logout" href="<?= $this->url->get(['for' => 'logout']) ?>">Đăng xuất</a>
            </div>
        </div>
        <div class="main-content">

            <div class="top-tool text-right">
                <form action="" id="search-result" class="pull-left">
                    <input type="text" name="search" class="form-control" id="search" placeholder="Tìm tên file, thư mục">
                </form>
                <input type="file" class="hidden" name="files" id="files-upload" multiple>
                <a href="" class="btn btn-sm btn-warning btn-rollback">Trở lại</a>
                <a href="" class="btn btn-sm btn-default add-new-dir">Thêm thư mục</a>
                <a href="" class="btn btn-sm btn-primary btn-reload">Làm mới</a>
                <a href="" class="btn btn-sm btn-success btn-bluk-upload">Tải file</a>
                <a href="" class="btn btn-sm btn-danger btn-bluk-delete">Xóa</a>
            </div>

            <div class="list-file">
            </div>

            <div class="footer-tool text-right">
                <button
                        data-height="<?= ($this->request->getQuery('height') ? $this->request->getQuery('height') : '') ?>"
                        data-width="<?= ($this->request->getQuery('width') ? $this->request->getQuery('width') : '') ?>"
                        data-callback="<?= ($this->request->getQuery('callback') ? $this->request->getQuery('callback') : 'getFileFromFileManager') ?>"
                        data-inputReceive="<?= ($this->request->getQuery('input-receive') ? $this->request->getQuery('input-receive') : 'false') ?>"
                        class="btn btn-success disabled" id="send-to-parent">

                        Sử dụng <span class="selected-count"></span>
                </button>
                <button class="btn btn-default disabled" id="cancel-selected">Hủy chọn <span class="selected-count"></span></button>
            </div>

        </div>
    </div>

    <div style="display: none">
        <form action="" id="add-dir" style="width: 320px;">
            <h4>Thêm thư mục mới</h4>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Nhập tên thư mục..." name="dir-name" required>
                <span class="input-group-btn">
                    <button class="btn btn-success" type="submit">Thêm</button>
                </span>
            </div><!-- /input-group -->
        </form>
    </div>


</body>
</html>
