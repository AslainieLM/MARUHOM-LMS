<?php

namespace App\Models;

use CodeIgniter\Model;

class FineModel extends Model
{
    protected $table            = 'fines';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'borrowing_id', 'user_id', 'amount', 'reason', 'status', 'paid_date'
    ];
    protected $useTimestamps    = true;

    /**
     * Get all fines with details
     */
    public function getFinesWithDetails($filters = [])
    {
        $builder = $this->db->table('fines')
            ->select('fines.*, borrowings.borrow_date, borrowings.due_date, borrowings.return_date,
                      books.title as book_title, books.isbn,
                      users.name as user_name, users.email as user_email, users.role as user_role')
            ->join('borrowings', 'fines.borrowing_id = borrowings.id')
            ->join('books', 'borrowings.book_id = books.id')
            ->join('users', 'fines.user_id = users.id');

        if (!empty($filters['status'])) {
            $builder->where('fines.status', $filters['status']);
        }
        if (!empty($filters['user_id'])) {
            $builder->where('fines.user_id', $filters['user_id']);
        }

        return $builder->orderBy('fines.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get unpaid fines for a user
     */
    public function getUserUnpaidFines($userId)
    {
        return $this->db->table('fines')
            ->select('fines.*, books.title as book_title')
            ->join('borrowings', 'fines.borrowing_id = borrowings.id')
            ->join('books', 'borrowings.book_id = books.id')
            ->where('fines.user_id', $userId)
            ->where('fines.status', 'unpaid')
            ->orderBy('fines.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get total unpaid fines amount for a user
     */
    public function getUserTotalUnpaidFines($userId)
    {
        $result = $this->db->table('fines')
            ->selectSum('amount')
            ->where('user_id', $userId)
            ->where('status', 'unpaid')
            ->get()
            ->getRowArray();

        return $result['amount'] ?? 0;
    }

    /**
     * Calculate fine for overdue book (PHP 10.00 per day)
     */
    public function calculateFine($dueDate, $returnDate = null)
    {
        $ratePerDay = 10.00; // PHP 10 per day
        $due = new \DateTime($dueDate);
        $returned = $returnDate ? new \DateTime($returnDate) : new \DateTime();
        
        if ($returned > $due) {
            $daysOverdue = $returned->diff($due)->days;
            return $daysOverdue * $ratePerDay;
        }
        
        return 0;
    }
}
