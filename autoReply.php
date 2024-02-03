<?php
# 插件：自定义回复
# 作者：@lxyddice
# 版本：v1.0
# DingraiaPHP官方插件 https://github.com/lxyddice/DingraiaPHP_offical/blob/main/autoReply.php
if (!file_exists("data/autoReply/word.json")) {
    mkdir("data/autoReply", 0777, true);
    file_put_contents("data/autoReply/word.json", "[]");
}
if (!file_exists("data/bot/helps/autoReply.json")) {
    file_put_contents("data/bot/helps/autoReply.json", json_encode(["start"=>"ar", "name"=>"自定义回复", "info"=>"自定义机器人回复，作者：lxyddice", "help"=>"/自定义回复 学习 #<收到的内容>#<回复>~# 精确/模糊  学习一个自定义回复\n\n/自定义回复 删除 #你好#  删除一个自定义回复\n\n/自定义回复 列表  查看自定义回复列表", "version"=>"v1.0"]));
}
if (strpos($globalmessage, "/自定义回复") === 0) {
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
        if (strpos($globalmessage, $value["keyword"]) !== false) {
            send_message($value["reply"], $webhook);
        }
    }
}