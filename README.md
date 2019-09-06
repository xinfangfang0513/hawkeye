###鹰眼异常监控sdk

捕获laravel中抛出的异常并捕获它，及时通知开发者，增加项目的稳定性

使用流程

* 在鹰眼后台添加自己要监控的项目获取配置access_key和access_secret

* 安装：composer require frambo/hawkeye 

* 替换：app/Exceptions/Handler.php 文件下：use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler; 为 use frambo\hawkeye\HawkeyeExceptionHandler as ExceptionHandler;

* 在config目录下添加hawkeye.php 文件，配置access_key，access_secret，monitoring_name三个参数
