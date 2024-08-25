<?php
if (isset($bot_run_as)) {
    $comLxyddicegithubWebhookFile = [
        "data/com.lxyddice.githubWebhook/config.json"=>["sendWebhooks"=>[],"secret"=>""],
        "data/com.lxyddice.githubWebhook/data.json"=>[],
        "data/com.lxyddice.githubWebhook/last.json"=>[],
        "data/com.lxyddice.githubWebhook/log.json"=>[],
    ];
    foreach ($comLxyddicegithubWebhookFile as $k => $v) {
        if (!file_exists($k)) {
            mkdir(dirname($k), 0777, true);
            write_to_file_json($k, $v);
        }
    }
    if (!file_exists("data/bot/helps/com.lxyddice.githubWebhook.json")) {
        file_put_contents("data/bot/helps/com.lxyddice.githubWebhook.json", json_encode([
            "start" => "gbwk", 
            "plugin" => "com.lxyddice.githubWebhook",
            "name" => "GitHub Webhook",
            "info" => "处理来自GitHub Webhook的事件",
            "help" => "本插件暂无指令，请在data/com.lxyddice.githubWebhook/config.json中配置Webhook地址和密钥",
            "author" => "lxyddice", 
            "version" => "1.0.0"
        ]));
    }
    if ($bot_run_as["chat_mode"] == "gbwh" && $bot_run_as["verify"]) {
        $gbwhLog = read_file_to_array("data/com.lxyddice.githubWebhook/log.json");
        $gbwhLog[] = $bot_run_as['callbackContent'];
        write_to_file_json("data/com.lxyddice.githubWebhook/log.json", $gbwhLog);

        $gbwhConfig = read_file_to_array("data/com.lxyddice.githubWebhook/config.json");

        # 解析各个事件
        $eventType = $bot_run_as['callbackContent']["header"]["X-Github-Event"];
        switch($eventType) {
            case "issues":
                $action = $bot_run_as['callbackContent']['action'];
                if ($action == "opened") {
                    $issue = $bot_run_as['callbackContent']['issue'];
                    $repository = $bot_run_as['callbackContent']['repository'];
                    $html_url = $issue['html_url'];
                    $title = $issue['title'];
                    $body = $issue['body'];
                    $user = $issue['user'];
                    $user_login = $user['login'];
                    $user_html_url = $user['html_url'];
                    $updated_at = $issue['updated_at'];
                    $repository['name'] = $repository['full_name'];
                    $msg = "[{$repository['name']}]({$repository['html_url']}) New issue: [{$title}]({$html_url}) by [{$user_login}]({$user_html_url})\n\nCreated at: {$updated_at}\n\n{$body}";
                } elseif ($action == "closed") {
                    $issue = $bot_run_as['callbackContent']['issue'];
                    $repository = $bot_run_as['callbackContent']['repository'];
                    $html_url = $issue['html_url'];
                    $title = $issue['title'];
                    $body = $issue['body'];
                    $user = $issue['user'];
                    $user_login = $user['login'];
                    $user_html_url = $user['html_url'];
                    $updated_at = $issue['updated_at'];
                    $repository['name'] = $repository['full_name'];
                    $msg = "[{$repository['name']}]({$repository['html_url']}) Issue closed: [{$title}]({$html_url}) by [{$user_login}]({$user_html_url})\n\nCreated at: {$updated_at}\n\n{$body}";
                }
                break;
            case "issue_comment":
                $action = $bot_run_as['callbackContent']['action'];
                $issue = $bot_run_as['callbackContent']['issue'];
                $comment = $bot_run_as['callbackContent']['comment'];
                $repository = $bot_run_as['callbackContent']['repository'];
                $html_url = $comment['html_url'];
                $body = $comment['body'];
                $user = $comment['user'];
                $user_login = $user['login'];
                $user_html_url = $user['html_url'];
                $updated_at = $comment['updated_at'];
                $repository['name'] = $repository['full_name'];
                $msg = "[{$repository['name']}]({$repository['html_url']}) New comment on issue [{$issue['title']}]({$issue['html_url']}) by [{$user_login}]({$user_html_url})\n\nCreated at: {$updated_at}\n\n{$body}";
                break;
            case "push":
                $pusher = $bot_run_as['callbackContent']['pusher'];
                $repository = $bot_run_as['callbackContent']['repository'];
                $commits = $bot_run_as['callbackContent']['commits'];
                $ref = $bot_run_as['callbackContent']['ref'];
                $repository['name'] = $repository['full_name'];
                $msg = "[{$repository['name']}]({$repository['html_url']}) New push by [{$pusher['name']}]({$pusher['html_url']})\n\nRef: {$ref}\n\n";
                foreach ($commits as $commit) {
                    $msg .= "[".substr($commit['id'], 0, 7)."...]({$commit['url']}) {$commit['message']} by [{$commit['author']['name']}]({$commit['author']['email']})\n\n";
                }
                break;
            case "pull_request":
                $action = $bot_run_as['callbackContent']['action'];
                $pull_request = $bot_run_as['callbackContent']['pull_request'];
                $repository = $bot_run_as['callbackContent']['repository'];
                $html_url = $pull_request['html_url'];
                $title = $pull_request['title'];
                $body = $pull_request['body'];
                $user = $pull_request['user'];
                $user_login = $user['login'];
                $user_html_url = $user['html_url'];
                $updated_at = $pull_request['updated_at'];
                $repository['name'] = $repository['full_name'];
                $msg = "[{$repository['name']}]({$repository['html_url']}) New pull request: [{$title}]({$html_url}) by [{$user_login}]({$user_html_url})\n\nCreated at: {$updated_at}\n\n{$body}";
                break;
            case "pull_request_review":
                $action = $bot_run_as['callbackContent']['action'];
                $pull_request = $bot_run_as['callbackContent']['pull_request'];
                $review = $bot_run_as['callbackContent']['review'];
                $repository = $bot_run_as['callbackContent']['repository'];
                $html_url = $review['html_url'];
                $body = $review['body'];
                $user = $review['user'];
                $user_login = $user['login'];
                $user_html_url = $user['html_url'];
                $updated_at = $review['updated_at'];
                $repository['name'] = $repository['full_name'];
                $msg = "[{$repository['name']}]({$repository['html_url']}) New review on pull request [{$pull_request['title']}]({$pull_request['html_url']}) by [{$user_login}]({$user_html_url})\n\nCreated at: {$updated_at}\n\n{$body}";
                break;
            case "pull_request_review_comment":
                $action = $bot_run_as['callbackContent']['action'];
                $pull_request = $bot_run_as['callbackContent']['pull_request'];
                $comment = $bot_run_as['callbackContent']['comment'];
                $repository = $bot_run_as['callbackContent']['repository'];
                $html_url = $comment['html_url'];
                $body = $comment['body'];
                $user = $comment['user'];
                $user_login = $user['login'];
                $user_html_url = $user['html_url'];
                $updated_at = $comment['updated_at'];
                $repository['name'] = $repository['full_name'];
                $msg = "[{$repository['name']}]({$repository['html_url']}) New comment on pull request [{$pull_request['title']}]({$pull_request['html_url']}) by [{$user_login}]({$user_html_url})\n\nCreated at: {$updated_at}\n\n{$body}";
                break;
            case "watch":
                $action = $bot_run_as['callbackContent']['action'];
                $repository = $bot_run_as['callbackContent']['repository'];
                $sender = $bot_run_as['callbackContent']['sender'];
                $repository['name'] = $repository['full_name'];
                $msg = "[{$repository['name']}]({$repository['html_url']}) New star by [{$sender['login']}]({$sender['html_url']})";
                break;
            case "fork":
                $action = $bot_run_as['callbackContent']['action'];
                $repository = $bot_run_as['callbackContent']['repository'];
                $forkee = $bot_run_as['callbackContent']['forkee'];
                $repository['name'] = $repository['full_name'];
                $msg = "[{$repository['name']}]({$repository['html_url']}) New fork by [{$forkee['owner']['login']}]({$forkee['owner']['html_url']})";
                break;
            case "create":
                $action = $bot_run_as['callbackContent']['action'];
                $ref = $bot_run_as['callbackContent']['ref'];
                $ref_type = $bot_run_as['callbackContent']['ref_type'];
                $repository = $bot_run_as['callbackContent']['repository'];
                $repository['name'] = $repository['full_name'];
                $msg = "[{$repository['name']}]({$repository['html_url']}) New {$ref_type} {$ref}";
                break;
            case "delete":
                $action = $bot_run_as['callbackContent']['action'];
                $ref = $bot_run_as['callbackContent']['ref'];
                $ref_type = $bot_run_as['callbackContent']['ref_type'];
                $repository = $bot_run_as['callbackContent']['repository'];
                $repository['name'] = $repository['full_name'];
                $msg = "[{$repository['name']}]({$repository['html_url']}) Delete {$ref_type} {$ref}";
                break;
            case "commit_comment":
                $action = $bot_run_as['callbackContent']['action'];
                $comment = $bot_run_as['callbackContent']['comment'];
                $repository = $bot_run_as['callbackContent']['repository'];
                $html_url = $comment['html_url'];
                $body = $comment['body'];
                $user = $comment['user'];
                $user_login = $user['login'];
                $user_html_url = $user['html_url'];
                $updated_at = $comment['updated_at'];
                $repository['name'] = $repository['full_name'];
                $msg = "[{$repository['name']}]({$repository['html_url']}) New comment on commit by [{$user_login}]({$user_html_url})\n\nCreated at: {$updated_at}\n\n{$body}";
                break;
            case "in_progress":
                $action = $bot_run_as['callbackContent']['action'];
                $repository = $bot_run_as['callbackContent']['repository'];
                $msg = "[{$repository['name']}]({$repository['html_url']}) {$action} in progress";
                break;
            }
            if ($msg) {
                foreach ($gbwhConfig["sendWebhooks"] as $webhook) {
                    send_markdown($msg, $webhook, "GitHub Webhook");
                }
            } else {
                //send_markdown("Unknown event type: {$eventType}", "https://oapi.dingtalk.com/robot/send?access_token=ef8f15f1542ff3364ef3cfc6045cca30369cf0d49cf1f97751faffbe889242cc", "GitHub Webhook");
            }
        }
    }