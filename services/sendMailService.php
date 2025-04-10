<?php
// File: services/sendMailService.php

require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Phần code gửi mail
function sendVerificationCode($toEmail, $verificationCode) {
    $mail = new PHPMailer(true);
    try {
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shopfstyle89@gmail.com';
        $mail->Password   = 'yvdembjxuhpxpuzz';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        
        // Cài đặt người gửi và người nhận
        $mail->setFrom('shopfstyle89@gmail.com', 'FStyle Support');
        $mail->addAddress($toEmail);
        
        // Cấu hình email HTML
        $mail->isHTML(true);
        $mail->Subject = 'Xác nhận email của bạn';
        $mail->Body = '
            <html>
            <head>
            <style>
                body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
                }
                .email-wrapper {
                max-width: 600px;
                margin: 30px auto;
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                padding: 30px;
                }
                .header {
                text-align: center;
                border-bottom: 1px solid #eee;
                padding-bottom: 15px;
                }
                .logo {
                font-size: 26px;
                color: #007bff;
                font-weight: bold;
                }
                .content {
                padding: 20px 0;
                }
                .content p {
                font-size: 16px;
                color: #333;
                line-height: 1.6;
                }
                .code-box {
                background-color: #f0f8ff;
                border: 2px dashed #007bff;
                border-radius: 5px;
                text-align: center;
                font-size: 30px;
                font-weight: bold;
                color: #007bff;
                margin: 20px auto;
                padding: 15px 0;
                width: 60%;
                }
                .footer {
                font-size: 13px;
                text-align: center;
                color: #999;
                margin-top: 30px;
                border-top: 1px solid #eee;
                padding-top: 15px;
                }
            </style>
            </head>
            <body>
            <div class="email-wrapper">
                <div class="header">
                <div class="logo">Shop FStyle</div>
                </div>
                <div class="content">
                <p>Chào bạn,</p>
                <p>Chúng tôi đã nhận được yêu cầu xác nhận tài khoản của bạn. Mã xác nhận của bạn là:</p>
                <div class="code-box">' . $verificationCode . '</div>
                <p>Vui lòng nhập mã này để hoàn tất xác minh tài khoản của bạn.</p>
                </div>
                <div class="footer">
                Nếu bạn không yêu cầu đăng ký tài khoản, vui lòng bỏ qua email này.<br>
                © ' . date('Y') . ' FStyle Shop. All rights reserved.
                </div>
            </div>
            </body>
            </html>
            ';

        $mail->AltBody = 'Mã xác nhận của bạn là: ' . $verificationCode;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Lỗi gửi mail: {$mail->ErrorInfo}");
        return false;
    }
}