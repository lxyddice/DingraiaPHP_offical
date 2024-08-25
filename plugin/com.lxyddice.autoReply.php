<?php
# 插件：自定义回复
# 作者：@lxyddice
# 版本：v1.1
# DingraiaPHP官方插件 https://github.com/lxyddice/DingraiaPHP_offical/blob/main/autoReply.php
if (isset($bot_run_as)) {
    $muteGamesFile = [
        "data/autoReply/data.json",
    ];

    foreach ($muteGamesFile as $file) {
        if (!file_exists($file)) {
            mkdir(dirname($file), 0777, true);
            file_put_contents($file, json_encode([]));
        }
    }

    if (!file_exists("data/bot/helps/com.lxyddice.autoRelpy.json")) {
        file_put_contents("data/bot/helps/com.lxyddice.autoRelpy.json", json_encode([
            "start"=>"ap",
            "plugin" => "com.lxyddice.autoRelpy", 
            "name"=>"自定义回复", "info"=>"自定义回复", 
            "help"=>"/自定义回复 学习 <#关键词><#回复词#> <精确/模糊>\n\n/自定义回复 删除 <关键词>\n\n/自定义回复 列表  查看自定义回复列表",
            "version"=>"1.1", 
            "author"=>"lxyddice"]));
    }
}

if (strpos($globalmessage, "/自定义回复") === 0) {
    if (!permission_check("autoReply", $guserarr["uid"])) {
        send_message("你没有权限使用自定义回复！", $webhook, $staffid);
        exit();
    }
    $command = explode(" ", $globalmessage)[1];
    if ($command == "学习") {
        $content = explode(" ", $globalmessage)[2];
        # 示例：自定义回复 学习 #你好#你好呀~# 精确/模糊
        $content = explode("#", $content);
        $keyword = $content[1];
        $reply = $content[2];
        $type = explode(" ", $globalmessage)[3];
        if ($type == "精确") {
            $data = ["keyword" => $keyword, "reply" => $reply, "type" => "精确"];
        } else {
            $data = ["keyword" => $keyword, "reply" => $reply, "type" => "模糊"];
        }
        if ($data) {
            $f = read_file_to_array("data/autoReply/word.json");
            # 防止重复
            foreach ($f as $key => $value) {
                if ($value["keyword"] == $keyword) {
                    send_message("学习失败！已经有这个关键词了！", $webhook, $staffid);
                    exit();
                }
            } 
            array_push($f, $data);
            file_put_contents("data/autoReply/word.json", json_encode($f, JSON_UNESCAPED_UNICODE));
        }
        send_message("学习成功！", $webhook, $staffid);
    } elseif ($command == "删除") {
        $content = explode(" ", $globalmessage)[2];
        $f = read_file_to_array("data/autoReply/word.json");
        $ok = false;
        foreach ($f as $key => $value) {
            if ($value["keyword"] == $content) {
                unset($f[$key]);
                $ok = true;
            }
        }
        if ($ok) {
            send_message("删除成功！", $webhook, $staffid);
        } else {
            send_message("删除失败！可能没有这个关键词！", $webhook, $staffid);
        }
        file_put_contents("data/autoReply/word.json", json_encode($f, JSON_UNESCAPED_UNICODE));
    } elseif ($command == "列表") {
        $f = read_file_to_array("data/autoReply/word.json");
        $reply = "";
        foreach ($f as $key => $value) {
            $reply .= "##### 关键词：" . $value["keyword"] . " 回复：" . $value["reply"] . " 类型：" . $value["type"] . "\n";
        }
        send_markdown("自定义回复列表：\n" . $reply, $webhook, "自定义回复列表", $staffid);
    }
}

$autoReplyData = read_file_to_array("data/autoReply/word.json");
foreach ($autoReplyData as $key => $value) {
    if ($value["type"] == "精确") {
        if ($value["keyword"] == $globalmessage) {
            send_message($value["reply"], $webhook);
        }
    } else {
        if (strpos($globalmessage, $value["keyword"]) === true) {
            send_message($value["reply"], $webhook);
        }  
    }
}