<?php

namespace App\Models;

use CodeIgniter\Model;

class BorrowingModel extends Model
{
    protected $table            = 'borrowings';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'book_id', 'user_id', 'borrow_date', 'due_date', 'return_date',
        'status', 'issued_by', 'returned_to', 'remarks'
    ];
    protected $useTimestamps    = true;

    /**
     * Get borrowings with book and user details
     */
    public function getBorrowingsWithDetails($filters = [])
    {
        $builder = $this->db->table('borrowings')
            ->select('borrowings.*, books.title as book_title, books.isbn, books.author,
                      users.name as borrower_name, users.email as borrower_email, users.role as borrower_role,
                      issuer.name as issued_by_name, returner.name as returned_to_name')
            ->join('books', 'borrowings.book_id = books.id')
            ->join('users', 'borrowings.user_id = users.id')
            ->join('users as issuer', 'borrowings.issued_by = issuer.id', 'left')
            ->join('users as returner', 'borrowings.returned_to = returner.id', 'left');

        if (!empty($filters['status'])) {
            $builder->where('borrowings.status', $filters['status']);
        }
        if (!empty($filters['user_id'])) {
            $builder->where('borrowings.user_id', $filters['user_id']);
        }

        return $builder->orderBy('borrowings.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get user's active borrowings
     */
    public function getUserActiveBorrowings($userId)
    {
        return $this->db->table('borrowings')
            ->select('borrowings.*, books.title as book_title, books.author, books.isbn, books.cover_image,
                      book_categories.name as category_name')
            ->join('books', 'borrowings.book_id = books.id')
            ->join('book_categories', 'books.category_id = book_categories.id', 'left')
            ->where('borrowings.user_id', $userId)
            ->whereIn('borrowings.status', ['borrowed', 'overdue'])
            ->orderBy('borrowings.due_date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get user's borrowing history
     */
    public function getUserBorrowingHistory($userId)
    {
        return $this->db->table('borrowings')
            ->select('borrowings.*, books.title as book_title, books.author, books.isbn')
            ->join('books', 'borrowings.book_id = books.id')
            ->where('borrowings.user_id', $userId)
            ->orderBy('borrowings.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Check if user currently has this book borrowed
     */
    public function isBookBorrowedByUser($bookId, $userId)
    {
        return $this->db->table('borrowings')
            ->where('book_id', $bookId)
            ->where('user_id', $userId)
            ->where('status', 'borrowed')
            ->countAllResults() > 0;
    }

    /**
     * Get count of active borrowings for a user
     */
    public function getActiveBorrowingCount($userId)
    {
        return $this->db->table('borrowings')
            ->where('user_id', $userId)
            ->whereIn('status', ['borrowed', 'overdue'])
            ->countAllResults();
    }

    /**
     * Get overdue borrowings
     */
    public function getOverdueBorrowings()
    {
        return $this->db->table('borrowings')
            ->select('borrowings.*, books.title as book_title, books.isbn,
                      users.name as borrower_name, users.email as borrower_email, users.role as borrower_role')
            ->join('books', 'borrowings.book_id = books.id')
            ->join('users', 'borrowings.user_id = users.id')
            ->where('borrowings.status', 'overdue')
            ->orderBy('borrowings.due_date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Update overdue statuses
     */
    public function updateOverdueStatuses()
    {
        return $this->db->table('borrowings')
            ->where('status', 'borrowed')
            ->where('due_date <', date('Y-m-d'))
            ->update(['status' => 'overdue']);
    }

    /**
     * Get recent borrowings for dashboard
     */
    public function getRecentBorrowings($limit = 5)
    {
        return $this->db->table('borrowings')
            ->select('borrowings.*, books.title as book_title,
                      users.name as borrower_name, users.role as borrower_role')
            ->join('books', 'borrowings.book_id = books.id')
            ->join('users', 'borrowings.user_id = users.id')
            ->orderBy('borrowings.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
