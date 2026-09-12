<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Library Management & Book Catalog') }} | Smart-Results</title>
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

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            border-left: 4px solid var(--primary);
        }

        .kpi-card.green { border-left-color: #16a34a; }
        .kpi-card.amber { border-left-color: #d97706; }
        .kpi-card.red { border-left-color: #ef4444; }

        .kpi-title {
            font-size: 12px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .kpi-value {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
        }

        .panel-grid {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 25px;
            align-items: flex-start;
        }

        @media (max-width: 900px) {
            .panel-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 16px;
            color: var(--secondary);
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
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
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 13px;
            background-color: #fff;
            transition: border-color 0.2s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
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
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn:hover {
            background-color: var(--primary-hover);
        }

        .btn-outline {
            background: white;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary-light);
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

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            padding: 10px 12px;
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
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-avail { background: #dcfce7; color: #166534; }
        .badge-out { background: #fee2e2; color: #991b1b; }
        .badge-cat { background: #e0f2fe; color: #0369a1; }

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
            max-width: 500px;
            padding: 24px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);
            max-height: 90vh;
            overflow-y: auto;
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

    <h2>📚 {{ __('Library Management & Book Catalog') }}</h2>

    <div class="nav-links no-print">
        <span>{{ __('Campus') }}: <b>{{ $schoolName }}</b></span>
        <div style="display: flex; align-items: center; gap: 14px;">
            <a href="{{ route('librarian.borrowings') }}" style="font-weight: bold; color: var(--primary);">
                🔄 {{ __('View Book Borrowings') }}
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

    <!-- KPI Summary Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-title">📚 {{ __('Total Book Titles') }}</div>
            <div class="kpi-value">{{ number_format($totalTitles) }}</div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-title">📖 {{ __('Total Copies in Stock') }}</div>
            <div class="kpi-value" style="color: #16a34a;">{{ number_format($totalCopies) }}</div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-title">✅ {{ __('Available on Shelf') }}</div>
            <div class="kpi-value" style="color: #0284c7;">{{ number_format($availableCopies) }}</div>
        </div>
        <div class="kpi-card amber">
            <div class="kpi-title">🔄 {{ __('Currently Borrowed') }}</div>
            <div class="kpi-value" style="color: #d97706;">{{ number_format($activeBorrowed) }}</div>
        </div>
        @if($overdueCount > 0)
        <div class="kpi-card red">
            <div class="kpi-title">⚠️ {{ __('Overdue Returns') }}</div>
            <div class="kpi-value" style="color: #ef4444;">{{ number_format($overdueCount) }}</div>
        </div>
        @endif
    </div>

    <div class="panel-grid">
        <!-- LEFT PANEL: Add New Book Form -->
        <div class="card no-print">
            <h3>➕ {{ __('Catalog New Book') }}</h3>
            <form method="POST" action="{{ route('librarian.books.store') }}">
                @csrf
                <label for="title">{{ __('Book Title') }} *:</label>
                <input type="text" name="title" id="title" placeholder="{{ __('e.g. Pure Mathematics 1') }}" required>

                <label for="author">{{ __('Author / Writer') }}:</label>
                <input type="text" name="author" id="author" placeholder="{{ __('e.g. Backhouse & Houldsworth') }}">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label for="category">{{ __('Subject / Category') }}:</label>
                        <input type="text" name="category" id="category" placeholder="{{ __('e.g. Mathematics') }}">
                    </div>
                    <div>
                        <label for="isbn">{{ __('ISBN / Book Code') }}:</label>
                        <input type="text" name="isbn" id="isbn" placeholder="978-0199142644">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label for="total_copies">{{ __('Total Copies') }} *:</label>
                        <input type="number" name="total_copies" id="total_copies" value="1" min="1" required>
                    </div>
                    <div>
                        <label for="shelf_location">{{ __('Shelf / Location') }}:</label>
                        <input type="text" name="shelf_location" id="shelf_location" placeholder="Shelf A-3">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label for="publisher">{{ __('Publisher') }}:</label>
                        <input type="text" name="publisher" id="publisher" placeholder="Oxford / Pearson">
                    </div>
                    <div>
                        <label for="edition">{{ __('Edition') }}:</label>
                        <input type="text" name="edition" id="edition" placeholder="3rd Edition">
                    </div>
                </div>

                <button type="submit" class="btn">💾 {{ __('Save Book to Catalog') }}</button>
            </form>

            <hr style="margin: 22px 0 16px 0; border: none; border-top: 1px solid #e2e8f0;">

            <!-- Quick Action: Issue Book Modal Trigger -->
            <button type="button" class="btn btn-outline" onclick="openIssueModal()">
                📤 {{ __('Issue Book to Student') }}
            </button>
        </div>

        <!-- RIGHT PANEL: Book Inventory Table -->
        <div class="card" style="width: 100%;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                <h3 style="margin: 0; padding: 0; border: none;">📖 {{ __('Book Inventory Catalog') }}</h3>

                <!-- Search & Category Filter -->
                <form method="GET" action="{{ route('librarian.dashboard') }}" style="display: flex; gap: 8px; flex-wrap: wrap;">
                    @if($categories->count() > 0)
                        <select name="category" onchange="this.form.submit()" style="width: auto; padding: 6px 10px; font-size: 12px;">
                            <option value="">-- {{ __('All Categories') }} --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search title, author, shelf...') }}" style="width: 180px; padding: 6px 10px; font-size: 12px;">
                    <button type="submit" style="background: var(--primary); color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">🔍 {{ __('Search') }}</button>
                    @if(request('search') || request('category'))
                        <a href="{{ route('librarian.dashboard') }}" style="padding: 6px 10px; background: #e2e8f0; color: #475569; border-radius: 4px; text-decoration: none; font-size: 12px;">✕</a>
                    @endif
                </form>
            </div>

            @if($books->count() > 0)
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('Title & Author') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Shelf') }}</th>
                                <th style="text-align: center;">{{ __('Available / Total') }}</th>
                                <th style="text-align: right;">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $b)
                                <tr>
                                    <td>
                                        <b>{{ $b->title }}</b>
                                        @if($b->author)
                                            <div style="font-size: 11px; color: #64748b;">{{ __('By') }}: {{ $b->author }}</div>
                                        @endif
                                        @if($b->isbn)
                                            <div style="font-size: 10px; color: #94a3b8;">ISBN: {{ $b->isbn }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($b->category)
                                            <span class="badge badge-cat">{{ $b->category }}</span>
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $b->shelf_location ?: '-' }}
                                    </td>
                                    <td style="text-align: center;">
                                        @if($b->available_copies > 0)
                                            <span class="badge badge-avail">{{ $b->available_copies }} / {{ $b->total_copies }}</span>
                                        @else
                                            <span class="badge badge-out">0 / {{ $b->total_copies }} ({{ __('Out of Stock') }})</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: inline-flex; gap: 6px; align-items: center;">
                                            @if($b->available_copies > 0)
                                                <button type="button" onclick="openIssueModalForBook({{ $b->id }}, '{{ addslashes($b->title) }}')" style="background: none; border: 1px solid var(--primary); color: var(--primary); border-radius: 4px; padding: 3px 7px; font-size: 11px; cursor: pointer;" title="{{ __('Issue this book') }}">
                                                    📤 {{ __('Issue') }}
                                                </button>
                                            @endif

                                            <button type="button" onclick="openEditModal({{ json_encode($b) }})" style="background: none; border: none; cursor: pointer; font-size: 14px;" title="{{ __('Edit') }}">
                                                ✏️
                                            </button>

                                            <form method="POST" action="{{ route('librarian.books.destroy', $b->id) }}" onsubmit="return confirm('{{ __('Delete book from catalog?') }}')" style="display: inline; margin: 0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: none; border: none; cursor: pointer; color: #ef4444; font-size: 14px;" title="{{ __('Delete') }}">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 15px;">
                    {{ $books->withQueryString()->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 30px; background: #f8fafc; border-radius: 8px; color: #64748b; border: 1px dashed #cbd5e1;">
                    💡 {{ __('No books found in the library catalog matching your query. Use the form on the left to add books.') }}
                </div>
            @endif
        </div>
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
            <label for="issue_book_id">{{ __('Select Book to Issue') }} *:</label>
            <select name="book_id" id="issue_book_id" required>
                <option value="">-- {{ __('Choose Book') }} --</option>
                @foreach($books as $bookOption)
                    @if($bookOption->available_copies > 0)
                        <option value="{{ $bookOption->id }}">{{ $bookOption->title }} ({{ __('Available') }}: {{ $bookOption->available_copies }})</option>
                    @endif
                @endforeach
            </select>

            <label for="issue_student_id">{{ __('Select Student') }} *:</label>
            <select name="student_id" id="issue_student_id" required>
                <option value="">-- {{ __('Choose Student') }} --</option>
                @foreach($students as $st)
                    <option value="{{ $st->id }}">{{ $st->student_name }} - {{ $st->class_name }} ({{ $st->reg_number }})</option>
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
            <input type="text" name="remarks" id="remarks" placeholder="{{ __('e.g. Good condition, returned before exams') }}">

            <button type="submit" class="btn">📤 {{ __('Confirm Book Issuance') }}</button>
        </form>
    </div>
</div>

<!-- Modal: Edit Book -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 15px;">
            <h3 style="margin: 0; font-size: 16px; color: var(--secondary);">✏️ {{ __('Edit Book Details') }}</h3>
            <button type="button" onclick="closeEditModal()" style="background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
        </div>

        <form id="editBookForm" method="POST" action="">
            @csrf
            @method('PUT')

            <label for="edit_title">{{ __('Book Title') }} *:</label>
            <input type="text" name="title" id="edit_title" required>

            <label for="edit_author">{{ __('Author / Writer') }}:</label>
            <input type="text" name="author" id="edit_author">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <label for="edit_category">{{ __('Subject / Category') }}:</label>
                    <input type="text" name="category" id="edit_category">
                </div>
                <div>
                    <label for="edit_isbn">{{ __('ISBN / Book Code') }}:</label>
                    <input type="text" name="isbn" id="edit_isbn">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <label for="edit_total_copies">{{ __('Total Copies') }} *:</label>
                    <input type="number" name="total_copies" id="edit_total_copies" min="1" required>
                </div>
                <div>
                    <label for="edit_shelf_location">{{ __('Shelf / Location') }}:</label>
                    <input type="text" name="shelf_location" id="edit_shelf_location">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <label for="edit_publisher">{{ __('Publisher') }}:</label>
                    <input type="text" name="publisher" id="edit_publisher">
                </div>
                <div>
                    <label for="edit_edition">{{ __('Edition') }}:</label>
                    <input type="text" name="edition" id="edit_edition">
                </div>
            </div>

            <button type="submit" class="btn">💾 {{ __('Save Changes') }}</button>
        </form>
    </div>
</div>

<script>
    function openIssueModal() {
        document.getElementById('issueModal').style.display = 'flex';
    }

    function openIssueModalForBook(bookId, bookTitle) {
        document.getElementById('issue_book_id').value = bookId;
        document.getElementById('issueModal').style.display = 'flex';
    }

    function closeIssueModal() {
        document.getElementById('issueModal').style.display = 'none';
    }

    function openEditModal(book) {
        document.getElementById('editBookForm').action = '/librarian/books/' + book.id;
        document.getElementById('edit_title').value = book.title || '';
        document.getElementById('edit_author').value = book.author || '';
        document.getElementById('edit_category').value = book.category || '';
        document.getElementById('edit_isbn').value = book.isbn || '';
        document.getElementById('edit_total_copies').value = book.total_copies || 1;
        document.getElementById('edit_shelf_location').value = book.shelf_location || '';
        document.getElementById('edit_publisher').value = book.publisher || '';
        document.getElementById('edit_edition').value = book.edition || '';

        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    window.onclick = function(e) {
        if (e.target === document.getElementById('issueModal')) closeIssueModal();
        if (e.target === document.getElementById('editModal')) closeEditModal();
    };
</script>

</body>
</html>
