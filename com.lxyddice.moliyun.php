<?php
<?php
if (isset($bot_run_as)) {
    $moliyunFiles = [
        "data/moliyun/key.json",
        "data/moliyun/log.json"
    ];

    foreach ($moliyunFiles as $file) {
        if (!file_exists($file)) {
            mkdir(dirname($file), 0777, true);
            file_put_contents($file, json_encode([]));
        }
    }
}

if (!file_exists("data/bot/helps/com.lxyddice.moliyun.json")) {
    file_put_contents("data/bot/helps/com.lxyddice.moliyun.json", json_encode([
        "start"=>"moli",
        "plugin" => "com.lxyddice.moliyun",
        "name"=>"茉莉云机器人",
        "info"=>"茉莉云机器人聊天",
        "help"=>"茉莉云机器人，请用/ml <消息>  开始聊天",
        "version"=>"1.2.0",
        "author"=>"lxyddice",
    ]));
}
if (strpos($globalmessage, "/ml ") === 0) {
    $molistaffid = substr($staffid, 0, 6);
    # 把conversationId哈希为6位数字
    $moliconversationId = base_convert(substr(md5($conversationId), 0, 6), 16, 10);
    $content = substr($globalmessage, 4);
    $url = "https://api.mlyai.com/reply";
    if ($conversationType == "1") {# 私聊
        $content = ["content" => $content, "type" => "1", "from" => $molistaffid, "fromName" => $name];
    } else {
        $content = ["content" => $content, "type" => "2", "from" => $molistaffid, "to" => $moliconversationId, "toName" => $groupnanme];
    }
    $moliApiKey = read_file_to_array("data/moliyun/key.json");
    if (isset($moliApiKey["ApiKey"]) && !empty($moliApiKey["ApiKey"])) {
        $res = json_decode(requests("POST", $url, $content, ["Content-Type" => "application/json", "Api-Key"=>$moliApiKey["ApiKey"],"Api-Secret"=>$moliApiKey["ApiSecret"]])["body"], true);
        send_message($res["data"][0]["content"], $webhook, $staffid);
        # 记录日志
        $log = read_file_to_array("data/moliyun/log.json");
        $log[] = ["time" => date("Y-m-d H:i:s"), "staffid" => $staffid, "name" => $name, "content" => $content, "reply" => $res];
        file_put_contents("data/moliyun/log.json", json_encode($log));
    } else {
        send_message("茉莉云机器人接口未配置！", $webhook, $staffid);
    }
    
}