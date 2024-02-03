<?php
# 插件：摸一摸
# 作者：@lxyddice
# 版本：v1.0
# DingraiaPHP官方插件 https://github.com/lxyddice/DingraiaPHP_offical/blob/main/patPat.php
if (!file_exists("data/bot/helps/patPat.json")) {
    file_put_contents("data/bot/helps/patPat.json", json_encode(["start"=>"pat", "name"=>"摸一摸", "info"=>"摸一摸，作者：lxyddice", "help"=>"摸我  摸摸自己\n\n摸@某人  摸摸某人", "version"=>"v1.0"]));
}
if ($globalmessage == "摸我") {
    $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
    $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
    $aUrl = userinfo($staffid, $token)['result']['avatar'];
    $res = requests("GET", "https://api.wer.plus/api/ruad?url={$aUrl}",[],[]);
    $res = json_decode($res["body"], true);
    if ($res["code"] == 200) {
        send_markdown("![avatar]({$res['url']})", $webhook, "摸一摸");
    }
}
if ($globalmessage == "摸 ") {
    $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
    $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
    $aUrl = userinfo($atUsers[0]['staffId'], $token)['result']['avatar'];
    $res = requests("GET", "https://api.wer.plus/api/ruad?url={$aUrl}",[],[]);
    $res = json_decode($res["body"], true);
    if ($res["code"] == 200) {
        send_markdown("![avatar]({$res['url']})", $webhook, "摸一摸");
    }
}