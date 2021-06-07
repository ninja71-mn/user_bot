<?php
include 'config.php';
global $C;

function connect($method, $datas = array())
{
    global $C;
    $url = "https://api.telegram.org/bot" . $C->token . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    return json_decode($res);
}

function getChatAdministrators($chat_id)
{
    return connect('getChatAdministrators', array(
        'chat_id' => $chat_id
    ))->result;
}

function getChatMember($chat_id, $user_id)
{
    return connect('getChatMember', array(
        'chat_id' => $chat_id,
        'user_id' => $user_id
    ))->result;
}

function sendMessage($chat_id, $text)
{
    return connect('sendMessage', array(
        'chat_id' => $chat_id,
        'text' => $text
    ))->result;
}

function replyMessage($chat_id, $text,$reply_to)
{
    return connect('sendMessage', array(
        'chat_id' => $chat_id,
        'text' => $text,
        'reply_to_message_id'=>$reply_to
    ))->result;
}

function deleteMessage($chat_id, $message_id)
{
    return connect('deleteMessage', array(
        'chat_id' => $chat_id,
        'message_id' => $message_id
    ))->result;
}

function getChat($chat_id)
{
    return connect('getChat', array(
        'chat_id' => $chat_id
    ))->result;
}

function keyboard($chat_id, $text, $buttons)
{
    return connect('sendMessage', array(
        'chat_id' => $chat_id,
        'text' => $text,
        'reply_markup' => json_encode(array(
            'keyboard' => $buttons,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ))
    ))->result;
}

function restrictChatMember($chat_id, $user_id, $time)
{
    return connect('restrictChatMember', array(
        'chat_id' => $chat_id,
        'user_id' => $user_id,
        'until_date' => $time
    ))->result;
}

function unRestrictChatMember($chat_id, $user_id)
{
    return connect('restrictChatMember', array(
        'chat_id' => $chat_id,
        'user_id' => $user_id,
        'can_send_messages' => true,
        'can_send_media_messages' => true,
        'can_send_other_messages' => false,
        'can_add_web_page_previews' => false,
        'can_send_polls' => false,
        'can_change_info' => false,
        'can_pin_messages' => false,

    ))->result;
}

function kickChatMember($chat_id, $user_id)
{
    return connect('kickChatMember', array(
        'chat_id' => $chat_id,
        'user_id' => $user_id
    ))->result;
}
function getMe()
{
    return connect('getMe')->result;
}
