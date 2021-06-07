<?php
error_reporting(0);
include('jdf.php');
// ایجاد متغیر های عمومی
$C = new stdClass;
$T = new stdClass;

// اطلاعات دیتابیس
$C->dbuser = '';
$C->dbpass = '';
$C->dbname = '';
$C->dbhost = '';

// توکن ربات
$C->token = '';

// تنظیم منطقه زمانی سرور برای نمایش صحیح ساعت ایران
date_default_timezone_set('Asia/Tehran');

// وصل شدن به دیتابیس
$db = mysqli_connect($C->dbhost, $C->dbuser, $C->dbpass, $C->dbname);
mysqli_set_charset($db, "utf8mb4");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}

// دریافت اطلاعات خروجی بعد از انجام عملیات تلگرام
$update = json_decode(file_get_contents('php://input'));
//file_put_contents('testTelegram.txt', (var_export(json_decode(file_get_contents('php://input')), true)), FILE_APPEND);
$T->text = isset($update->message->text) ? $update->message->text : $update->message->caption;
$T->chat_id = $update->message->chat->id;
$T->message_id = $update->message->message_id;
$T->from_id = $update->message->from->id;
$T->user_name = $update->message->from->username;
$T->first_name = $update->message->from->first_name;
$T->last_name = $update->message->from->last_name;
$T->chat_tp = $update->message->chat->type;
$T->from_reply_id = $update->message->from->first_name;
$T->reply_msg = $update->message->reply_to_message;
$T->reply_msg_to = $update->message->reply_to_message->from->id;
$T->reply_msg_id = $update->message->reply_to_message->message_id;
$T->new_chat_member = $update->message->new_chat_member;
$T->new_user = $update->message->new_chat_member->username;
$T->reply_msg_text = isset($update->message->reply_to_message->text) ? $update->message->reply_to_message->text : $update->message->reply_to_message->caption;
$T->gpname = $update->message->chat->title;
$T->video_id = $update->message->video->file_id;
$T->photo_id = $update->message->photo[count($update->message->photo) - 1]->file_id;
$T->gif_id = $update->message->document->file_id;
$T->master = "12345";
$T->channel_text = $update->channel_post->text;
$T->chanel_id = $update->channel_post->chat->id;

$uquery = mysqli_query($db, "SELECT * FROM users WHERE user_id='$T->from_id'");
if (mysqli_num_rows($uquery) == 1) {
    while ($row = mysqli_fetch_assoc($uquery)) {
        $call_name = $row['call_name'];
    }
} else {
    mysqli_query($db, "INSERT INTO users
						(user_id,user_name,first_name,last_name,call_name)
						VALUES('$T->from_id','$T->user_name','$T->first_name','$T->last_name','$T->first_name')");
    $call_name = $T->first_name;
}


// پاسخ به کال بک
if (isset($update->callback_query)) {
    $cb_id = $update->callback_query->id;
    $cb_first_name = $update->callback_query->from->first_name;
    connect('answerCallbackQuery', array(
        'callback_query_id' => $cb_id,
        'text' => 'سلام ' . $cb_first_name,
        'show_alert' => true
    ));

}
