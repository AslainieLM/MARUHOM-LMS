<?php

namespace App\Models;

use CodeIgniter\Model;

class BookCategoryModel extends Model
{
    protected $table            = 'book_categories';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['name', 'description'];
    protected $useTimestamps    = true;

    /**
     * Get all categories with book counts
     */
    public function getCategoriesWithCounts()
    {
        return $this->db->table('book_categories')
            ->select('book_categories.*, COUNT(books.id) as book_count')
            ->join('books', 'book_categories.id = books.category_id', 'left')
            ->groupBy('book_categories.id')
            ->orderBy('book_categories.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get all categories as dropdown options
     */
    public function getCategoryOptions()
    {
        $categories = $this->orderBy('name', 'ASC')->findAll();
        $options = [];
        foreach ($categories as $cat) {
            $options[$cat['id']] = $cat['name'];
        }
        return $options;
    }
}
