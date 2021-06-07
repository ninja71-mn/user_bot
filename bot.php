<?php
global $T;
require 'function.php';
$me_id = '';

$me_username = getMe()->username;
// نمایش چت آی دی فعلی
if ($T->text == '/id') {
    sendMessage($T->chat_id, getChat($T->chat_id)->id);
}

if ($T->chat_tp != "private" && $T->chat_tp != "channel") {
    $user_detail = getChatMember($T->chat_id, $T->from_id);
    //متد خوش آمد گویی
    if ($T->new_chat_member != '') {
        if ($me_username != $T->new_user) {
            if ($T->new_user != '') {
                $text = "@" . $T->new_user . "\n" . " سلام " . $T->new_chat_member->first_name . ' ' . $T->new_chat_member->last_name . " عزیز،  به گروه یوزرکد خوش آمدید، لطفا از پرسیدن سوالاتی از قبیل :"."\n"
                    ."- کی **** بلده"."\n"."- کسی هست"."\n"."- کسی به فلان مبحث تسلط داره"."\n"."خودداری بفرمایید";
                sendMessage($T->chat_id, $text);
            } else {
                $text = " سلام " . $T->new_chat_member->first_name . ' ' . $T->new_chat_member->last_name . " عزیز، گروه یوزرکد خوش آمدید، لطفا از پرسیدن سوالاتی از قبیل : "."\n"
                    ."- کی **** بلده"."\n"."- کسی هست"."\n"."- کسی به فلان مبحث تسلط داره"."\n"."خودداری بفرمایید";
                sendMessage($T->chat_id, $text);
            }
        }
    } else {
        $squery = mysqli_query($db, "SELECT * FROM status WHERE group_id='$T->chat_id'");
        while ($row = mysqli_fetch_assoc($squery)) {
            $turn = $row['turn'];
            if ($turn == 1) {
// متد حذف پیام
                $pquery = mysqli_query($db, "SELECT * FROM forbidden ");
                while ($row = mysqli_fetch_assoc($pquery)) {
                    $filter = $row['filter'];
                    if (preg_match("/\b" . preg_quote($filter, '/') . "\b/u", $T->text)) {
                        deleteMessage($T->chat_id, $T->message_id);
                        $text = "پیام شما به دلیل استفاده از کلمات غیرمجاز حذف شد.";
                        sendMessage($T->chat_id, "@" . $T->user_name . "\n" . $text);

                    }
                }
                $among_query = mysqli_query($db, "SELECT * FROM among ");
                while ($row_among = mysqli_fetch_assoc($among_query)) {

                    $first = $row_among['first'];
                    $second = $row_among['second'];

                    if (preg_match("/" . $first . " (.*) " .$second . "/u", $T->text)) {
                        if (strlen($T->text) < 100) {
                            deleteMessage($T->chat_id, $T->message_id);
                        }
                        $text = "  لطفا از پرسیدن سوالاتی از قبیل : " . "\n"
                            . "- کی **** بلده" . "\n" . "- کسی هست" . "\n" . "- کسی به فلان مبحث تسلط داره" . "\n" . "خودداری بفرمایید"."\n Dontasktoask.com";
                        sendMessage($T->chat_id, "@" . $T->user_name . "\n" . $text);

                    }
                }
//متد موت کردن کاربر
                if (preg_match("/(\bmute\b)/u", $T->text) || preg_match("/(\bMute\b)/u", $T->text)) {
                    strtolower($T->text);

                    $me = getChatMember($T->chat_id, $me_id);
                    if (!empty($T->from_id)) {
                        if ($me->status == 'member') {
                            $text = "من مدیر نیستم";
                        } else if ($T->from_id == '1234' || $user_detail->status == "administrator" || $user_detail->status == "creator") {
                            if (isset($T->reply_msg->from->username)) {
                                $muted_user = $T->reply_msg->from->username;
                            } else {
                                $muted_user = $T->reply_msg->from->first_name;
                            }
                            $time = str_replace('mute ', '', $T->text);
                            $text = "کاربر " . $muted_user . " برای " . $time . " ساعت نمیتواند پیامی ارسال کند";
                            settype($time, "integer");
                            $time = time() + $time * 3600;
                            restrictChatMember($T->chat_id, $T->reply_msg->from->id, $time);
                        } else {
                            $text = "Access Denied!!!";
                        }
                        sendMessage($T->chat_id, $text);
                    }
                }
//متد آزاد کردن کاربر
                if (preg_match("/(\bunmute\b)/u", $T->text) || preg_match("/(\bUnmute\b)/u", $T->text)) {
                    strtolower($T->text);

                    $me = getChatMember($T->chat_id, $me_id);
                    if (!empty($T->from_id)) {
                        if ($me->status == 'member') {
                            $text = "من مدیر نیستم";
                        } else if ($T->from_id == '1234' || $user_detail->status == "administrator" || $user_detail->status == "creator") {
                            if (isset($T->reply_msg->from->username)) {
                                $muted_user = $T->reply_msg->from->username;
                            } else {
                                $muted_user = $T->reply_msg->from->first_name;
                            }
                            $text = "کاربر " . $muted_user . " آزاد شد ";
                            unRestrictChatMember($T->chat_id, $T->reply_msg->from->id);
                        } else {
                            $text = "Access Denied!!!";
                        }
                        sendMessage($T->chat_id, $text);
                    }
                }
//متد حذف کاربر
                if ($T->text == '!kick') {

                    $me = getChatMember($T->chat_id, $me_id);
                    if (!empty($T->from_id)) {
                        if ($me->status == 'member') {
                            $text = "من مدیر نیستم!!! ";
                        } else if ($T->from_id == '1234' || $user_detail->status == "administrator" || $user_detail->status == "creator") {
                            kickChatMember($T->chat_id, $T->reply_msg->from->id);
                            $text = "کاربر " . $T->reply_msg->from->id . " از گروه اخراج شد";
                        } else {
                            $text = "Access Denied!!!";
                        }
                        sendMessage($T->chat_id, $text);
                    }
                }
                if(preg_match("/^#/u", $T->text)){
                    $txt = str_replace('#', null, $T->text);
                    $uquery = mysqli_query($db, "SELECT * FROM request WHERE request_req LIKE '$txt'");
                    while ($row = mysqli_fetch_assoc($uquery)) {
                        $dbreq = $row['request_req'];
                        $dbans = $row['request_ans'];
                        //$text = urlencode($dbans);

                        $text = urlencode($dbans);
                        replyMessage($T->chat_id, $dbans, $T->message_id);
                    }
                }
                if ($T->text == 'یوزر خاموش') {


                    if ($T->from_id == '1234' || $user_detail->status == "administrator" || $user_detail->status == "creator") {
                        $text = "❌❌❌";
                        mysqli_query($db, "UPDATE status
						SET
						turn = '0'
						WHERE group_id='$T->chat_id'");
                        sendMessage($T->chat_id, $text);
                    }
                }
            } else if ($turn == 0) {
                if ($T->text == 'یوزر روشن') {
                    if ($T->from_id == '1234' || $user_detail->status == "administrator" || $user_detail->status == "creator") {
                        $text = "✅✅✅";
                        mysqli_query($db, "UPDATE status
						SET
						turn = '1'
						WHERE group_id='$T->chat_id'");
                    }
                }
                sendMessage($T->chat_id, $text);
            }
        }
    }
}

//متد فیلتر کردن
if (preg_match("/(\bیوزر فیلتر کن بین\b)/u", $T->text)) {
    if ($T->from_id == "1234" || $user_detail->status == "administrator" || $user_detail->status == "creator") {
        $req = str_replace('یوزر فیلتر کن بین', '', $T->text);

        $req = str_replace("\n", null, $T->text);
        $convert = explode('!', $req);
        $re = $convert[1];
        $first = trim(str_replace("\n", null, $re));
        $ar = $convert[2];
        $second = trim(str_replace("\n", null, $ar));


        mysqli_query($db, "INSERT INTO among
						(first,second)
						VALUES('$first','$second')");
        $text = " فیلتر شد";
    } else {
        $text = "Access Denied!!!";
    }
    replyMessage($T->chat_id, $text, $T->message_id);
}
if (preg_match("/(\bفیلتر کن یوزر\b)/u", $T->text)) {
    if ($T->from_id == "1234" || $user_detail->status == "administrator" || $user_detail->status == "creator") {
        $req = str_replace('فیلتر کن یوزر', '', $T->text);
        $req = str_replace("\n", null, $T->text);
        $convert = explode('!', $req);
        $re = $convert[1];
        $r = trim(str_replace("\n", null, $re));
        mysqli_query($db, "INSERT INTO forbidden
						(filter)
						VALUES('$r')");
        $text = $r. " فیلتر شد";
    } else {
        $text = "Access Denied!!!";
    }
    replyMessage($T->chat_id, $text, $T->message_id);
}

//متد یادگیری
if (preg_match("/(\bیاد بگیر یوزر\b)/u", $T->text)) {

    if ($T->from_id == "1234" || $user_detail->status == "administrator" || $user_detail->status == "creator") {
        $req = str_replace('یاد بگیر یوزر', '', $T->text);

        $convert = explode('!', $req);
        $re = $convert[1];
        $r = trim(str_replace("\n", null, $re));
        $a = $db->real_escape_string(trim($convert[2]));

        if(mysqli_query($db, "INSERT INTO request (request_req,request_ans) VALUES('$r','$a')")){
            $text = "ثبت شد";

        }else{
            $text = mysqli_error($db);

        }

    } else {
        $text = "Access Denied!!!";
    }

    replyMessage($T->chat_id, $text, $T->message_id);
}