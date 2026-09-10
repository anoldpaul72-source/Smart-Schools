<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Marks Template | Smart-Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 20px;
            color: #333333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        h2 {
            color: #ea580c;
            margin-top: 0;
            text-align: center;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
            background: #e9ecef;
            padding: 10px 14px;
            border-radius: 4px;
            align-items: center;
        }

        .nav-links a {
            font-weight: bold;
            text-decoration: none;
            color: #0056b3;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 14px;
            color: #334155;
        }

        input, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
            background-color: white;
            font-family: inherit;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #ea580c;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 25px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background 0.2s;
        }

        button:hover {
            background-color: #c2410c;
        }

        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .info-box {
            background: #fffbeb;
            border: 1px dashed #f59e0b;
            padding: 12px;
            border-radius: 4px;
            color: #b45309;
            font-size: 13px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📤 {{ __('Upload Completed Template') }}</h2>

    <div class="nav-links">
        <a href="{{ route('teacher.marks') }}">⬅ {{ __('Marks Entry') }}</a>
        <a href="{{ route('home') }}">{{ __('Home') }}</a>

        <!-- Language Switcher -->
        <div style="display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 2px 4px; gap: 4px;">
            <a href="{{ route('lang.switch', 'en') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'en' ? '#ea580c' : '#64748b' }}; background: {{ app()->getLocale() == 'en' ? '#ffedd5' : 'transparent' }};">🇬🇧 EN</a>
            <a href="{{ route('lang.switch', 'sw') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'sw' ? '#ea580c' : '#64748b' }}; background: {{ app()->getLocale() == 'sw' ? '#ffedd5' : 'transparent' }};">🇹🇿 SW</a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(!$isPrivileged && $subjects->isEmpty())
        <div class="alert alert-danger" style="text-align: left; background: #fff1f2; border: 1px solid #fda4af; color: #9f1239;">
            ⚠️ <b>Hujapangiwa somo lolote la kufundisha!</b><br>
            Hauruhusiwi kupakia matokeo mpaka Mkuu wa Shule au Admin akupangie somo.
        </div>
    @endif

    <form method="POST" action="{{ route('teacher.marks.upload_csv') }}" enctype="multipart/form-data">
        @csrf

        <label for="subject_id">Select Subject:</label>
        <select name="subject_id" id="subject_id" required {{ (!$isPrivileged && $subjects->isEmpty()) ? 'disabled' : '' }}>
            <option value="">-- Select Subject --</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
            @endforeach
        </select>

        <label for="class_name">Select Class:</label>
        <select name="class_name" id="class_name" required>
            <option value="">-- Select Class --</option>
            @foreach($classes as $cls)
                <option value="{{ $cls }}">{{ $cls }}</option>
            @endforeach
        </select>

        <label for="term">Exam Assessment Type:</label>
        <select name="term" id="term" required>
            <option value="">-- Select Assessment Type --</option>
            <option value="Weekly Test">Weekly Test</option>
            <option value="Monthly Test">Monthly Test</option>
            <option value="Midterm Test">Midterm Test</option>
            <option value="Terminal Examination">Terminal Examination</option>
            <option value="Annual Examination">Annual Examination</option>
        </select>

        <label for="csv_file">Upload Completed CSV Spreadsheet:</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv, .txt" required style="padding: 8px;">

        <button type="submit">Import Marks Data</button>
    </form>

    <div class="info-box">
        💡 <b>Instructions:</b> Please make sure to download the pre-populated CSV template first from the main Marks Entry page, enter the student scores in the <code>Score</code> column, save as CSV, and upload it here.
    </div>
</div>

</body>
</html>
