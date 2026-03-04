<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table            = 'books';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'title', 'author', 'isbn', 'publisher', 'publication_year',
        'category_id', 'description', 'cover_image', 'total_copies',
        'available_copies', 'shelf_location', 'status'
    ];
    protected $useTimestamps    = true;

    /**
     * Get all books with category name
     */
    public function getBooksWithCategory()
    {
        return $this->db->table('books')
            ->select('books.*, book_categories.name as category_name')
            ->join('book_categories', 'books.category_id = book_categories.id', 'left')
            ->orderBy('books.title', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get a single book with category
     */
    public function getBookWithCategory($id)
    {
        return $this->db->table('books')
            ->select('books.*, book_categories.name as category_name')
            ->join('book_categories', 'books.category_id = book_categories.id', 'left')
            ->where('books.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Search books by title, author, isbn, or category
     */
    public function searchBooks($keyword)
    {
        return $this->db->table('books')
            ->select('books.*, book_categories.name as category_name')
            ->join('book_categories', 'books.category_id = book_categories.id', 'left')
            ->groupStart()
                ->like('books.title', $keyword)
                ->orLike('books.author', $keyword)
                ->orLike('books.isbn', $keyword)
                ->orLike('books.publisher', $keyword)
                ->orLike('book_categories.name', $keyword)
            ->groupEnd()
            ->orderBy('books.title', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get available books (with copies > 0)
     */
    public function getAvailableBooks()
    {
        return $this->db->table('books')
            ->select('books.*, book_categories.name as category_name')
            ->join('book_categories', 'books.category_id = book_categories.id', 'left')
            ->where('books.available_copies >', 0)
            ->where('books.status', 'available')
            ->orderBy('books.title', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Decrease available copies by 1
     */
    public function decrementAvailableCopies($bookId)
    {
        return $this->db->table('books')
            ->where('id', $bookId)
            ->where('available_copies >', 0)
            ->set('available_copies', 'available_copies - 1', false)
            ->update();
    }

    /**
     * Increase available copies by 1
     */
    public function incrementAvailableCopies($bookId)
    {
        return $this->db->table('books')
            ->where('id', $bookId)
            ->set('available_copies', 'available_copies + 1', false)
            ->update();
    }

    /**
     * Get books by category
     */
    public function getBooksByCategory($categoryId)
    {
        return $this->db->table('books')
            ->select('books.*, book_categories.name as category_name')
            ->join('book_categories', 'books.category_id = book_categories.id', 'left')
            ->where('books.category_id', $categoryId)
            ->orderBy('books.title', 'ASC')
            ->get()
            ->getResultArray();
    }
}
