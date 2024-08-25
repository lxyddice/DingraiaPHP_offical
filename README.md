# DingraiaPHP_offical

# 这是什么~？

[DingraiaPHP](https://github.com/lxyddice/DingraiaPHP)官方插件库，欢迎研究or使用~

查阅 https://doc.lxyddice.top/dingraiaphp/dingraiaphp/guan-fang-cha-jian 使用文档~

如果喜欢，请给我个star，谢谢喵！

# 使用帮助

一般插件：plugin  放入框架 plugin 文件夹内

外置模块插件：module  放入框架 module/DinrgaiaPHP/plugin 文件夹内

## 茉莉云机器人 com.lxyddice.moliyun.php

安装后运行一次框架，在data/moliyun/key.json配置Appkey和AppSecret

## GithubWebhook com.lxyddice.githubWebhook.php

把 module/githubWebhook.php 放入外置模块插件文件夹

把 plugin/com.lxyddice.githubWebhook.php 放入一般插件文件夹

运行一次框架

打开 data/com.lxyddice.githubWebhook/config.json 配置

sendWebhook 即为转发到的钉钉机器人的webhook，secret即为Github设置的webhook secret