## 项目搭建

1.  git clone git@code.gammainfo.com:edu/exam.git
2.  composer install
3.  更改目录权限（storage,bootstrap/cache）
4.  cp .env.example .env
5.  php artisan key:generate
6.  npm install (由于众所周知的问题，建议用cnpm install)
7.  vue编译：开发=>npm run dev;生产=>npm run prod
8.  laravel调试：php artisan serv 或 apache/nginx搭建调试
9.  安装wkhtmltopdf https://wkhtmltopdf.org/downloads.html

## 部署事项

* 创建目录public/ueditor/php/upload,并修改权限为 777
* kityformula数据公式编辑器支持IE9+
* 开启任务计划: linux crontab中添加laravel task
* 需要安装php-mysqlnd模块,用于解决mysql驱动获取数据后类型丢失(全为字符串型,这种会导致vue模型失效)http://www.druidcoder.cn/2016/05/10/mysql-driver/
* 安装wkhtmltopdf https://wkhtmltopdf.org/downloads.html