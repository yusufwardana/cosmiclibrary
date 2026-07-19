<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Services\LibraryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function __construct(private readonly LibraryService $library) {}

    public function index(Request $request): View
    {
        $books = $this->library->listBooks($request->only(['search', 'category_id', 'isbn']));

        return view('library.books.index', compact('books'));
    }

    public function create(): View
    {
        return view('library.books.create');
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $this->library->createBook($request->validated());

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show(Book $book): View
    {
        $book->load(['category', 'items']);

        return view('library.books.show', compact('book'));
    }

    public function edit(Book $book): View
    {
        return view('library.books.edit', compact('book'));
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $this->library->updateBook($book, $request->validated());

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $this->library->deleteBook($book);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
