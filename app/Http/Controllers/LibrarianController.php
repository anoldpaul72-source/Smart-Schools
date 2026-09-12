<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookBorrowing;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibrarianController extends Controller
{
    /**
     * Display the Library Dashboard & Book Catalog
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $schoolName = $user->school_name ?: School::value('school_name') ?? 'Default School';

        $query = Book::query();
        if ($user->school_name) {
            $query->where(function ($q) use ($user) {
                $q->where('school_name', $user->school_name)
                  ->orWhereNull('school_name');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('shelf_location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $books = $query->orderBy('title')->paginate(15);

        // Stats metrics
        $statsQuery = Book::query();
        if ($user->school_name) {
            $statsQuery->where(function ($q) use ($user) {
                $q->where('school_name', $user->school_name)
                      ->orWhereNull('school_name');
            });
        }
        $totalTitles   = (clone $statsQuery)->count();
        $totalCopies   = (clone $statsQuery)->sum('total_copies');
        $availableCopies = (clone $statsQuery)->sum('available_copies');

        $borrowQuery = BookBorrowing::query();
        if ($user->school_name) {
            $borrowQuery->where(function ($q) use ($user) {
                $q->where('school_name', $user->school_name)
                  ->orWhereNull('school_name');
            });
        }
        $activeBorrowed = (clone $borrowQuery)->where('status', 'Borrowed')->count();
        $overdueCount   = (clone $borrowQuery)->where('status', 'Borrowed')->where('due_date', '<', now()->toDateString())->count();

        // Distinct categories for filtering
        $categories = Book::whereNotNull('category')->distinct()->pluck('category')->sort()->values();

        // Students list for book issuing modal
        $studentsQuery = Student::query();
        if ($user->school_name) {
            $studentsQuery->where('school_name', $user->school_name);
        }
        $students = $studentsQuery->orderBy('class_name')->orderBy('student_name')->get();
        $classes  = $students->pluck('class_name')->unique()->sort()->values();

        return view('librarian.dashboard', compact(
            'schoolName',
            'books',
            'totalTitles',
            'totalCopies',
            'availableCopies',
            'activeBorrowed',
            'overdueCount',
            'categories',
            'students',
            'classes'
        ));
    }

    /**
     * Store a new book in the library catalog
     */
    public function storeBook(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'author'           => 'nullable|string|max:255',
            'isbn'             => 'nullable|string|max:100',
            'category'         => 'nullable|string|max:100',
            'publisher'        => 'nullable|string|max:255',
            'edition'          => 'nullable|string|max:50',
            'total_copies'     => 'required|integer|min:1',
            'shelf_location'   => 'nullable|string|max:100',
            'description'      => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        Book::create([
            'school_name'      => $user->school_name,
            'title'            => $request->title,
            'author'           => $request->author,
            'isbn'             => $request->isbn,
            'category'         => $request->category,
            'publisher'        => $request->publisher,
            'edition'          => $request->edition,
            'total_copies'     => $request->total_copies,
            'available_copies' => $request->total_copies,
            'shelf_location'   => $request->shelf_location,
            'description'      => $request->description,
        ]);

        return back()->with('success', '✔️ ' . __('Book added to library catalog successfully!'));
    }

    /**
     * Update an existing book record
     */
    public function updateBook(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'title'            => 'required|string|max:255',
            'author'           => 'nullable|string|max:255',
            'isbn'             => 'nullable|string|max:100',
            'category'         => 'nullable|string|max:100',
            'publisher'        => 'nullable|string|max:255',
            'edition'          => 'nullable|string|max:50',
            'total_copies'     => 'required|integer|min:1',
            'shelf_location'   => 'nullable|string|max:100',
            'description'      => 'nullable|string|max:1000',
        ]);

        $diff = $request->total_copies - $book->total_copies;
        $newAvailable = max(0, $book->available_copies + $diff);

        $book->update([
            'title'            => $request->title,
            'author'           => $request->author,
            'isbn'             => $request->isbn,
            'category'         => $request->category,
            'publisher'        => $request->publisher,
            'edition'          => $request->edition,
            'total_copies'     => $request->total_copies,
            'available_copies' => $newAvailable,
            'shelf_location'   => $request->shelf_location,
            'description'      => $request->description,
        ]);

        return back()->with('success', '✔️ ' . __('Book details updated successfully!'));
    }

    /**
     * Delete a book from catalog
     */
    public function destroyBook($id)
    {
        $book = Book::findOrFail($id);

        // Check if book currently has active borrowings
        $hasActive = BookBorrowing::where('book_id', $book->id)
            ->where('status', 'Borrowed')
            ->exists();

        if ($hasActive) {
            return back()->with('error', '⚠️ ' . __('Cannot delete book: Copies are currently borrowed by students.'));
        }

        $book->delete();
        return back()->with('success', '✔️ ' . __('Book removed from catalog successfully!'));
    }

    /**
     * Display Borrowings List & Filter
     */
    public function borrowings(Request $request)
    {
        $user = Auth::user();
        $schoolName = $user->school_name ?: School::value('school_name') ?? 'Default School';

        $query = BookBorrowing::with(['book', 'student', 'issuedBy'])->latest();

        if ($user->school_name) {
            $query->where(function ($q) use ($user) {
                $q->where('school_name', $user->school_name)
                  ->orWhereNull('school_name');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'Overdue') {
                $query->where('status', 'Borrowed')->where('due_date', '<', now()->toDateString());
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('student_name', 'like', "%{$search}%")
                       ->orWhere('reg_number', 'like', "%{$search}%")
                       ->orWhere('class_name', 'like', "%{$search}%");
                })->orWhereHas('book', function ($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%")
                       ->orWhere('isbn', 'like', "%{$search}%");
                });
            });
        }

        $borrowings = $query->paginate(20);

        // Books with available copies for issue modal
        $availableBooks = Book::where('available_copies', '>', 0);
        if ($user->school_name) {
            $availableBooks->where(function ($q) use ($user) {
                $q->where('school_name', $user->school_name)->orWhereNull('school_name');
            });
        }
        $availableBooks = $availableBooks->orderBy('title')->get();

        // Students list
        $studentsQuery = Student::query();
        if ($user->school_name) {
            $studentsQuery->where('school_name', $user->school_name);
        }
        $students = $studentsQuery->orderBy('class_name')->orderBy('student_name')->get();
        $classes  = $students->pluck('class_name')->unique()->sort()->values();

        return view('librarian.borrowings', compact('schoolName', 'borrowings', 'availableBooks', 'students', 'classes'));
    }

    /**
     * Issue a book to a student
     */
    public function issueBook(Request $request)
    {
        $request->validate([
            'book_id'       => 'required|exists:books,id',
            'student_id'    => 'required|exists:students,id',
            'borrowed_date' => 'required|date',
            'due_date'      => 'required|date|after_or_equal:borrowed_date',
            'remarks'       => 'nullable|string|max:500',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->available_copies < 1) {
            return back()->with('error', '⚠️ ' . __('Sorry, no copies of this book are currently available in the library.'));
        }

        $user = Auth::user();

        BookBorrowing::create([
            'school_name'   => $user->school_name,
            'book_id'       => $book->id,
            'student_id'    => $request->student_id,
            'borrowed_date' => $request->borrowed_date,
            'due_date'      => $request->due_date,
            'status'        => 'Borrowed',
            'remarks'       => $request->remarks,
            'issued_by'     => $user->id,
        ]);

        // Decrement available copies
        $book->decrement('available_copies');

        return back()->with('success', '✔️ ' . __('Book successfully issued to student!'));
    }

    /**
     * Mark a borrowed book as returned
     */
    public function returnBook(Request $request, $id)
    {
        $borrowing = BookBorrowing::findOrFail($id);

        if ($borrowing->status === 'Returned') {
            return back()->with('error', __('This book borrowing record has already been marked as returned.'));
        }

        $borrowing->update([
            'status'        => 'Returned',
            'returned_date' => now()->toDateString(),
            'remarks'       => $request->remarks ?: $borrowing->remarks,
        ]);

        // Increment book available copies
        if ($borrowing->book) {
            $borrowing->book->increment('available_copies');
        }

        return back()->with('success', '✔️ ' . __('Book marked as returned successfully!'));
    }
}
