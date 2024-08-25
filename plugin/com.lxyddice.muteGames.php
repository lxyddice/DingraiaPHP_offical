<?php
if (isset($bot_run_as)) {
    $muteGamesFile = [
        "data/muteGames/data.json",
        "data/muteGames/lunpan.json",
        "data/muteGames/fenxi.json"
    ];

    foreach ($muteGamesFile as $file) {
        if (!file_exists($file)) {
            mkdir(dirname($file), 0777, true);
            file_put_contents($file, json_encode([]));
        }
    }

    if (!file_exists("data/bot/helps/com.lxyddice.muteGames.json")) {
        file_put_contents("data/bot/helps/com.lxyddice.muteGames.json", json_encode([
            "start"=>"mg","plugin" => "com.lxyddice.muteGames", 
            "name"=>"禁言游戏合集", "info"=>"禁言游戏合集", 
            "help"=>"自裁 再见...世界...\n\n决斗  与别人决斗吧！\n\n轮盘  俄罗斯轮盘赌\n\n/mg me  个人数据", 
            "version"=>"1.0.7", 
            "author"=>"lxyddice"]));
    }
}

if ($globalmessage == "自裁") {
    $t = rand(10, 300);
    $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
    $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
    $res = mute_user($t*1000,$conversationId,[$staffid],$token);
    send_message("{$name}噗的一下不见了，需要{$t}秒才能回来...", $webhook, $staffid);
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
            $winner = array_rand($array);
            $winner = $array[$winner];
            unset($array[$winner]);
            $l = $array[0];
            $t = rand(1, 300);
            
            $rn = rand(1, 5);
            if ($rn == 3) {
                $rm = "重击！";
                $t = $t * (rand(12, 24) / 10);
            }
            
            $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
            $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
            $res = mute_user($t*1000,$conversationId,[$winner],$token);
            $winnerName = userinfo($winner, $token)['result']['name'];
            unset($f[$conversationId]);
            file_put_contents("data/muteGames/data.json", json_encode($f));
            send_message($rm."最终，{$winnerName}还是没能躲过对手的致命一击，需要{$t}秒养伤时间！", $webhook);
            if ($t >= 350) {
                updateMoney($guserarr['uid'], 1);
                send_message("{$winnerName}得到了一份养伤补助，获得金钱x1！", $webhook);
            }
            $f = read_file_to_array("data/muteGames/fenxi.json");
            if (isset($f["solo"][$winner])) {
                $f["solo"][$winner]["total"] += 1;
            } else {
                $f["solo"][$winner] = ["total"=>1, "win"=>0];
            }
            if (isset($f["solo"][$l])) {
                $f["solo"][$l]["total"] += 1;
                $f["solo"][$l]["win"] += 1;
            } else {
                $f["solo"][$l] = ["total"=>1, "win"=>1];
            }
            file_put_contents("data/muteGames/fenxi.json", json_encode($f));
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
            send_message("你拿起了一把手枪，转动了滑膛...“没想到你真的来了...”卡尔莫的声音有些颤抖。{$name}摇了摇头，把枪口对准了自己...", $webhook, $staffid);
        }
        if ($f[$conversationId] == 0) {
            send_message("这一轮俄罗斯轮盘已经结束了！", $webhook, $staffid);
            unset($f[$conversationId]);
            file_put_contents("data/muteGames/lunpan.json", json_encode($f));
            exit();
        }
        $die = rand(1, $f[$conversationId]);
        $f[$conversationId] = $f[$conversationId] - 1;
        if ($die == 1) {
            $t = rand(90, 450);
            $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
            $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
            $res = mute_user($t*1000,$conversationId,[$staffid],$token);
            send_message("“砰！”{$name}扣动了扳机，一发子弹从枪口中飞出，卡尔莫一下子闭上了眼睛，过了一会睁开了，发现{$name}已经倒在了地上...\n虽然这是虚空世界，但是{$name}还是需要{$t}秒才能重构身体...", $webhook, $staffid);
            unset($f[$conversationId]);
            file_put_contents("data/muteGames/lunpan.json", json_encode($f));
            
            $f = read_file_to_array("data/muteGames/fenxi.json");
            if (isset($f["lunpan"][$staffid])) {
                $f["lunpan"][$staffid]["total"] += 1;
                $f["lunpan"][$staffid]["totalDie"] += 1;
            } else {
                $f["lunpan"][$staffid] = ["total"=>1, "totalDie"=>1];
            }
            file_put_contents("data/muteGames/fenxi.json", json_encode($f));
        } else {
            if ($f[$conversationId] == 1) {
                unset($f[$conversationId]);
                send_message("“好吧，我嘞个命不该绝啊...恭喜你，{$name}”，你满身大汗走出房间，这一切都结束了", $webhook, $staffid);
            } else {
                send_message("“啪！”{$name}扣动了扳机，却没有发出枪响，卡尔莫松了一口气，{$name}手中的枪掉在地上，早已满身冷汗...\n这把黄金光泽的手枪还剩{$f[$conversationId]}发子弹...", $webhook, $staffid);
            }
            file_put_contents("data/muteGames/lunpan.json", json_encode($f));
            
            $f = read_file_to_array("data/muteGames/fenxi.json");
            if (isset($f["lunpan"][$staffid])) {
                $f["lunpan"][$staffid]["total"] += 1;
            } else {
                $f["lunpan"][$staffid] = ["total"=>1, "totalDie"=>0];
            }
            file_put_contents("data/muteGames/fenxi.json", json_encode($f));
        }
    }

}
if ($globalmessage == "/mg me") {
    if (file_exists("data/muteGames/fenxi.json")) {
        $f = read_file_to_array("data/muteGames/fenxi.json");
        if (isset($f["solo"][$staffid])) {
            $total = $f["solo"][$staffid]["total"] ?? 0;
            $win = $f["solo"][$staffid]["win"] ?? 0;
            $lunpanTotal = $f["lunpan"][$staffid]["total"] ?? 0;
            $lunpanTotalDie = $f["lunpan"][$staffid]["totalDie"] ?? 0;
            send_markdown("用户名->{$name}\n\n## 决斗数据\n\n游玩场数->{$total}\n\n胜利数->{$win}\n\n## 轮盘数据\n\n游玩场数->{$lunpanTotal}\n\n死亡数->{$lunpanTotalDie}", $webhook, $staffid);
        } else {
            send_markdown("你还没有游玩数据...", $webhook, $staffid);
        }
    }
}
function 禁言游戏合集：决斗($array) {
    $rand = array_rand($array);
    return $array[$rand];
}