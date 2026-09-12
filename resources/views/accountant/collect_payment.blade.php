<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance - Record Student Payment | Smart-Results</title>
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
            color: #0d9488;
            text-align: center;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0;
            margin-bottom: 20px;
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
            color: #0d9488;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 14px;
            color: #475569;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
            background-color: white;
            font-family: inherit;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #0d9488;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #0d9488;
            color: white;
            border: none;
            font-size: 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 25px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background 0.2s;
        }

        button:hover {
            background-color: #0f766e;
        }

        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>

@include('layouts.sidebar')

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">
        <button type="button" class="sidebar-toggle-btn" onclick="toggleSidebar()" title="{{ __('Toggle Sidebar') }}">
            ☰ {{ __('Menu') }}
        </button>
        <div style="font-size: 13px; color: #64748b; font-weight: 700;">Smart-Schools &bull; {{ __('Finance Desk') }}</div>
    </div>

    <h2>💰 {{ __('Collect Payment') }}</h2>

    <div class="nav-links">
        <span>{{ __('Campus') }}: <b>{{ $schoolName }}</b></span>
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('accountant.fees') }}" style="font-weight: bold;">
                📊 {{ __('Fee Ledger') }}
            </a>
            <a href="{{ route('home') }}">{{ __('Home') }}</a>

            <!-- Language Switcher -->
            <div style="display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 2px 4px; gap: 4px;">
                <a href="{{ route('lang.switch', 'en') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'en' ? '#0d9488' : '#64748b' }}; background: {{ app()->getLocale() == 'en' ? '#ccfbf1' : 'transparent' }};">🇬🇧 EN</a>
                <a href="{{ route('lang.switch', 'sw') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'sw' ? '#0d9488' : '#64748b' }}; background: {{ app()->getLocale() == 'sw' ? '#ccfbf1' : 'transparent' }};">🇹🇿 SW</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Class Selector Filter -->
    <form method="GET" action="{{ route('accountant.collect_payment') }}" id="classFilterForm">
        <label for="class_name">Select Student Class:</label>
        <select name="class_name" id="class_name" onchange="this.form.submit()" required>
            <option value="">-- Choose Class --</option>
            @foreach($classes as $c)
                <option value="{{ $c }}" {{ $selectedClass === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </form>

    <!-- Payment Submission Form -->
    <form method="POST" action="{{ route('accountant.payment.store') }}">
        @csrf

        <label for="student_id">Select Student Name:</label>
        <select name="student_id" id="student_id" required {{ $students->isEmpty() ? 'disabled' : '' }}>
            @if($students->isEmpty())
                <option value="">-- {{ $selectedClass ? 'No students enrolled in ' . $selectedClass : 'Choose class above first' }} --</option>
            @else
                <option value="">-- Select Student --</option>
                @foreach($students as $stud)
                    <option value="{{ $stud->id }}">{{ $stud->student_name }} ({{ $stud->reg_number }})</option>
                @endforeach
            @endif
        </select>

        <label for="amount_paid">Amount Paid (TZS):</label>
        <input type="number" step="any" min="1" name="amount_paid" id="amount_paid" placeholder="e.g. 250000" required>

        <label for="receipt_no">Receipt / Voucher Number:</label>
        <input type="text" name="receipt_no" id="receipt_no" placeholder="e.g. RCP-2026-0042" required>

        <label for="payment_date">Payment Date:</label>
        <input type="date" name="payment_date" id="payment_date" value="{{ date('Y-m-d') }}" required>

        <button type="submit">Submit Payment Record</button>
    </form>
</div>

</body>
</html>
