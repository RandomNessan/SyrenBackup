# SyrenBackup Wizard

A simple database backup site for easy management.

这是一个简单易用的由php编写的数据库备份管理系统，数据库采用mysql。

# 部署方式

假设你需要备份的网站是aaa.com，那么你需要一台新的vps并创建一个新的网站，或许它的域名可以是bbb.com。

你需要使用aapanel来搭建这个网站，当然，不使用也是可以的。

你的服务器需要安装以下环境:

`````
Nginx 1.24
PHP 7.4+
Mysql 5.7+
`````

创建网站bbb.com并创建数据库。进入文件根目录删除没用的初始配置。记得做好域名解析以及配置TLS并且强制开启https。

#### 接下来，你需要在文件根目录进行以下操作:

克隆这个项目

```
git clone https://github.com/RandomNessan/SyrenBackup.git
```

将init.sql.gz导入你创建的数据库。

给init.php文件赋予写入权限，即"666"权限。
给uploads文件夹赋予被写入和被执行权限，即"777"权限。

导入完成之后访问bbb.com根据提示对数据库进行初始化配置。

登录到管理员界面，进入"上传配置"，复制"自动部署备份脚本"，将指令粘贴到aaa.com所在的vps并执行。根据提示填入信息:

```
请输入以下信息：
数据库名: asdasd
数据库用户名: 123123
数据库密码: 【不可见】
远程备份站地址 (例: https://bak.123.com): https://aaa.com
希望保留的备份数量（例如30）: 15
✅ 成功生成脚本：/www/server/panel/script/db_auto_backup_push.sh
```

完成之后执行以下命令进行测试:

```
bash /www/server/panel/script/db_auto_backup_push.sh
```

执行成功后，会在bbb.com/dashboard.php页面下看到备份的数据库文件。

回到aaa.com所在的aapanel，在cron中添加定时任务，详细配置参考cron.md文件。
