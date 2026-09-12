<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Book Borrowings & Returns') }} | Smart-Results</title>
    <style>
        :root {
            --primary: #0284c7;
            --primary-hover: #0369a1;
            --primary-light: #e0f2fe;
            --secondary: #0f172a;
            --border: #cbd5e1;
            --bg-page: #f8fafc;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg-page);
            margin: 20px;
            color: #333333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 26px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        h2 {
            color: var(--primary);
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            text-transform: uppercase;
            text-align: center;
            font-size: 21px;
            letter-spacing: 0.5px;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .nav-links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
            background: #f1f5f9;
            padding: 10px 16px;
            border-radius: 8px;
            align-items: center;
            border: 1px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 10px;
        }

        .nav-links a {
            font-weight: bold;
            text-decoration: none;
            color: var(--primary);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 18px;
            font-size: 13px;
            font-weight: bold;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            padding: 11px 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-borrowed { background: #fef3c7; color: #b45309; }
        .badge-returned { background: #dcfce7; color: #166534; }
        .badge-overdue { background: #fee2e2; color: #991b1b; }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }

        .btn-return {
            background: #16a34a;
            color: white;
        }

        .btn-return:hover {
            background: #15803d;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .modal-content {
            background: white;
            border-radius: 10px;
            width: 100%;
            max-width: 480px;
            padding: 24px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);
        }

        label {
            display: block;
            margin-top: 12px;
            margin-bottom: 5px;
            font-size: 13px;
            font-weight: bold;
            color: #475569;
        }

        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 13px;
        }

        .btn {
            display: inline-block;
            width: 100%;
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
            margin-top: 15px;
            text-align: center;
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
        <div style="font-size: 13px; color: #64748b; font-weight: 700;">Smart-Schools &bull; {{ __('Library Portal') }}</div>
    </div>

    <h2>🔄 {{ __('Book Borrowings & Returns Ledger') }}</h2>

    <div class="nav-links no-print">
        <span>{{ __('Campus') }}: <b>{{ $schoolName }}</b></span>
        <div style="display: flex; align-items: center; gap: 14px;">
            <a href="{{ route('librarian.dashboard') }}" style="font-weight: bold; color: var(--primary);">
                📚 {{ __('Book Catalog') }}
            </a>
            <a href="{{ route('home') }}">{{ __('Home') }}</a>

            <!-- Language Switcher -->
            <div style="display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 2px 4px; gap: 4px;">
                <a href="{{ route('lang.switch', 'en') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'en' ? '#0284c7' : '#64748b' }}; background: {{ app()->getLocale() == 'en' ? '#e0f2fe' : 'transparent' }};">🇬🇧 EN</a>
                <a href="{{ route('lang.switch', 'sw') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'sw' ? '#0284c7' : '#64748b' }}; background: {{ app()->getLocale() == 'sw' ? '#e0f2fe' : 'transparent' }};">🇹🇿 SW</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Filter Bar -->
    <div class="filter-bar no-print">
        <form method="GET" action="{{ route('librarian.borrowings') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <select name="status" onchange="this.form.submit()" style="width: auto; padding: 7px 12px; font-size: 13px;">
                <option value="">-- {{ __('All Statuses') }} --</option>
                <option value="Borrowed" {{ request('status') === 'Borrowed' ? 'selected' : '' }}>🟡 {{ __('Active Borrowings') }}</option>
                <option value="Overdue" {{ request('status') === 'Overdue' ? 'selected' : '' }}>🔴 {{ __('Overdue Only') }}</option>
                <option value="Returned" {{ request('status') === 'Returned' ? 'selected' : '' }}>🟢 {{ __('Returned Records') }}</option>
            </select>

            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search student name, reg #, or book...') }}" style="width: 250px; padding: 7px 12px; font-size: 13px;">
            <button type="submit" style="background: var(--primary); color: white; border: none; padding: 7px 14px; border-radius: 6px; font-size: 13px; font-weight: bold; cursor: pointer;">🔍 {{ __('Filter') }}</button>
            @if(request('status') || request('search'))
                <a href="{{ route('librarian.borrowings') }}" style="padding: 7px 12px; background: #e2e8f0; color: #475569; border-radius: 6px; text-decoration: none; font-size: 13px;">✕ {{ __('Clear') }}</a>
            @endif
        </form>

        <button type="button" onclick="openIssueModal()" style="background: var(--primary); color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 13px; cursor: pointer;">
            ➕ {{ __('Issue New Book') }}
        </button>
    </div>

    <!-- Borrowings Table -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        @if($borrowings->count() > 0)
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Student Name & Class') }}</th>
                            <th>{{ __('Book Title') }}</th>
                            <th>{{ __('Borrow Date') }}</th>
                            <th>{{ __('Due Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Return Date') }}</th>
                            <th style="text-align: right;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrowings as $index => $b)
                            @php
                                $isOverdue = $b->status === 'Borrowed' && $b->due_date && $b->due_date->lt(now()->startOfDay());
                            @endphp
                            <tr style="{{ $isOverdue ? 'background-color: #fff1f2;' : '' }}">
                                <td>{{ $borrowings->firstItem() + $index }}</td>
                                <td>
                                    <b>{{ $b->student ? $b->student->student_name : '-' }}</b>
                                    @if($b->student)
                                        <div style="font-size: 11px; color: #64748b;">{{ $b->student->class_name }} &bull; {{ $b->student->reg_number }}</div>
                                    @endif
                                </td>
                                <td>
                                    <b>{{ $b->book ? $b->book->title : '-' }}</b>
                                    @if($b->book && $b->book->shelf_location)
                                        <div style="font-size: 11px; color: #64748b;">{{ __('Shelf') }}: {{ $b->book->shelf_location }}</div>
                                    @endif
                                </td>
                                <td>{{ $b->borrowed_date ? $b->borrowed_date->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <b style="{{ $isOverdue ? 'color: #dc2626;' : '' }}">
                                        {{ $b->due_date ? $b->due_date->format('d/m/Y') : '-' }}
                                    </b>
                                </td>
                                <td>
                                    @if($b->status === 'Returned')
                                        <span class="badge badge-returned">✅ {{ __('Returned') }}</span>
                                    @elseif($isOverdue)
                                        <span class="badge badge-overdue">⚠️ {{ __('Overdue') }}</span>
                                    @else
                                        <span class="badge badge-borrowed">⏳ {{ __('Borrowed') }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $b->returned_date ? $b->returned_date->format('d/m/Y') : '-' }}
                                </td>
                                <td style="text-align: right;">
                                    @if($b->status === 'Borrowed')
                                        <form method="POST" action="{{ route('librarian.borrowings.return', $b->id) }}" onsubmit="return confirm('{{ __('Mark this book as returned by student?') }}')" style="display: inline; margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn-action btn-return">
                                                📥 {{ __('Mark Returned') }}
                                            </button>
                                        </form>
                                    @else
                                        <span style="font-size: 12px; color: #16a34a; font-weight: bold;">✔️ {{ __('Completed') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 15px;">
                {{ $borrowings->withQueryString()->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #64748b;">
                💡 {{ __('No book borrowing records found matching your filters.') }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Issue Book to Student -->
<div id="issueModal" class="modal">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 15px;">
            <h3 style="margin: 0; font-size: 16px; color: var(--primary);">📤 {{ __('Issue Book to Student') }}</h3>
            <button type="button" onclick="closeIssueModal()" style="background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
        </div>

        <form method="POST" action="{{ route('librarian.borrowings.issue') }}">
            @csrf
            <label for="book_id">{{ __('Select Book to Issue') }} *:</label>
            <select name="book_id" id="book_id" required>
                <option value="">-- {{ __('Choose Book') }} --</option>
                @foreach($availableBooks as $bk)
                    <option value="{{ $bk->id }}">{{ $bk->title }} ({{ __('Available') }}: {{ $bk->available_copies }})</option>
                @endforeach
            </select>

            <label for="borrow_class_filter">{{ __('Select Class') }}:</label>
            <select id="borrow_class_filter" onchange="filterBorrowStudentsByClass(this.value)">
                <option value="">-- {{ __('All Classes') }} --</option>
                @foreach($classes as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </select>

            <label for="student_id">{{ __('Select Student') }} *:</label>
            <select name="student_id" id="student_id" required>
                <option value="">-- {{ __('Choose Student') }} --</option>
                @foreach($students as $st)
                    <option value="{{ $st->id }}" data-class="{{ $st->class_name }}">{{ $st->student_name }} - {{ $st->class_name }} ({{ $st->reg_number }})</option>
                @endforeach
            </select>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <label for="borrowed_date">{{ __('Borrow Date') }} *:</label>
                    <input type="date" name="borrowed_date" id="borrowed_date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label for="due_date">{{ __('Due Date for Return') }} *:</label>
                    <input type="date" name="due_date" id="due_date" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required>
                </div>
            </div>

            <label for="remarks">{{ __('Remarks / Notes (Optional)') }}:</label>
            <input type="text" name="remarks" id="remarks" placeholder="{{ __('e.g. Good condition') }}">

            <button type="submit" class="btn">📤 {{ __('Confirm Book Issuance') }}</button>
        </form>
    </div>
</div>

<script>
    function openIssueModal() {
        document.getElementById('issueModal').style.display = 'flex';
    }

    function closeIssueModal() {
        document.getElementById('issueModal').style.display = 'none';
    }

    function filterBorrowStudentsByClass(selectedClass) {
        const studentSelect = document.getElementById('student_id');
        const options = studentSelect.querySelectorAll('option');

        studentSelect.value = '';
        options.forEach(opt => {
            if (!opt.value) {
                opt.style.display = '';
                return;
            }
            const studentClass = opt.getAttribute('data-class');
            if (!selectedClass || studentClass === selectedClass) {
                opt.style.display = '';
                opt.disabled = false;
            } else {
                opt.style.display = 'none';
                opt.disabled = true;
            }
        });
    }

    window.onclick = function(e) {
        if (e.target === document.getElementById('issueModal')) closeIssueModal();
    };
</script>

</body>
</html>
