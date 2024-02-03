<?php
# 插件：欢迎消息
# 作者：@lxyddice
# 版本：v1.0
# DingraiaPHP官方插件 https://github.com/lxyddice/DingraiaPHP_offical/blob/main/welcome.php
if ($bot_run_as['chat_mode'] == "cb") {
    if ($bot_run_as['callbackContent']['EventType'] == "chat_add_member") {
        $userIds = $bot_run_as['callbackContent']['UserId'];
        $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
        $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
        # 如果userIds是数组，说明是多人加入，然后遍历数组，逐个发送欢迎消息
        if (is_array($userIds)) {
            foreach ($userIds as $userId) {
                $username = userinfo($userId, $token)['result']['name'];
                $welcomeText = "欢迎新成员 " . $username . " 加入本群！\n时间：" . date("Y-m-d H:i:s", time());
                send_message($welcomeText, $webhook, $userId);
            }
        } else {
            $username = userinfo($userIds, $token)['result']['name'];
            $welcomeText = "欢迎新成员 " . $username . " 加入本群！\n时间：" . date("Y-m-d H:i:s", time());
            send_message($welcomeText, $webhook, $userId);
        }
    }
    if ($bot_run_as['callbackContent']['EventType'] == "chat_quit") {
        $userId = $bot_run_as['callbackContent']['Operator'];
        $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
        $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
        $username = userinfo($userId, $token)['result']['name'];
        $quitText = "成员 " . $username . " 退出本群！\n时间：" . date("Y-m-d H:i:s", time());
        send_message($quitText, $webhook);
    }
}