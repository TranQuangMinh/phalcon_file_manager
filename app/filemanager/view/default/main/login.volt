{% extends 'default/main.volt' %}

{% block content %}
    <div class="form-login">
        <h3 class="title">Xác thực người dùng</h3>
        <form action="{{ url({'for': 'login'}) }}" method="post" class="form-login-main">
            <div class="logo text-center">
                <img src="{{ config.application.base_url }}asset/img/file-manager.png" alt="">
            </div>
            {% if (mess is defined and mess != '') %}
                <p class="text-center text-danger"><i>{{ mess }}</i></p>
            {% endif %}
            <div class="form-group">
                <input name="username" type="text" class="form-control" placeholder="Nhập user">
            </div>
            <div class="form-group">
                <input name="pass" type="text" class="form-control" placeholder="Nhập mật khẩu">
            </div>
            <div class="text-right">
                <button type="submit" class="btn btn-success">Đăng nhập Quản lý</button>
            </div>
        </form>
    </div>
{% endblock %}