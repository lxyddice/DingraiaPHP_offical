<?php
# 插件：禁言游戏合集
# 作者：@lxyddice
# 版本：v1.0
# DingraiaPHP官方插件 https://github.com/lxyddice/DingraiaPHP_offical/edit/main/muteGames.php
if (!file_exists("data/muteGames/data.json")) {
    mkdir("data/muteGames", 0777, true);
    file_put_contents("data/muteGames/data.json", "[]");
    file_put_contents("data/muteGames/lunpan.json", "[]");
}
if (!file_exists("data/bot/helps/muteGames.json")) {
    file_put_contents("data/bot/helps/muteGames.json", json_encode(["start"=>"mg", "name"=>"禁言游戏合集", "info"=>"禁言游戏合集，作者：lxyddice", "help"=>"自裁 再见...世界...\n\n决斗  开启决斗，随机禁言一方\n\n轮盘  开启俄罗斯轮盘", "version"=>"v1.0"]));
}
if ($globalmessage == "自裁") {
    $t = rand(10, 600);
    $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
    $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
    $res = mute_user($t*1000,$conversationId,[$staffid],$token);
    send_message("恭喜 {$name} 获得{$t}秒禁言！", $webhook, $staffid);
}
if ($globalmessage == "决斗") {
    if (file_exists("data/muteGames/data.json")) {
        $f = read_file_to_array("data/muteGames/data.json");
        if (!isset($f[$conversationId])) {
            $f[$conversationId] = $staffid;
            file_put_contents("data/muteGames/data.json", json_encode($f));
            send_message("{$name}开启了决斗，ta渴望一个有价值的对手！", $webhook, $staffid);
        } else {
            if ($f[$conversationId] == $staffid) {
                send_message("你已经在决斗中了！", $webhook, $staffid);
                exit();
            }
            $array = [$staffid, $f[$conversationId]];
            $winner = 禁言游戏合集：决斗($array);
            $t = rand(10, 600);
            $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
            $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
            $res = mute_user($t*1000,$conversationId,[$winner],$token);
            $winnerName = userinfo($winner, $token)['result']['name'];
            unset($f[$conversationId]);
            file_put_contents("data/muteGames/data.json", json_encode($f));
            send_message("最终，{$winnerName}还是没能躲过对手的致命一击，获得{$t}秒养伤时间！", $webhook);
        }
    }
}
if ($globalmessage == "轮盘") {
    if (file_exists("data/muteGames/lunpan.json")) {
        $f = read_file_to_array("data/muteGames/lunpan.json");
        $lunpanNew = false;
        if (!isset($f[$conversationId])) {
            $f[$conversationId] = 6;
            $lunpanNew = true;
            file_put_contents("data/muteGames/lunpan.json", json_encode($f));
            send_message("{$name}开启了一轮俄罗斯轮盘，这是一把6发的左轮手枪，装填了1发子弹...", $webhook, $staffid);

        }
        if ($f[$conversationId] == 0) {
            send_message("这一轮俄罗斯轮盘已经结束了！", $webhook, $staffid);
            unset($f[$conversationId]);
            file_put_contents("data/muteGames/lunpan.json", json_encode($f));
            exit();
        }
        $f[$conversationId] = $f[$conversationId] - 1;
        $die = rand(1, $f[$conversationId]);
        if ($die == 1) {
            $t = rand(10, 600);
            $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
            $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
            $res = mute_user($t*1000,$conversationId,[$staffid],$token);
            send_message("{$name}开了一枪，枪响了，被禁言{$t}秒！此轮俄罗斯轮盘结束！", $webhook, $staffid);
            unset($f[$conversationId]);
            file_put_contents("data/muteGames/lunpan.json", json_encode($f));
        } else {
            send_message("{$name}在俄罗斯轮盘中幸运地活了下来！此盘俄罗斯轮盘还剩{$f[$conversationId]}发子弹！", $webhook, $staffid);
            file_put_contents("data/muteGames/lunpan.json", json_encode($f));
    
        }
    }

}

function 禁言游戏合集：决斗($array) {
    $rand = array_rand($array);
    return $array[$rand];
}
