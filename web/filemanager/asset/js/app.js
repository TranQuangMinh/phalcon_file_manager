var folder_list = {};
var entry_curent = {};
var current_path = '';
var selectedFile = [];
var ajaxUpload = false;
var totalFile = 0;

$(document).ready(function(){

    $wrap_list_folder = $('.list-dir .wrap-list');

    getListFolder($);
    updateListFileByPath('', $);
})
    .on('click', '.arrow-list-dir', function (event) {
        event.preventDefault();
        $(this).closest('a').toggleClass('active').next('.sub-list').stop(true, true).slideToggle('fast');
        return false;
    })
    .on('click', '.open-sub', function (event) {
        event.preventDefault();
        var path = $(this).attr('data-path');
        updateListFileByPath(path, $);
    })
    .on('dblclick', '.list-file .file', function (event) {
        event.preventDefault();
        var data = $(this).data();
        $(this).addClass('show-in-iframe');
        $this = $(this);
        data = data.meta;
        $.fancybox({
            href: router.detail_url + '?filename=' + data.path,
            type: 'iframe',
            autoCenter: true,
            autoWidth: true,
            autoHeight: true,
            afterClose: function (event) {
                $this.removeClass('show-in-iframe');
            }
        });
    })
    .on('dblclick', '.list-file .dir', function (event) {
        event.preventDefault();
        var data = $(this).data();
        var path = data.meta.link;
        updateListFileByPath(path, $);
    })
    .on('click', '.btn-bluk-delete', function (event) {
        event.preventDefault();
        $this = $(this);
        if (!confirm('Bạn có chắc là sẽ xóa tất cả thư mục/ file này không?')) {
            $this.closest('.item').removeClass('loading');
            return false;
        }

        $('.list-file .selected').addClass('loading');
        $('.list-file .selected').each(function(index, el){
            var data = $(el).data();
            $.post(router.delete_url, data, function (data) {
                if (data.status == 200) {
                    $(el).fadeOut('fast', function () {
                        $(this).remove();
                    });
                } else {
                    alert(data.message);
                }
            }).always(function(){
                $(el).removeClass('loading');
            });
        });

        while (selectedFile.length > 0) {
            selectedFile.pop();
        }

        $('.footer-tool button').addClass('disabled').find('.selected-count').text('');

        setTimeout(function () {
            getListFolder($, function () {
                $(document).find('[data-path="'+ current_path +'"]').addClass('active opening').next('.sub-list').stop(true, true).slideDown('fast');
                $(document).find('[data-path="'+ current_path +'"]').parents('li').children('a').addClass('active').next('.sub-list').stop(true, true).slideDown('fast');
                $('body').find('.opening').trigger('click');
            });
        }, 200);
    })
    .on('click', '.list-file .delete-action', function (event) {
        $this = $(this);
        $this.closest('.item').addClass('loading');
        if (!confirm('Bạn có chắc là sẽ xóa thư mục/ file này không?')) {
            $this.closest('.item').removeClass('loading');
            return false;
        }

        var data = $this.closest('.item').data();
        $.post(router.delete_url, data, function (res) {
            if (res.status == 200) {
                setTimeout(function () {
                    getListFolder($, function () {
                        $(document).find('[data-path="'+ current_path +'"]').addClass('active opening').next('.sub-list').stop(true, true).slideDown('fast');
                        $(document).find('[data-path="'+ current_path +'"]').parents('li').children('a').addClass('active').next('.sub-list').stop(true, true).slideDown('fast');
                        $('body').find('.opening').trigger('click');
                    });
                }, 200);

                while (selectedFile.length > 0) {
                    selectedFile.pop();
                }
                $('.footer-tool button').addClass('disabled').find('.selected-count').text('');

            } else {
                alert(res.message);
            }
        }).always(function(){
            $this.closest('.item').removeClass('loading');
        });

        return false;
    })
    .on('click', '#send-to-parent', function(event){
        event.preventDefault();
        var data = $(this).data();
        var dataCallBack = {
            'data' : selectedFile,
            'meta': data
        };

        window.parent.postMessage(dataCallBack, config.allowParent);

        // if (typeof window.parent[data.callback] === 'function') {
        //     window.parent[data.callback](dataCallBack);
        // }
    })
    .on('click', '#cancel-selected', function(event){
        event.preventDefault();
        $('.list-file').find('.selected').removeClass('selected');
        while (selectedFile.length > 0) {
            selectedFile.pop();
        }
        $('.footer-tool button').addClass('disabled').find('.selected-count').text('');
    })
    .on('click', '.list-file .item', function (event) {
        event.preventDefault();
        $this = $(this);
        var data = $this.data();

        if (event.ctrlKey) {
            selectedFile.push(data.meta);
        } else {
            $('.list-file .item').removeClass('selected');
            while (selectedFile.length > 0) {
                selectedFile.pop();
            }
            selectedFile.push(data.meta);
        }
        $this.addClass('selected');

        $('.footer-tool button').removeClass('disabled').find('.selected-count').text('('+ selectedFile.length +')');
    })
    .on('click', '.btn-rollback', function (event) {
        event.preventDefault();
        $('body').find('.opening').closest('ul').closest('li').children('a').trigger('click');
        while (selectedFile.length > 0) {
            selectedFile.pop();
        }
        $('.footer-tool button').addClass('disabled').find('.selected-count').text('');
    })
    .on('click', '.btn-reload', function (event) {
        event.preventDefault();
        $('body').find('.opening').trigger('click');
        while (selectedFile.length > 0) {
            selectedFile.pop();
        }
        $('.footer-tool button').addClass('disabled').find('.selected-count').text('');
    })
    .on('click', '.add-new-dir', function (event) {
        event.preventDefault();
        $.fancybox({
            content: $('#add-dir'),
            autoCenter: true,
            autoWidth: true
        });
        $('#add-dir').find('input').focus();
    })
    .on('submit', '#add-dir', function (event) {
        event.preventDefault();
        $this = $(this);
        var data = $(this).serialize();
        $.post(router.add_dir, data, function (data) {
            $this.find('.info').remove();
            if (data.status == 200) {
                $this.append('<div class="success info"><i>Thêm thư mục thành công</i></div>');
                getListFolder($, function () {
                    $(document).find('[data-path="'+ current_path +'"]').addClass('active opening').next('.sub-list').stop(true, true).slideDown('fast');
                    $(document).find('[data-path="'+ current_path +'"]').parents('li').children('a').addClass('active').next('.sub-list').stop(true, true).slideDown('fast');
                    $('body').find('.opening').trigger('click');
                });

                $this.trigger('reset');
            } else {
                $this.append('<div class="danger info"><i>'+ data.message +'</i></div>');
            }
        })
    })
    .on('click', '.btn-bluk-upload', function (event) {
        event.preventDefault();
        $('#files-upload').trigger('click');
    })
    .on('change', '#files-upload', function (event) {
        var file_datas = $(this).prop('files'),
        $this = $(this);
        totalFile = file_datas.length;
        $('.top-tool').addClass('loading');
        $.each(file_datas, function(index, file_data){
            var form_data = new FormData();
            ImageTools.resize(file_data, {
                width: 1200,
                height: 1200
            }, function(blob, didItResize) {
                form_data.append('file', blob);
                form_data.append('file-name', file_data['name']);
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
                            uploadBlukFileFinish();
                        }
                    },
                    xhr: function(){
                        var xhr = $.ajaxSettings.xhr() ;
                        xhr.upload.onprogress = function(evt){
                            var percent =  evt.loaded/evt.total * 100;
                        };
                        return xhr ;
                    }
                });
            });
        });
    })
    .on('keyup', '[name="search"]', function (event) {
        $this = $(this);
        var searchField = $this.val();
        $('.list-file').html('');
        if (searchField != '') {
            var regex = new RegExp(searchField, "i");
            var count = 1;
            var limit = 0;
            var output = '';
            $.each(entry_curent, function(key, val) {
                if ( (val.metadata.path.search(regex) != -1) && count < 20 ) {
                    $('.list-file').append(buildItemFile(val));
                }
            });
        } else {
            $.each(entry_curent, function(key, val) {
                $('.list-file').append(buildItemFile(val));
            });
        }
    })
;

function uploadBlukFileFinish(){
    setTimeout(function () {
        $('body').find('.opening').trigger('click');
        $('.top-tool').addClass('finish');
        setTimeout(function(){
            $('.top-tool').removeClass('finish loading');
        }, 250);
    }, 500);
}

function getListFolder($, callback) {
    $.getJSON(router.list_folder, function (data) {
        $folder_list = data.result;
        $wrap_list_folder.find('ul').html('<li class="has-child">' +
            '<a class="open-sub" data-path=""><span class="arrow-list-dir"></span>Thư mục gốc</a>' +
            '<ul class="sub-list" style="display: block">' + buildTreeView($folder_list, '') + '</ul>' +
            '</li>');

        $('.total-dir').text(data.meta_count.folder_count);
        $('.total-file').text(data.meta_count.file_count);
        $('.total-size').text(data.meta_count.size_count);

    }).always(function(){
        if (typeof callback == 'function'){
            callback();
        }
    });

}

function buildTreeView(data, path) {
    var html = '';

    $.each(data, function (index, value) {
        if (value) {
            if (value['sub_dir'].length > 0) {
                html += '<li class="has-child"><a class="open-sub" data-path="'+ path + value.name +'"><span class="arrow-list-dir"></span> '+ value.name +'</a>';
                html += '<ul class="sub-list">';
                html += buildTreeView(value['sub_dir'], path + value.name + '/');
                html += '</ul></li>';
            } else {
                html += '<li><a class="open-sub" data-path="'+ path + value.name +'"><span class="arrow-list-dir"></span>'+ value.name +'</a></li>';
            }
        }
    });

    return html;
}

function buildItemFile(data) {
    var html = '';
    var img = '';
    if (data.metadata.type == 'dir') {
        img = config.asset_url + 'img/type-file/dir.png'
    } else if (data.metadata.group == 'image') {
        img = data.metadata.link;
    } else {
        img = config.asset_url + 'img/type-file/'+ data.metadata.group +'.png'
    }
    html += '<div class="item '+ data.metadata.type +'" data-meta=\''+ JSON.stringify(data.metadata) +'\'>' +
        '<div class="wrap-thumb">' +
        '<img src="'+ img +'" alt="">' +
        '<div class="tool">' +
        '<a href="" class="change-active"></a>' +
        '<a href="" class="delete-action"></a>' +
        '</div>' +
        '<div class="file-name">'+ data.name +'</div>' +
        '</div>';

    return html;
}

function updateListFileByPath(path, $) {
    $('.list-file').addClass('loading');
    $.getJSON(router.entry_folder + '?path=' + path, function(data){
        if (data.status == 200) {
            entry_curent = data.result;
        }

        if (data.result.length > 0) {
            $('.list-file').html('');
            $.each(data.meta_time, function (index, create_time) {
                $.each(data.result, function (index, value) {
                    if (create_time == value.create) {
                        $('.list-file').append(buildItemFile(value));
                    }
                });
            });
        } else {
            $('.list-file').html('<div class="alert alert-danger">Thư mục này chưa có file.</div>');
        }

        $('.open-sub').removeClass('opening');
        $(document).find('[data-path="'+ path +'"]').addClass('active opening').next('.sub-list').stop(true, true).slideDown('fast');
        current_path = path;

        while (selectedFile.length > 0) {
            selectedFile.pop();
        }
        $('.footer-tool button').addClass('disabled').find('.selected-count').text('');

    }).always(function () {
        setTimeout(function () {
            $('.list-file').removeClass('loading');
        }, 500);
    })
}

function deleteFileFromIframe(data) {
    $('.list-file').find('.show-in-iframe').fadeOut('fast', function () {
        $(this).remove();
    });

    $.fancybox.close();
}


