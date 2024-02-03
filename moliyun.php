<?php
# 接入茉莉云机器人
if (!file_exists("data/moliyun/key.json")) {
    mkdir("data/moliyun", 0777, true);
    file_put_contents("data/moliyun/key.json", json_encode(["ApiKey"=>"","ApiSecret"=>""]));
}
if (!file_exists("data/bot/helps/moliyun.json")) {
    file_put_contents("data/bot/helps/moliyun.json", json_encode(["start"=>"moli", "name"=>"茉莉云机器人", "info"=>"茉莉云机器人帮助，作者：lxyddice", "help"=>"/ml <内容>  茉莉云机器人聊天", "version"=>"v1.0"]));
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
    } else {
        send_message("茉莉云机器人接口未配置！", $webhook, $staffid);
    }
    
}