<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        p {
            font-size: 16px;
            color: #666;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        footer {
            margin-top: 20px;
            font-size: 12px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your OTP Code</h1>
        <p>Hello {{ $user->name }},</p>
        <p>Your One-Time Password (OTP) for verification is:</p>
        <p class="otp">{{ $otp }}</p>
        <p>This OTP is valid for 5 minutes. Please do not share it with anyone.</p>
        <footer>
            <p>Thank you for using our application!</p>
        </footer>
    </div>
</body>
</html>
