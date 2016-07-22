{% extends 'default/main.volt' %}

{% block content %}
    <div class="text-center container">
        <br><br>
        <div class="col-xs-12">
            <div class="alert alert-danger">
                {{ message }}
            </div>
        </div>
    </div>
{% endblock %}