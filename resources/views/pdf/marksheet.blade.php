<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Marksheet - {{ $result->exam_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            color: #0066cc;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 18px;
            color: #333;
            font-weight: normal;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            color: #555;
        }
        .info-value {
            display: table-cell;
            width: 70%;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .marks-table th {
            background-color: #0066cc;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .marks-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        .marks-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .summary-label {
            display: table-cell;
            width: 50%;
            font-weight: bold;
            font-size: 13px;
        }
        .summary-value {
            display: table-cell;
            width: 50%;
            font-size: 13px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .grade-box {
            display: inline-block;
            padding: 5px 15px;
            background-color: #0066cc;
            color: white;
            font-weight: bold;
            border-radius: 3px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DPS Ruby Park</h1>
        <h2>MARKSHEET</h2>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Student Name:</div>
            <div class="info-value">{{ $result->first_name }} {{ $result->last_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Admission No:</div>
            <div class="info-value">{{ $result->admission_no }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Roll No:</div>
            <div class="info-value">{{ $result->roll_no ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Class:</div>
            <div class="info-value">{{ $result->class_name ?? 'N/A' }} @if($result->stream_name) ({{ $result->stream_name }}) @endif</div>
        </div>
        <div class="info-row">
            <div class="info-label">Exam:</div>
            <div class="info-value">{{ $result->exam_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Academic Year:</div>
            <div class="info-value">{{ $result->academic_year }}</div>
        </div>
    </div>

    <table class="marks-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Marks Obtained</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjectMarks as $mark)
                <tr>
                    <td>{{ $mark->subject_name }}</td>
                    <td>{{ $mark->marks_obtained }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center; padding: 20px;">No marks recorded</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-section">
        <div class="summary-row">
            <div class="summary-label">Total Marks:</div>
            <div class="summary-value">{{ $result->total_marks ?? 'N/A' }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Percentage:</div>
            <div class="summary-value">{{ $result->percentage ?? 'N/A' }}%</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Grade:</div>
            <div class="summary-value">
                @if($result->grade)
                    <span class="grade-box">{{ $result->grade }}</span>
                @else
                    N/A
                @endif
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated marksheet. Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>
</body>
</html>
