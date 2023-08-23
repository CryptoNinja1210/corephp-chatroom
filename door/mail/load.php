<?php if(!defined('s7V9pz')) {die();}?><?php

if (!function_exists('post')) {
    function post($m, $f, $t, $r = 0, $l = 0) {
    sfn("PHPMailer.php", "mail");
    sfn("SMTP.php", "mail");
    sfn("Exception.php", "mail");
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    if (isset($l['host'])) {
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = $l['host'];
        $mail->Username = $l['user'];
        $mail->Password = $l['pass'];
        $mail->SMTPSecure = $l['protocol'];
        $mail->Port = $l['port'];
    } else {
        $mail->SMTPAuth = false;
        $mail->SMTPSecure = false;
    }
    if ($r == 0) {
        $r = $f;
    }
    if (isset($l['peer'])) {
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
    }
    $mail->IsHTML(true);
    $mail->CharSet = "UTF-8";
    $mail->SetFrom($f['email'], $f['name']);
    $mail->Subject = $m['subject'];
    $mail->Body = $m['content'];
    $mail->AddAddress($t['email'], $t['name']);
    $mail->AddReplyTo($r['email'], $r['name']);
    if (isset($l['debug'])) {
        $mail->SMTPDebug = $l['debug'];
    }
    if (!$mail->Send()) {
        if (isset($l['debug'])) {
            return 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            return false;
        }
    } else {
        return true;
    }

}
}


?>