<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Student;
use App\Models\Book;
use App\Models\BookBorrowing;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LibrarianPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        School::create([
            'school_name' => 'Demo High School',
            'address'     => 'Dar es Salaam',
            'phone'       => '0700000000',
        ]);
    }

    public function test_librarian_can_view_library_dashboard(): void
    {
        $librarian = User::factory()->create([
            'role'        => 'Librarian',
            'school_name' => 'Demo High School',
        ]);

        $response = $this->actingAs($librarian)->get(route('librarian.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Library Management');
    }

    public function test_librarian_can_catalog_new_book(): void
    {
        $librarian = User::factory()->create([
            'role'        => 'Librarian',
            'school_name' => 'Demo High School',
        ]);

        $response = $this->actingAs($librarian)->post(route('librarian.books.store'), [
            'title'        => 'Advanced Physics Vol 1',
            'author'       => 'Nelkon & Parker',
            'category'     => 'Physics',
            'total_copies' => 5,
            'shelf_location' => 'Shelf B-2',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('books', [
            'title'            => 'Advanced Physics Vol 1',
            'total_copies'     => 5,
            'available_copies' => 5,
        ]);
    }

    public function test_librarian_can_issue_and_return_book(): void
    {
        $librarian = User::factory()->create([
            'role'        => 'Librarian',
            'school_name' => 'Demo High School',
        ]);

        $student = Student::create([
            'reg_number'   => 'STD-TEST-001',
            'student_name' => 'Baraka Juma',
            'class_name'   => 'Form 4',
            'school_name'  => 'Demo High School',
        ]);

        $book = Book::create([
            'title'            => 'Chemistry for Secondary Schools',
            'school_name'      => 'Demo High School',
            'total_copies'     => 2,
            'available_copies' => 2,
        ]);

        // 1. Issue Book
        $issueResponse = $this->actingAs($librarian)->post(route('librarian.borrowings.issue'), [
            'book_id'       => $book->id,
            'student_id'    => $student->id,
            'borrowed_date' => now()->toDateString(),
            'due_date'      => now()->addDays(14)->toDateString(),
        ]);

        $issueResponse->assertRedirect();
        $this->assertEquals(1, $book->fresh()->available_copies);
        $this->assertDatabaseHas('book_borrowings', [
            'book_id'    => $book->id,
            'student_id' => $student->id,
            'status'     => 'Borrowed',
        ]);

        $borrowing = BookBorrowing::first();

        // 2. Return Book
        $returnResponse = $this->actingAs($librarian)->post(route('librarian.borrowings.return', $borrowing->id));
        $returnResponse->assertRedirect();

        $this->assertEquals(2, $book->fresh()->available_copies);
        $this->assertEquals('Returned', $borrowing->fresh()->status);
    }

    public function test_unauthorized_user_cannot_access_librarian_portal(): void
    {
        $parent = User::factory()->create([
            'role'        => 'Parent',
            'school_name' => 'Demo High School',
        ]);

        $response = $this->actingAs($parent)->get(route('librarian.dashboard'));
        $response->assertStatus(403);
    }
}
