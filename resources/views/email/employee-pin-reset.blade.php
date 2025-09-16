<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIN Reset Notification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8f9fa;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            padding: 30px 20px;
            text-align: center;
            color: black;
        }
        
        .email-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .email-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        
        .message-content {
            font-size: 16px;
            line-height: 1.8;
            color: #555555;
            margin-bottom: 30px;
        }
        
        .pin-container {
            background: #f8f9fa;
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        
        .pin-label {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .pin-code {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            font-family: 'Courier New', monospace;
            letter-spacing: 4px;
            margin: 10px 0;
        }
        
        .pin-note {
            font-size: 12px;
            color: #6c757d;
            margin-top: 10px;
            font-style: italic;
        }
        
        .info-box {
            background: #e8f4fd;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 6px 6px 0;
        }
        
        .info-box h3 {
            color: #007bff;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .info-box ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .info-box li {
            margin: 8px 0;
            color: #555555;
        }
        
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .warning-box .warning-icon {
            color: #856404;
            font-size: 18px;
            margin-right: 8px;
        }
        
        .warning-box p {
            color: #856404;
            font-size: 14px;
            margin: 0;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            background: #f8f9fa;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .details-table th,
        .details-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .details-table th {
            background: #e9ecef;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }
        
        .details-table td {
            color: #6c757d;
            font-size: 14px;
        }
        
        .contact-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 6px;
            margin: 30px 0;
            text-align: center;
        }
        
        .contact-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .contact-info {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .contact-item {
            text-align: center;
        }
        
        .contact-item .icon {
            font-size: 20px;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .contact-item p {
            font-size: 14px;
            color: #6c757d;
            margin: 0;
        }
        
        .contact-item a {
            color: #007bff;
            text-decoration: none;
        }
        
        .contact-item a:hover {
            text-decoration: underline;
        }
        
        .email-footer {
            background: #2c3e50;
            color: #bdc3c7;
            padding: 25px;
            text-align: center;
            font-size: 12px;
        }
        
        .email-footer p {
            margin: 5px 0;
        }
        
        .email-footer .company-name {
            color: #ffffff;
            font-weight: 600;
        }
        
        @media (max-width: 600px) {
            .email-body {
                padding: 20px 15px;
            }
            
            .pin-code {
                font-size: 24px;
                letter-spacing: 2px;
            }
            
            .contact-info {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>🔒 PIN Reset Notification</h1>
            <p>Your clock-in PIN has been successfully reset</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                Hello {{ $data['employee_name'] }},
            </div>
            
            <div class="message-content">
                <p>Your clock-in PIN has been successfully reset. Please find your new PIN details below:</p>
            </div>
            
            <!-- PIN Display -->
            <div class="pin-container">
                <div class="pin-label">Your New Clock-In PIN</div>
                <div class="pin-code">{{ $data['new_pin'] }}</div>
                <div class="pin-note">Keep this PIN confidential and secure</div>
            </div>
            
            <!-- Reset Details -->
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Detail</th>
                        <th>Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Employee Name</strong></td>
                        <td>{{ $data['employee_name'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Employee ID</strong></td>
                        <td>{{ $data['employee_id'] }}</td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Important Information -->
            <div class="info-box">
                <h3>📋 Important Information</h3>
                <ul>
                    <li>Use this PIN for clocking in and out of your shifts</li>
                    <li>Your PIN is case-sensitive and must be entered exactly as shown</li>
                    <li>Do not share your PIN with anyone for security reasons</li>
                    <li>If you forget your PIN, you can request another reset</li>
                    <li>The PIN will remain active until you change it or request another reset</li>
                </ul>
            </div>
            
            <!-- Security Warning -->
            <div class="warning-box">
                <p>
                    <span class="warning-icon">⚠️</span>
                    <strong>Security Notice:</strong> If you did not request this PIN reset, please contact HR immediately. Keep your PIN confidential and never share it with colleagues or supervisors.
                </p>
            </div>
            
            <div class="message-content">
                <p>Thank you for using our employee management system. If you have any questions or concerns, please don't hesitate to reach out to our support team.</p>
                
                <p style="margin-top: 20px;">Best regards,<br>
                <strong>{{ $data['company_name'] }} HR Team</strong></p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p>
                <span class="company-name">{{ $data['company_name'] }}</span><br>
                This is an automated message. Please do not reply to this email.
            </p>
            <p>© {{ date('Y') }} {{ $data['company_name'] }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>