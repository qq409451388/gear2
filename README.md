Gear框架2代！(beta 1)<br/>
开发环境：<br/>
系统：macos<br/>
php：7.3<br/>
swoole：4.0+(非必须)<br/><br/>
启动方式：<br/>
1、无swoole环境启动，可使用gear2/gear/main.php  启动<br/>
2、有swoole环境启动，可使用gear2/gear/main.php2 启动<br/>
3、支持生成单文件启动：<br/>
    @1、执行/gear2/generater.php文件<br/>
    @2、生成文件gear2.phar、gear2new.phar 分别对应无swoole与有swoole环境的启动<br/>
    @3、在任何位置使用easy.phar启动服务，同样支持两个参数<br/>
启动实例：<br/>
php main.php [ip] [port] 或者 php gear.phar<br/>
如：php main.php 127.0.0.1 5000 或者 php gear.phar 127.0.0.1 5000</br>