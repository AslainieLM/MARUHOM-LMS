<?php

namespace App\Models;

use CodeIgniter\Model;

class ReservationModel extends Model
{
    protected $table            = 'reservations';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'book_id', 'user_id', 'reservation_date', 'expiry_date', 'status'
    ];
    protected $useTimestamps    = true;

    /**
     * Get user's active reservations
     */
    public function getUserReservations($userId)
    {
        return $this->db->table('reservations')
            ->select('reservations.*, books.title as book_title, books.author, books.isbn,
                      book_categories.name as category_name')
            ->join('books', 'reservations.book_id = books.id')
            ->join('book_categories', 'books.category_id = book_categories.id', 'left')
            ->where('reservations.user_id', $userId)
            ->where('reservations.status', 'pending')
            ->orderBy('reservations.reservation_date', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Check if user has a pending reservation for this book
     */
    public function hasActiveReservation($bookId, $userId)
    {
        return $this->db->table('reservations')
            ->where('book_id', $bookId)
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->countAllResults() > 0;
    }

    /**
     * Get all pending reservations with details
     */
    public function getPendingReservations()
    {
        return $this->db->table('reservations')
            ->select('reservations.*, books.title as book_title, books.author, books.isbn,
                      books.available_copies, users.name as user_name, users.email as user_email,
                      users.role as user_role')
            ->join('books', 'reservations.book_id = books.id')
            ->join('users', 'reservations.user_id = users.id')
            ->where('reservations.status', 'pending')
            ->orderBy('reservations.reservation_date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Expire old reservations (older than 48 hours)
     */
    public function expireOldReservations()
    {
        return $this->db->table('reservations')
            ->where('status', 'pending')
            ->where('expiry_date <', date('Y-m-d H:i:s'))
            ->update(['status' => 'expired']);
    }

    /**
     * Get reservation count for a book
     */
    public function getReservationCount($bookId)
    {
        return $this->db->table('reservations')
            ->where('book_id', $bookId)
            ->where('status', 'pending')
            ->countAllResults();
    }
}
