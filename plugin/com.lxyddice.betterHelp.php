<?php
if (!file_exists("data/bot/helps/com.lxyddice.betterHelp.json")) {
    file_put_contents("data/bot/helps/com.lxyddice.betterHelp.json", json_encode([
        "name" => "插件社区",
        "plugin" => "com.lxyddice.betterHelp",
        "start" => "bh",
        "info" => "插件社区管理插件，提供插件的帮助、更新、下载等功能\n\n修复了一些问题",
        "author" => "lxyddice",
        "version" => "1.4.0",
        "help" => "##### /bh：查看帮助列表\n\n##### /bh [插件名]：查看命令的帮助\n\n##### /bh <插件名
        >update：检查命令的更新\n\n##### /bh <插件名> download：下载命令的插件\n\n##### /bh <插件名> p <页数>  插件帮助分页\n\n##### /bh a clear：清空帮助缓存"
    ]));
}

class comLxyddiceBetterHelp {
    public function processHelpFiles($directory) {
        $helps = [];
        if (is_dir($directory)) {
            $files = scandir($directory);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) == "json") {
                    $filePath = $directory . "/" . $file;
                    $content = json_decode(file_get_contents($filePath), true);
                    if ($content) {
                        $helps[] = $content;
                    }
                }
            }
        }
        return $helps;
    }

    public function sendPluginUpdateMessage($value, $res, $webhook, $staffid, $action) {
        if ($res["code"] == 0) {
            $updateMessage = "下载成功~\n\n##### 插件:{$value['name']}（{$value['plugin']}）\n\n##### 当前版本：{$res['result']['version']}-更新时间：{$res['result']['updateTime']}\n\n##### 更新内容：\n\n###### {$res['result']['info']}";
            file_put_contents("plugin/".$value["plugin"].".php", base64_decode($res["result"]["code"]));
            send_markdown($updateMessage, $webhook, "插件下载", $staffid);
            $this->cleanHelpFiles();
        } else {
            send_message("下载失败~{$res['message']}", $webhook, $staffid);
        }
        exit();
    }

    public function cleanHelpFiles() {
        $files = scandir("data/bot/helps");
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == "json") {
                unlink("data/bot/helps/".$file);
            }
        }
    }

    public function handleHelpCommand($globalmessage, $webhook, $staffid) {
        $params = explode(" ", $globalmessage);
        $page = isset($params[1]) ? (int)$params[1] : 1;
        $perPage = 10;

        $helps = $this->processHelpFiles("data/bot/helps");
        $totalHelps = count($helps);
        $totalPages = ceil($totalHelps / $perPage);

        if ($page > $totalPages) {
            $page = $totalPages;
        } elseif ($page < 1) {
            $page = 1;
        }

        $start = ($page - 1) * $perPage;
        $end = min($start + $perPage, $totalHelps);

        $reply = "帮助列表（第 $page 页，共 $totalPages 页）：\n";
        for ($i = $start; $i < $end; $i++) {
            $help = $helps[$i];
            $reply .= "##### /bh " . $help["start"] . "：" . $help["name"] . "\n";
        }

        send_markdown($reply, $webhook, "帮助列表", $staffid);
    }

    public function handlePluginCommand($globalmessage, $webhook, $staffid, $bot_run_as) {
        $params = explode(" ", $globalmessage);
        $command = isset($params[1]) ? $params[1] : "";
        $action = isset($params[2]) ? $params[2] : "";

        if (empty($command) || is_numeric($command)) {
            $this->handleHelpCommand($globalmessage, $webhook, $staffid);
        } else {
            $helps = $this->processHelpFiles("data/bot/helps");
            $reply = "";
            $filtered_helps = [];

            foreach ($helps as $help) {
                if (isset($help["start"]) && $help["start"] == $command) {
                    switch ($action) {
                        case "update":
                            $url = $bot_run_as["config"]["pluginUpdateUrl"]."?plugin=".$help["plugin"];
                            $res = json_decode(requests("GET", $url)["body"], true)["result"];
                            if ($res["version"] != $help["version"]) {
                                $updateTime = $res["updateTime"];
                                send_markdown("\n\n##### 插件:{$help['name']}（{$help['plugin']}）发现更新\n\n#####  {$help['version']} -> {$res['version']} 更新于：{$updateTime}\n\n##### 更新内容：\n\n###### {$res['info']}", $webhook, "插件更新", $staffid);
                            } else {
                                send_markdown("没有发现更新~\n\n##### 插件:{$help['name']}（{$help['plugin']}）\n\n##### 当前版本：{$help['version']}", $webhook, "插件更新", $staffid);
                            }
                            break;
                        case "download":
                            $this->sendPluginUpdateMessage($help, json_decode(requests("GET", $bot_run_as["config"]["pluginUpdateUrl"]."?plugin=".$help["plugin"])["body"], true), $webhook, $staffid, $action);
                            break;
                        default:
                            $filtered_helps[] = $help;
                            break;
                    }
                }
            }

            if ($command == "a") {
                switch ($action) {
                    case "clear":
                        $this->cleanHelpFiles();
                        $reply .= "已清空帮助缓存";
                        break;
                    default:
                        break;
                }
            }

            if (!empty($filtered_helps)) {
                $page = isset($params[3]) ? (int)$params[3] : 1;
                $reply = $this->generate_paginated_reply($filtered_helps, $page);
                send_markdown($reply, $webhook, "帮助", $staffid);
            } elseif (!empty($reply)) {
                send_markdown($reply, $webhook, "帮助", $staffid);
            } else {
                send_message("没有找到这个命令的帮助！", $webhook, $staffid);
            }
        }
    }

    private function generate_paginated_reply($helps, $page, $items_per_page = 999) {
        $total_items = count($helps);
        $total_pages = ceil($total_items / $items_per_page);

        if ($page < 1 || $page > $total_pages) {
            return "页码超出范围，请输入有效的页码（1 到 $total_pages）";
        }

        $start_index = ($page - 1) * $items_per_page;
        $end_index = min($start_index + $items_per_page, $total_items);

        $reply = "";
        for ($i = $start_index; $i < $end_index; $i++) {
            $help = $helps[$i];
            $reply .= $help["name"] . "\n\n ###### " . $help["info"] . " - " . $help["author"] . " - " . $help["version"] . "\n\n";
            $reply .= $help["help"] . "\n\n";
        }

        $reply .= "第" . $page . "页 共" . $total_pages . "页";

        return $reply;
    }
}

if (strpos($globalmessage, "/bh") === 0) {
    $comLxyddiceBetterHelp = new comLxyddiceBetterHelp();
    $comLxyddiceBetterHelp->handlePluginCommand($globalmessage, $webhook, $staffid, $bot_run_as);
}
?>
<?php
# 插件：更好的帮助
# 作者：@lxyddice
# 版本：v1.0
# DingraiaPHP官方插件 https://github.com/lxyddice/DingraiaPHP_offical/blob/main/betterHelp.php

if (!file_exists("data/bot/helps/betterHelp.json")) {
    file_put_contents("data/bot/helps/betterHelp.json", json_encode(["help"=>""]));
}

if (strpos($globalmessage, "/bh") === 0) {
    $command = explode(" ", $globalmessage)[1];
    if (empty($command)) {
        $helps = [];
        $files = scandir("data/bot/helps");
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == "json") {
                $helps[$file] = read_file_to_array("data/bot/helps/" . $file);
            }
        }
        $reply = "";
        foreach ($helps as $key => $value) {
            $helps[$key] = $value["help"];
            if (isset($value["start"])) {
                $reply .= "##### /bh " . $value["start"] . "：" . $value["info"] . "\n";
            }
        }
        send_markdown("帮助列表：\n" . $reply, $webhook, "帮助列表", $staffid);
    } else {
        $helps = [];
        $files = scandir("data/bot/helps");
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == "json") {
                $helps[$file] = read_file_to_array("data/bot/helps/" . $file);
            }
        }
        $reply = "";
        foreach ($helps as $key => $value) {
            if (isset($value["start"]) && $value["start"] == $command) {
                $reply .= $value["name"] . "\n\n ###### " . $value["info"] . "\n\n";
                $reply .= $value["help"];
            }
        }
        if (empty($reply)) {
            send_message("没有找到这个命令的帮助！", $webhook, $staffid);
        } else {
            send_markdown($reply, $webhook, "帮助", $staffid);
        }
    }
}
