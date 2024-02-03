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
