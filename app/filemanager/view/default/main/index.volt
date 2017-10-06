{% extends 'default/main.volt' %}

{% block content %}
    <script>var requestGet = {{ request.getQuery()|json_encode }}</script>
    <script src="{{ config.application.base_url }}/asset/js/app.js"></script>
    <div class="wrap">
        <div class="list-dir">

            <div class="alert alert-info text-center" style="padding: 5px">
                <b class="total-size"></b> / <b class="total-dir"></b> folder - <b class="total-file"></b> file</div>
            <div class="wrap-list">
                <ul></ul>
            </div>
            <hr>
            <div>
                <a class="logout" href="{{ url({'for': 'logout'}) }}">Đăng xuất</a>
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
                        data-height="{{ request.getQuery('height') ? request.getQuery('height') : '' }}"
                        data-width="{{ request.getQuery('width') ? request.getQuery('width') : '' }}"
                        data-callback="{{ request.getQuery('callback') ? request.getQuery('callback') : 'getFileFromFileManager' }}"
                        data-inputReceive="{{ request.getQuery('input-receive') ? request.getQuery('input-receive') : 'false' }}"
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

{% endblock %}
