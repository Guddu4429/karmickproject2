<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enquiry from Guardian</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .meta {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #0d6efd;
        }
        .meta p {
            margin: 5px 0;
            font-size: 14px;
        }
        .meta strong {
            color: #495057;
        }
        .message {
            background: white;
            padding: 20px;
            border-radius: 6px;
            white-space: pre-wrap;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DPS Ruby Park</h1>
        <p style="margin: 5px 0 0 0; opacity: 0.9;">New Enquiry Received</p>
    </div>
    
    <div class="content">
        <div class="meta" style="margin-bottom: 15px;">
            <p style="margin: 5px 0; font-size: 14px;"><strong>From (Guardian):</strong> {{ $emailFrom }}</p>
            <p style="margin: 5px 0; font-size: 14px;"><strong>Date:</strong> {{ now()->format('d M Y, g:i A') }}</p>
        </div>

        <div style="background: #e7f1ff; padding: 15px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #0d6efd;">
            <h4 style="margin: 0 0 10px 0; color: #0d6efd; font-size: 14px;">Student Information</h4>
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="padding: 3px 0;"><strong>Name:</strong></td>
                    <td style="padding: 3px 0;">{{ $studentName }}</td>
                </tr>
                @if($studentClass)
                <tr>
                    <td style="padding: 3px 0;"><strong>Class:</strong></td>
                    <td style="padding: 3px 0;">{{ $studentClass }}{{ $studentStream ? ' (' . $studentStream . ')' : '' }}</td>
                </tr>
                @endif
                @if($studentRollNo)
                <tr>
                    <td style="padding: 3px 0;"><strong>Roll No:</strong></td>
                    <td style="padding: 3px 0;">{{ $studentRollNo }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="meta">
            <p><strong>Subject:</strong> {{ $enquirySubject }}</p>
        </div>
        
        <h3 style="margin-top: 15px; color: #495057;">Message:</h3>
        <div class="message">{{ $messageText }}</div>
    </div>
    
    <div class="footer">
        <p>This email was sent from the DPS Ruby Park Student Portal.</p>
        <p>Please reply directly to the sender at {{ $emailFrom }}</p>
    </div>
</body>
</html>
