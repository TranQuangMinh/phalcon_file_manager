a:3:{i:0;s:1882:"<!DOCTYPE html>
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
    ";s:7:"content";N;i:1;s:17:"
</body>
</html>
";}