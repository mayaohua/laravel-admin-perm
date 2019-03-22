<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>邮箱验证验证</title>
</head>
<body>

<div style="background:#fff;border:1px solid #ccc;margin:2%;padding:0 30px;">
    <div style="line-height:40px;height:40px">&nbsp;</div>
    <p style="margin:0;padding:0;font-size:14px;line-height:30px;color:#333;font-family:arial,sans-serif;font-weight:bold">亲爱的{{$name}}：</p>
    <div style="line-height:20px;height:20px">&nbsp;</div>
    <p style="margin:0;padding:0;line-height:30px;font-size:14px;color:#333;font-family:'宋体',arial,sans-serif">您好！您正在进行账号邮箱身份验证，本次请求的验证码为：</p>
    <p style="margin:0;padding:0;line-height:30px;font-size:14px;color:#333;font-family:'宋体',arial,sans-serif"><b style="font-size:18px;color:#f90">{{$code}}</b><span style="margin:0;padding:0;margin-left:10px;line-height:30px;font-size:14px;color:#979797;font-family:'宋体',arial,sans-serif">(为了保障您帐号的安全性，请在{{$time}}内完成验证。)</span></p>
    <div style="line-height:80px;height:80px">&nbsp;</div>
    <p style="margin:0;padding:0;line-height:30px;font-size:14px;color:#333;font-family:'宋体',arial,sans-serif">513学院团队</p>
    <p style="margin:0;padding:0;line-height:30px;font-size:14px;color:#333;font-family:'宋体',arial,sans-serif">{{$date}}</p>
    <p style="margin:0;padding:0;line-height:30px;font-size:14px;color:#333;font-family:'宋体',arial,sans-serif">本邮件由系统自动发出，请勿回复。</p>
    <div style="line-height:20px;height:20px">&nbsp;</div>
    <div style="border-top:1px dashed #dfdfdf;padding:30px 0;overflow:hidden;">
        <div style="float:left;width:110px">
            <img src="" style="border:1px solid #dfdfdf;padding:5px;width: 100px;height: 100px;">
        </div>
        <div style="overflow:hidden">
            <p style="text-indent:2em;color:#666;font-size:14px">关注<a href="" style="font-size:16px;color:#36c;text-decoration:none;font-weight:bold" target="_blank"> 513学院 </a>微信公众号，收取验证码不再等待！</p>
            <p style="text-indent:2em;color:#666;font-size:14px">赶紧扫描关注吧</p>
        </div>
    </div>
</div>
</body>
</html>
