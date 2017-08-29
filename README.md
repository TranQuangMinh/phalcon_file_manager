# phalcon_file_manager

**Requirements**
- php 5.5 +
- Phalcon frameword
- Nginx or Apache

**Config**
- Root document : web/filemanager
- Rename app/filemanager/parameter_exxample.php to app/filemanager/parameter.php
- in app/filemanager/parameter.php
  - application[token_user] is md5(username)
  - application[token] is md5(password)
  - Login with username and password
  
**Share session across domains**
  - Insert before ```session_start();``` Admin app and Filemanager app 

```php 
  session_name('any_name');
  ini_set('session.cookie_domain', '.domain.xyz');
```

**How to use**
- form.php
```html
<button class="upload">Upload</button>
```
- app-form.js
```javascript
$('.upload').on('click', function (event) {
    event.preventDefault();
    $.fancybox({
        type: 'iframe',
        href: filemanager_url + '?callback=callbackAfterSelect&input-receive=inputReceiveData',
        'width': '90%',
        'height': '90%',
        'autoScale': true,
        'transitionIn': 'fade',
        'transitionOut': 'fade'
    });
});

window.addEventListener('message', function(event) {
    if(event.data.meta.callback == 'callbackAfterSelect') {
        callbackAfterSelect(event.data.data);
    }

}, false);

function callbackAfterSelect(result) {
    console.log(result);
})
```
**Insert to Tinymce editor**
- copy web/filemanager/asset/helper_plugins/tinymce/plugins/image to directory plguin tinymce your project.
- Enable plguin image in config tinymce.
- Done.


**:)) Good luck**

![Phalcon file manager](https://github.com/TranQuangMinh/phalcon_file_manager/blob/master/login.png?raw=true "Phalcon file manager")

![Phalcon file manager](https://github.com/TranQuangMinh/phalcon_file_manager/blob/master/index.png?raw=true "Phalcon file manager")
