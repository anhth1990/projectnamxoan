<?php

    //var_dump(extension_loaded('openssl'));
    //die();
/**
 * 
 * This example shows making an SMTP connection with authentication.
 */

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
//date_default_timezone_set('Etc/UTC');

require 'PHPMailerAutoload.php';
function guiMail($sendTo,$title,$content){
    //Create a new PHPMailer instance
    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    //Ask for HTML-friendly debug output
    $mail->Debugoutput = 'html';
    //Set the hostname of the mail server
    $mail->Host = "162.243.228.247";
    //Set the SMTP port number - likely to be 25, 465 or 587
    $mail->Port = 25;
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //Username to use for SMTP authentication
    $mail->Username = "safesai";
    //Password to use for SMTP authentication
    $mail->Password = "yYXX8cgcgydwYQdV";
    $mail->SMTPSecure = false;
    $mail->AuthType="PLAIN";
    //Set who the message is to be sent from
    $mail->setFrom('no-reply@safesai.com', 'no-reply');
    //Set an alternative reply-to address
    $mail->addReplyTo('no-reply@safesai.com', 'no-reply');
    //Set who the message is to be sent to
    $mail->addAddress($sendTo, "KH");
    //Set the subject line
    $mail->Subject = $title;
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail->msgHTML($content);
    //Replace the plain text body with one created manually
    $mail->AltBody = 'vsec@2017';
    //Attach an image file
    //$mail->addAttachment('images/phpmailer_mini.png');

    //send the message, check for errors
    return $mail->send();
}
