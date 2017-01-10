# Angular-ueditor for ThinkPHP

## 版本

- ThinkPHP： 3.2.3
- Angularjs：v1.4.6
- ueditor：

```
/*!
 * UEditor
 * version: ueditor
 * build: Wed Aug 10 2016 11:06:02 GMT+0800 (CST)
 */
```

## 功能

- 效果图

![效果示例](https://dn-shimo-image.qbox.me/mBjHrCg5UBkAH01Q/image.png "效果示例")


## 使用方法

1. 新建项目文件夹
2. 复制 `ThinkPHP` 框架代码到项目文件夹中
3. 复制本例代码到项目文件夹中
4. 添加 `index.php` 入口文件
5. ` Index/index ` 即demo页面，访问 `/Index/index` 即可
6. 在 `/Index/index` 中，设置了编辑器的配置项，并未显示所有的按钮和功能
7. 上传的文件保存在 ` /Upload ` 目录下，以当前日期为子目录，文件名为uniqid函数随机值。如需修改，可改 ` /Application/Api/UeditorController.class.php ` 文件中的配置项

```
<?php
	private $UPLOAD_CONFIG = array(
		'rootPath'   =>    './Upload/',
		'autoSub'    =>    true,
		'subName'    =>    array('date','Ymd'),
	);
```


## 文件列表

- ` /Application/Api/UeditorController.class.php ` 后端处理主文件，用于处理文件、图片上传等
- ` /Application/Api/Conf/ueditor.json ` ueditor后端配置文件，这个文件是从ueditor的DEMO中拿的，没有修改
- ` /Public/lib/angular.min.js ` angularjs文件
- ` /Public/lib/ueditor/ ` angular-ueditor文件

## 文件来源

- ` /Application/Api/Conf/ueditor.json `： http://ueditor.baidu.com/website/download.html
- ` /Public/lib/angular.min.js `：http://apps.bdimg.com/libs/angular.js/1.4.6/angular.min.js
- ` /Public/lib/ueditor/ `：http://zqjimlove.github.io/angular-ueditor/

### 引用文件修改

` /Public/lib/ueditor/ueditor.all.js `文件有修改，解决了上传单张图片后直接保存时，angularjs中内容不更新的问题

## 未完成功能

- 上传涂鸦图片（base64 format）
- 抓取远程文件功能