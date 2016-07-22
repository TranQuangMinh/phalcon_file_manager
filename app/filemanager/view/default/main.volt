<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <title>File manager</title>
    <link rel="stylesheet" href="{{ config.application.base_url }}/asset/css/bootstrap.css">
    <link rel="stylesheet" href="{{ config.application.base_url }}/asset/fancybox/jquery.fancybox.css">
    <link rel="stylesheet" href="{{ config.application.base_url }}/asset/css/style.css">

    <script>
        var router = {
            'list_folder': '{{ url({'for': 'get_folder_list'}) }}',
            'entry_folder': '{{ url({'for': 'get_folder_entry'}) }}',
            'add_dir': '{{ url({'for': 'add_dir'}) }}',
            'upload_url': '{{ url({'for': 'upload_file'}) }}',
            'delete_url': '{{ url({'for': 'delete'}) }}',
            'detail_url': '{{ url({'for': 'detail_file'}) }}'
        };

        var config = {
            'upload_url' : '{{ config.application.upload_url }}',
            'asset_url' : '{{ config.application.base_url }}asset/',
            'allowParent' : '{{ config.application.allow_parent }}'
        }
    </script>

    <script src="{{ config.application.base_url }}/asset/js/jquery.js"></script>
    <script src="{{ config.application.base_url }}/asset/js/bootstrap.min.js"></script>
    <script src="{{ config.application.base_url }}/asset/fancybox/jquery.fancybox.pack.js"></script>
    <script src="{{ config.application.base_url }}/asset/js/ImageTools.js"></script>
</head>
<body>
    {% block content %}{% endblock %}
</body>
</html>
