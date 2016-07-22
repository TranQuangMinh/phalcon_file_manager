{% extends 'default/main.volt' %}

{% block content %}
    <style>
        body {
            position: relative;
        }
    </style>
    <div class="detail-img">
        <div class="col-left">
            {% if width and height %}
                <img src="{{ src }}" alt="">
            {% else %}
                <br>
                <div class="col-xs-12">
                    <div class="alert alert-info">Không có xem trước cho định dạng file này.</div>
                    Bạn có thể tải file <a href="{{ src }}"><b>tại đây</b></a>
                </div>
                <div class="clearfix"></div>
            {% endif %}
        </div>
        <div class="col-right">
            <p>
                <b id="filename_origin">{{ file_name }}</b>
            </p>
            <p>
                {% if width and height %}
                    Size: <b>{{ width }} x {{ height }}</b> <br>
                {% endif %}
                DLượng: <b>{{ size }}</b>KB<br>
                Kiểu: <b>{{ type }}</b>
                <a href="{{ src }}">Link tải về</a>
            </p>
            <a href="" class="btn btn-block btn-danger delete-file-single">Xóa file</a>
            <a href="" class="btn btn-block btn-warning btn-change-file">Đổi file</a>
        </div>
        <div class="clearfix"></div>
    </div>
    <input type="file" class="hidden" name="files" id="files-upload-change">
    <script>
        $(document).ready(function (event) {
            $('.delete-file-single').on('click', function (event) {
                event.preventDefault();
                var data = {
                    'meta' : {
                        'type': 'file',
                        'link': '{{ src }}'
                    }
                };

                $.post(router.delete_url, data, function (res) {
                    if (res.status == 200) {
                        window.parent.deleteFileFromIframe();
                    } else {
                        $('.detail-image').css({'padding': '15px'}).html('<div class="alert alert-success">'+ data.message +'</div>');
                    }
                });
            });

            $('.btn-change-file').on('click', function (event) {
                event.preventDefault();
                $('#files-upload-change').trigger('click');
            });

            $('#files-upload-change').on('change', function (event) {
                event.preventDefault();

                var file_datas = $(this).prop('files'),
                        $this = $(this);
                totalFile = file_datas.length;
                $.each(file_datas, function(index, file_data){
                    var form_data = new FormData();
                    ImageTools.resize(file_data, {
                        width: 1200,
                        height: 1200
                    }, function(blob, didItResize) {
                        form_data.append('file', blob);
                        form_data.append('file-name', $.trim($('#filename_origin').text()));
                        $.ajax({
                            url: router.upload_url,
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function(data){
                                totalFile --;
                                if (totalFile == 0) {
                                    window.location.reload();
                                }
                            }
                        });
                    });
                });
            })
        })
    </script>
{% endblock %}