<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to Website</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 40px;">
<div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
    <h2 style="color: #333;">Hi {{ $user->name }},</h2>

    <p style="font-size: 16px; color: #555;">
        Welcome to <strong>Website</strong> – your new favorite destination for discovering amazing products at great prices!
    </p>

    <p style="font-size: 16px; color: #555;">
        We're excited to have you on board. As a registered user, you can now:
    </p>

    <ul style="font-size: 16px; color: #555;">
        <li>📦 Shop from thousands of products</li>
        <li>💳 Track your orders and manage returns</li>
        <li>🎁 Get access to exclusive deals and offers</li>
    </ul>

    <p style="font-size: 16px; color: #555;">
        Ready to get started? Click the button below to explore the latest collections:
    </p>

    <p style="text-align: center;">
        <a href="{{ url('/') }}" style="background-color: #28a745; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none; display: inline-block; font-size: 16px;">
            Start Shopping
        </a>
    </p>

    <p style="font-size: 16px; color: #555;">
        If you have any questions or need help, we're just an email away.
    </p>

    <p style="font-size: 16px; color: #555;">
        Happy shopping!<br>
        – The Website Team
    </p>
</div>
</body>
</html>
