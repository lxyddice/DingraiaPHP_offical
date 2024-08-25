<?php
if (isset($bot_run_as)) {
    $patPatFiles = [
        "data/patPat/log.json",
    ];

    foreach ($patPatFiles as $file) {
        if (!file_exists($file)) {
            mkdir(dirname($file), 0777, true);
            file_put_contents($file, json_encode([]));
        }
    }
}

if (!file_exists("data/bot/helps/com.lxyddice.patPat.json")) {
    file_put_contents("data/bot/helps/com.lxyddice.patPat.json", json_encode([
        "start"=>"patpat",
        "plugin" => "com.lxyddice.patPat",
        "name"=>"摸一摸",
        "info"=>"摸摸你~",
        "help"=>"摸我  摸摸自己的头像\n\n摸<@user>  摸摸某人",
        "version"=>"1.2.1",
        "author"=>"lxyddice",
    ]));
}

if ($globalmessage == "摸我") {
    $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
    $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
    $aUrl = userinfo($staffid, $token)['result']['avatar'];
    $res = requests("GET", "https://api.wer.plus/api/ruad?url={$aUrl}",[],[]);
    $res = json_decode($res["body"], true);
    if ($res["code"] == 200) {
        send_markdown("![avatar]({$res['data']['image_url']})", $webhook, "摸一摸");
    } else {
        send_markdown("{$res['code']}：{$res['msg']}", $webhook, "摸一摸出错了！");
    }
    # 记录摸摸
    $log = read_file_to_array("data/patPat/log.json");
    $log[$chatbotCorpId][$staffid] = $log[$chatbotCorpId][$staffid] + 1;
    $log["all"][$staffid] = $log["all"][$staffid] + 1;
    $log["get"][] = ["staffid"=>$staffid,"time"=>time(), "type"=>"self", "result"=>$res];
    write_to_file_json("data/patPat/log.json", $log);
}
if ($globalmessage == "摸 ") {
    $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
    $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
    $aUrl = userinfo($atUsers[0]['staffId'], $token)['result']['avatar'];
    $res = requests("GET", "https://api.wer.plus/api/ruad?url={$aUrl}",[],[]);
    $res = json_decode($res["body"], true);
    if ($res["code"] == 200) {
        send_markdown("![avatar]({$res['data']['image_url']})", $webhook, "摸一摸");
    } else {
        send_markdown("{$res['code']}：{$res['msg']}", $webhook, "摸一摸出错了！");
    }
    # 记录摸摸
    $log = read_file_to_array("data/patPat/log.json");
    $log[$chatbotCorpId][$staffid] = $log[$chatbotCorpId][$staffid] + 1;
    $log["all"][$staffid] = $log["all"][$staffid] + 1;
    $log["get"][] = ["staffid"=>$staffid,"time"=>time(), "type"=>"other", "result"=>$res];
    write_to_file_json("data/patPat/log.json", $log);
}