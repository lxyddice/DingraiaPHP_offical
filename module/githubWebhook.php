<?php
function DingraiaPHPGithubWebhookMain($b, $c) {
    global $bot_run_as;
    global $hideLoadPluginInfo_B;
    
    $hideLoadPluginInfo_B = 1;
    $hideLoadPluginInfo = 1;
    $bot_run_as['config']['notSendDefault'] = 1;
    $bot_run_as['config']['index_hide_load'] = 1;
    
    $githubSecret = read_file_to_array("data/com.lxyddice.githubWebhook/config.json")["secret"];
    $bot_run_as["chat_mode"] = "gbwh";
    $bot_run_as["callbackContent"] = $b;
    $bot_run_as['plugin']['index_hide_load'] = 1;
    $bot_run_as["callbackContent"]["header"] = getallheaders();

    DingraiaPHPAddEndModulePlugin("module/DingraiaPHP/plugin/githubWebhook.php", "DingraiaPHPGithubWebhookEnd");
    
    $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
    $body = file_get_contents('php://input');
    $calculatedSignature = 'sha256=' . hash_hmac('sha256', $body, $githubSecret);
    
    if (!hash_equals($signature, $calculatedSignature)) {
        $bot_run_as["verify"] = false;
        write_to_file_json("data/com.lxyddice.githubWebhook/vf.json",[$signature_sha1, $bot_run_as["callbackContent"]["header"]["X-Hub-Signature-256"], $githubSecret]);
        $bot_run_as["response"] = ["code" => 403, "message" => "Forbidden"];
        return $b;
    }
    $bot_run_as["response"] = ["code" => 0, "message" => "OK"];
    $bot_run_as["verify"] = true;
    return $b;
}

function DingraiaPHPGithubWebhookEnd() {
    global $bot_run_as;
    $back = $bot_run_as["response"];
    $back["request_id"] =  $bot_run_as["RUN_ID"];
    header('Content-Type:application/json; charset=utf-8');
    if ($bot_run_as["response"]["status"] == 0) {
        echo(json_encode($back));
    } else {
        echo(json_encode($bot_run_as["response"]));
    }
}