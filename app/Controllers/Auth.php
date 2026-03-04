<?php

namespace App\Controllers;

use App\Models\CaptchaModel;
use App\Models\BookModel;
use App\Models\BorrowingModel;
use App\Models\ReservationModel;
use App\Models\FineModel;
use App\Models\BookCategoryModel;

class Auth extends BaseController
{
    protected $session;
    protected $validation;
    protected $db;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
    }
    
    /**
     * Create notification for user
     */
    private function createNotification($userId, $message)
    {
        try {
            $notificationModel = new \App\Models\NotificationsModel();
            $philippineTimezone = new \DateTimeZone('Asia/Manila');
            $currentDateTime = new \DateTime('now', $philippineTimezone);
            
            $notificationData = [
                'user_id' => $userId,
                'message' => $message,
                'is_read' => 0,
                'created_at' => $currentDateTime->format('Y-m-d H:i:s')
            ];
            
            return $notificationModel->insert($notificationData);
        } catch (\Exception $e) {
            log_message('error', 'Notification creation error: ' . $e->getMessage());
            return false;
        }
    }

    public function register()
    {
        if ($this->session->get('isLoggedIn') === true) {
            return redirect()->to(base_url('dashboard'));
        }

        if ($this->request->getMethod() === 'POST') {
            
            $rules = [
                'name'             => 'required|min_length[3]|max_length[100]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'password'         => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]'
            ];

            $messages = [
                'name' => [
                    'required'   => 'Name is required.',
                    'min_length' => 'Name must be at least 3 characters long.',
                    'max_length' => 'Name cannot exceed 100 characters.'
                ],
                'email' => [
                    'required'    => 'Email is required.',
                    'valid_email' => 'Please enter a valid email address.',
                    'is_unique'   => 'This email is already registered.'
                ],
                'password' => [
                    'required'   => 'Password is required.',
                    'min_length' => 'Password must be at least 6 characters long.'
                ],
                'password_confirm' => [
                    'required' => 'Password confirmation is required.',
                    'matches'  => 'Password confirmation does not match.'
                ]
            ];

            if ($this->validate($rules, $messages)) {
                
                $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
                
                $userData = [
                    'name'       => $this->request->getPost('name'),
                    'email'      => $this->request->getPost('email'),
                    'password'   => $hashedPassword,
                    'role'       => 'student',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $builder = $this->db->table('users');
                
                if ($builder->insert($userData)) {
                    $this->session->setFlashdata('success', 'Registration successful! Please login with your credentials.');
                    return redirect()->to(base_url('login'));
                } else {
                    $this->session->setFlashdata('error', 'Registration failed. Please try again.');
                }
            } else {
                $this->session->setFlashdata('errors', $this->validation->getErrors());
            }
        }

        return view('auth/register');
    }

    public function login()
    {
        if ($this->session->get('isLoggedIn') === true) {
            return redirect()->to(base_url('dashboard'));
        }

        if ($this->request->getMethod() === 'POST') {
            
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required',
                'captcha'  => 'required'
            ];

            $messages = [
                'email' => [
                    'required'    => 'Email is required.',
                    'valid_email' => 'Please enter a valid email address.'
                ],
                'password' => [
                    'required' => 'Password is required.'
                ],
                'captcha' => [
                    'required' => 'CAPTCHA is required.'
                ]
            ];

            if ($this->validate($rules, $messages)) {
                $captchaModel = new CaptchaModel();
                $captchaId = (int) ($this->session->get('captcha_id') ?? 0);
                $captchaInput = (string) $this->request->getPost('captcha');
                $userIpAddress = (string) $this->request->getIPAddress();

                if (!$captchaModel->validateCaptcha($captchaId, $captchaInput, $userIpAddress)) {
                    $this->session->remove('captcha_id');
                    $this->session->setFlashdata('error', 'Invalid or expired CAPTCHA. Please try again.');
                    return view('auth/login');
                }

                $this->session->remove('captcha_id');

                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                $builder = $this->db->table('users');
                $user = $builder->where('email', $email)->get()->getRowArray();

                if ($user && password_verify($password, $user['password'])) {
                    
                    $sessionData = [
                        'userID'     => $user['id'],
                        'name'       => $user['name'],
                        'email'      => $user['email'],
                        'role'       => $user['role'],
                        'isLoggedIn' => true
                    ];

                    $this->session->set($sessionData);
                    
                    $this->session->setFlashdata('success', 'Welcome back, ' . $user['name'] . '!');
                    return redirect()->to(base_url('dashboard'));
                    
                } else {
                    $this->session->setFlashdata('error', 'Invalid email or password.');
                }
            } else {
                $this->session->setFlashdata('errors', $this->validation->getErrors());
            }
        }

        return view('auth/login');
    }

    public function logout()
    {
        $this->session->setFlashdata('success', 'You have been logged out successfully.');
        $this->session->destroy();
        return redirect()->to(base_url('login'));
    }

    public function dashboard()
    {
        if ($this->session->get('isLoggedIn') !== true) {
            $this->session->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to(base_url('login'));
        }

        $userRole = $this->session->get('role');
        $borrowingModel = new BorrowingModel();
        
        // Update overdue statuses
        $borrowingModel->updateOverdueStatuses();
        
        $baseData = [
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'email'  => $this->session->get('email'),
                'role'   => $this->session->get('role')
            ]
        ];

        switch ($userRole) {
            case 'admin':
                $totalUsers = $this->db->table('users')->countAll();
                $totalAdmins = $this->db->table('users')->where('role', 'admin')->countAllResults();
                $totalLibrarians = $this->db->table('users')->where('role', 'librarian')->countAllResults();
                $totalTeachers = $this->db->table('users')->where('role', 'teacher')->countAllResults();
                $totalStudents = $this->db->table('users')->where('role', 'student')->countAllResults();
                $totalBooks = $this->db->table('books')->countAll();
                $totalBorrowings = $this->db->table('borrowings')->whereIn('status', ['borrowed', 'overdue'])->countAllResults();
                $totalOverdue = $this->db->table('borrowings')->where('status', 'overdue')->countAllResults();
                $totalFinesUnpaid = $this->db->table('fines')->where('status', 'unpaid')->selectSum('amount')->get()->getRowArray()['amount'] ?? 0;
                $recentUsers = $this->db->table('users')->orderBy('created_at', 'DESC')->limit(5)->get()->getResultArray();
                $recentBorrowings = $borrowingModel->getRecentBorrowings(5);

                $dashboardData = array_merge($baseData, [
                    'title' => 'Admin Dashboard - Maruhom Library',
                    'totalUsers' => $totalUsers,
                    'totalAdmins' => $totalAdmins,
                    'totalLibrarians' => $totalLibrarians,
                    'totalTeachers' => $totalTeachers,
                    'totalStudents' => $totalStudents,
                    'totalBooks' => $totalBooks,
                    'totalBorrowings' => $totalBorrowings,
                    'totalOverdue' => $totalOverdue,
                    'totalFinesUnpaid' => $totalFinesUnpaid,
                    'recentUsers' => $recentUsers,
                    'recentBorrowings' => $recentBorrowings
                ]);
                return view('auth/dashboard', $dashboardData);
                
            case 'librarian':
                $totalBooks = $this->db->table('books')->countAll();
                $totalAvailableBooks = $this->db->table('books')->where('available_copies >', 0)->countAllResults();
                $totalBorrowings = $this->db->table('borrowings')->whereIn('status', ['borrowed', 'overdue'])->countAllResults();
                $totalOverdue = $this->db->table('borrowings')->where('status', 'overdue')->countAllResults();
                $totalReservations = $this->db->table('reservations')->where('status', 'pending')->countAllResults();
                $totalFinesUnpaid = $this->db->table('fines')->where('status', 'unpaid')->selectSum('amount')->get()->getRowArray()['amount'] ?? 0;
                $recentBorrowings = $borrowingModel->getRecentBorrowings(5);
                $overdueBorrowings = $borrowingModel->getOverdueBorrowings();

                $dashboardData = array_merge($baseData, [
                    'title' => 'Librarian Dashboard - Maruhom Library',
                    'totalBooks' => $totalBooks,
                    'totalAvailableBooks' => $totalAvailableBooks,
                    'totalBorrowings' => $totalBorrowings,
                    'totalOverdue' => $totalOverdue,
                    'totalReservations' => $totalReservations,
                    'totalFinesUnpaid' => $totalFinesUnpaid,
                    'recentBorrowings' => $recentBorrowings,
                    'overdueBorrowings' => $overdueBorrowings
                ]);
                return view('auth/dashboard', $dashboardData);
                
            case 'teacher':
                $userId = $this->session->get('userID');
                $activeBorrowings = $borrowingModel->getUserActiveBorrowings($userId);
                $borrowingHistory = $borrowingModel->getUserBorrowingHistory($userId);
                $fineModel = new FineModel();
                $unpaidFines = $fineModel->getUserTotalUnpaidFines($userId);
                $reservationModel = new ReservationModel();
                $activeReservations = $reservationModel->getUserReservations($userId);

                $dashboardData = array_merge($baseData, [
                    'title' => 'Teacher Dashboard - Maruhom Library',
                    'activeBorrowings' => $activeBorrowings,
                    'borrowingHistory' => $borrowingHistory,
                    'unpaidFines' => $unpaidFines,
                    'activeReservations' => $activeReservations,
                    'totalBorrowed' => count($activeBorrowings),
                    'totalHistory' => count($borrowingHistory)
                ]);
                return view('auth/dashboard', $dashboardData);
                
            case 'student':
                $userId = $this->session->get('userID');
                $activeBorrowings = $borrowingModel->getUserActiveBorrowings($userId);
                $borrowingHistory = $borrowingModel->getUserBorrowingHistory($userId);
                $fineModel = new FineModel();
                $unpaidFines = $fineModel->getUserTotalUnpaidFines($userId);
                $reservationModel = new ReservationModel();
                $activeReservations = $reservationModel->getUserReservations($userId);

                $dashboardData = array_merge($baseData, [
                    'title' => 'Student Dashboard - Maruhom Library',
                    'activeBorrowings' => $activeBorrowings,
                    'borrowingHistory' => $borrowingHistory,
                    'unpaidFines' => $unpaidFines,
                    'activeReservations' => $activeReservations,
                    'totalBorrowed' => count($activeBorrowings),
                    'totalHistory' => count($borrowingHistory)
                ]);
                return view('auth/dashboard', $dashboardData);
                
            default:
                return view('auth/dashboard', $baseData);
        }
    }

    public function manageUsers()
    {
        if ($this->session->get('isLoggedIn') !== true) {
            $this->session->setFlashdata('error', 'Please login to access this page.');
            return redirect()->to(base_url('login'));
        }        
        
        if ($this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied.');
            return redirect()->to(base_url('dashboard'));
        }

        $action = $this->request->getGet('action');
        $userID = $this->request->getGet('id');
        $currentAdminID = $this->session->get('userID');
        
        if ($action === 'create' && $this->request->getMethod() === 'POST') {
            $rules = [
                'name'     => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-ZñÑ\s]+$/]',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'role'     => 'required|in_list[admin,librarian,teacher,student]'
            ];

            $messages = [
                'name' => [
                    'required'    => 'Name is required.',
                    'min_length'  => 'Name must be at least 3 characters long.',
                    'max_length'  => 'Name cannot exceed 100 characters.',
                    'regex_match' => 'Name can only contain letters (including ñ/Ñ) and spaces.'
                ],
                'email' => [
                    'required'    => 'Email is required.',
                    'valid_email' => 'Please enter a valid email address.',
                    'is_unique'   => 'This email is already registered.'
                ],
                'password' => [
                    'required'   => 'Password is required.',
                    'min_length' => 'Password must be at least 6 characters long.'
                ],
                'role' => [
                    'required' => 'Role is required.',
                    'in_list'  => 'Invalid role selected.'
                ]
            ];

            if ($this->validate($rules, $messages)) {
                $userData = [
                    'name'       => $this->request->getPost('name'),
                    'email'      => $this->request->getPost('email'),
                    'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role'       => $this->request->getPost('role'),
                    'phone'      => $this->request->getPost('phone') ?: null,
                    'address'    => $this->request->getPost('address') ?: null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $builder = $this->db->table('users');
                if ($builder->insert($userData)) {
                    $this->session->setFlashdata('success', 'User created successfully!');
                    return redirect()->to(base_url('admin/manage_users'));
                } else {
                    $this->session->setFlashdata('error', 'Failed to create user.');
                }
            } else {
                $this->session->setFlashdata('errors', $this->validation->getErrors());
            }
        }
        
        if ($action === 'edit' && $userID) {
            $builder = $this->db->table('users');
            $userToEdit = $builder->where('id', $userID)->get()->getRowArray();

            if (!$userToEdit) {
                $this->session->setFlashdata('error', 'User not found.');
                return redirect()->to(base_url('admin/manage_users'));
            }

            if ($userToEdit['id'] == $currentAdminID) {
                $this->session->setFlashdata('error', 'You cannot edit your own account.');
                return redirect()->to(base_url('admin/manage_users'));
            }

            if ($this->request->getMethod() === 'POST') {
                $rules = [
                    'name' => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-ZñÑ\s]+$/]',
                    'email' => "required|valid_email|is_unique[users.email,id,{$userID}]",
                    'role' => 'required|in_list[admin,librarian,teacher,student]'
                ];

                if ($this->request->getPost('password')) {
                    $rules['password'] = 'min_length[6]';
                }

                if ($this->validate($rules)) {
                    $updateData = [
                        'name'       => $this->request->getPost('name'),
                        'email'      => $this->request->getPost('email'),
                        'role'       => $this->request->getPost('role'),
                        'phone'      => $this->request->getPost('phone') ?: null,
                        'address'    => $this->request->getPost('address') ?: null,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->request->getPost('password')) {
                        $updateData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
                    }

                    if ($builder->where('id', $userID)->update($updateData)) {
                        $this->session->setFlashdata('success', 'User updated successfully!');
                        return redirect()->to(base_url('admin/manage_users'));
                    } else {
                        $this->session->setFlashdata('error', 'Failed to update user.');
                    }
                } else {
                    $this->session->setFlashdata('errors', $this->validation->getErrors());
                }
            }

            $users = $this->db->table('users')->orderBy('created_at', 'DESC')->get()->getResultArray();
            $data = [
                'user' => [
                    'userID' => $this->session->get('userID'),
                    'name'   => $this->session->get('name'),
                    'email'  => $this->session->get('email'),
                    'role'   => $this->session->get('role')
                ],
                'title' => 'Edit User - Admin Dashboard',
                'users' => $users,
                'currentAdminID' => $currentAdminID,
                'editUser' => $userToEdit,
                'showCreateForm' => false,
                'showEditForm' => true
            ];
            return view('admin/manage_users', $data);
        }
        
        if ($action === 'delete' && $userID) {
            $builder = $this->db->table('users');
            $userToDelete = $builder->where('id', $userID)->get()->getRowArray();

            if (!$userToDelete) {
                $this->session->setFlashdata('error', 'User not found.');
                return redirect()->to(base_url('admin/manage_users'));
            }

            if ($userToDelete['id'] == $currentAdminID) {
                $this->session->setFlashdata('error', 'You cannot delete your own account.');
                return redirect()->to(base_url('admin/manage_users'));
            }

            if ($userToDelete['role'] === 'admin') {
                $this->session->setFlashdata('error', 'You cannot delete another admin account.');
                return redirect()->to(base_url('admin/manage_users'));
            }

            // Check active borrowings
            $activeBorrowings = $this->db->table('borrowings')
                ->where('user_id', $userID)
                ->where('status', 'borrowed')
                ->countAllResults();
            
            if ($activeBorrowings > 0) {
                $this->session->setFlashdata('error', 'Cannot delete user with active borrowings.');
                return redirect()->to(base_url('admin/manage_users'));
            }

            if ($this->db->table('users')->where('id', $userID)->delete()) {
                $this->session->setFlashdata('success', 'User deleted successfully!');
            } else {
                $this->session->setFlashdata('error', 'Failed to delete user.');
            }

            return redirect()->to(base_url('admin/manage_users'));
        }
        
        $users = $this->db->table('users')->orderBy('created_at', 'DESC')->get()->getResultArray();

        $data = [
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'email'  => $this->session->get('email'),
                'role'   => $this->session->get('role')
            ],
            'title' => 'Manage Users - Admin Dashboard',
            'users' => $users,
            'currentAdminID' => $currentAdminID,
            'editUser' => null,
            'showCreateForm' => $this->request->getGet('create') === 'true' || ($action === 'create' && $this->request->getMethod() !== 'POST'),
            'showEditForm' => false
        ];
          
        return view('admin/manage_users', $data);
    }

    public function manageBooks()
    {
        if ($this->session->get('isLoggedIn') !== true) {
            return redirect()->to(base_url('login'));        
        }
        
        $userRole = $this->session->get('role');
        if (!in_array($userRole, ['admin', 'librarian'])) {
            return redirect()->to(base_url('dashboard'));
        }

        $action = $this->request->getGet('action');
        $bookID = $this->request->getGet('id');
        $bookModel = new BookModel();
        $categoryModel = new BookCategoryModel();
        
        if ($action === 'create' && $this->request->getMethod() === 'POST') {
            $rules = [
                'title'        => 'required|min_length[2]|max_length[255]',
                'author'       => 'required|min_length[2]|max_length[255]',
                'isbn'         => 'permit_empty|max_length[20]',
                'publisher'    => 'permit_empty|max_length[255]',
                'total_copies' => 'required|integer|greater_than[0]',
                'category_id'  => 'permit_empty|integer',
                'status'       => 'required|in_list[available,unavailable]'
            ];

            if ($this->validate($rules)) {
                $totalCopies = (int) $this->request->getPost('total_copies');
                $bookData = [
                    'title'            => $this->request->getPost('title'),
                    'author'           => $this->request->getPost('author'),
                    'isbn'             => $this->request->getPost('isbn') ?: null,
                    'publisher'        => $this->request->getPost('publisher') ?: null,
                    'publication_year' => $this->request->getPost('publication_year') ?: null,
                    'category_id'      => $this->request->getPost('category_id') ?: null,
                    'description'      => $this->request->getPost('description') ?: null,
                    'total_copies'     => $totalCopies,
                    'available_copies' => $totalCopies,
                    'shelf_location'   => $this->request->getPost('shelf_location') ?: null,
                    'status'           => $this->request->getPost('status'),
                    'created_at'       => date('Y-m-d H:i:s'),
                    'updated_at'       => date('Y-m-d H:i:s')
                ];

                if ($bookModel->insert($bookData)) {
                    $this->session->setFlashdata('success', 'Book added successfully!');
                    $redirect = $userRole === 'librarian' ? 'librarian/manage_books' : 'admin/manage_books';
                    return redirect()->to(base_url($redirect));
                } else {
                    $this->session->setFlashdata('error', 'Failed to add book.');
                }
            } else {
                $this->session->setFlashdata('errors', $this->validation->getErrors());
            }
        }
        
        if ($action === 'edit' && $bookID) {
            $bookToEdit = $bookModel->find($bookID);
            if (!$bookToEdit) {
                $this->session->setFlashdata('error', 'Book not found.');
                $redirect = $userRole === 'librarian' ? 'librarian/manage_books' : 'admin/manage_books';
                return redirect()->to(base_url($redirect));
            }
            
            if ($this->request->getMethod() === 'POST') {
                $rules = [
                    'title'        => 'required|min_length[2]|max_length[255]',
                    'author'       => 'required|min_length[2]|max_length[255]',
                    'total_copies' => 'required|integer|greater_than[0]',
                    'status'       => 'required|in_list[available,unavailable]'
                ];

                if ($this->validate($rules)) {
                    $newTotal = (int) $this->request->getPost('total_copies');
                    $oldTotal = (int) $bookToEdit['total_copies'];
                    $currentAvailable = (int) $bookToEdit['available_copies'];
                    $borrowed = $oldTotal - $currentAvailable;
                    $newAvailable = max(0, $newTotal - $borrowed);

                    $updateData = [
                        'title'            => $this->request->getPost('title'),
                        'author'           => $this->request->getPost('author'),
                        'isbn'             => $this->request->getPost('isbn') ?: null,
                        'publisher'        => $this->request->getPost('publisher') ?: null,
                        'publication_year' => $this->request->getPost('publication_year') ?: null,
                        'category_id'      => $this->request->getPost('category_id') ?: null,
                        'description'      => $this->request->getPost('description') ?: null,
                        'total_copies'     => $newTotal,
                        'available_copies' => $newAvailable,
                        'shelf_location'   => $this->request->getPost('shelf_location') ?: null,
                        'status'           => $this->request->getPost('status'),
                        'updated_at'       => date('Y-m-d H:i:s')
                    ];

                    if ($bookModel->update($bookID, $updateData)) {
                        $this->session->setFlashdata('success', 'Book updated successfully!');
                        $redirect = $userRole === 'librarian' ? 'librarian/manage_books' : 'admin/manage_books';
                        return redirect()->to(base_url($redirect));
                    } else {
                        $this->session->setFlashdata('error', 'Failed to update book.');
                    }
                } else {
                    $this->session->setFlashdata('errors', $this->validation->getErrors());
                }
            }
            
            $books = $bookModel->getBooksWithCategory();
            $categories = $categoryModel->findAll();
            $data = [
                'user' => [
                    'userID' => $this->session->get('userID'),
                    'name'   => $this->session->get('name'),
                    'email'  => $this->session->get('email'),
                    'role'   => $this->session->get('role')
                ],
                'title' => 'Edit Book',
                'books' => $books,
                'categories' => $categories,
                'editBook' => $bookToEdit,
                'showCreateForm' => false,
                'showEditForm' => true
            ];
            $viewPath = $userRole === 'librarian' ? 'librarian/manage_books' : 'admin/manage_books';
            return view($viewPath, $data);
        }
        
        if ($action === 'delete' && $bookID) {
            $activeBorrowings = $this->db->table('borrowings')
                ->where('book_id', $bookID)
                ->where('status', 'borrowed')
                ->countAllResults();
            
            if ($activeBorrowings > 0) {
                $this->session->setFlashdata('error', 'Cannot delete book with active borrowings.');
            } else {
                if ($bookModel->delete($bookID)) {
                    $this->session->setFlashdata('success', 'Book deleted successfully!');
                } else {
                    $this->session->setFlashdata('error', 'Failed to delete book.');
                }
            }
            $redirect = $userRole === 'librarian' ? 'librarian/manage_books' : 'admin/manage_books';
            return redirect()->to(base_url($redirect));
        }
          
        $books = $bookModel->getBooksWithCategory();
        $categories = $categoryModel->findAll();

        $data = [
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'email'  => $this->session->get('email'),
                'role'   => $this->session->get('role')
            ],
            'title' => 'Manage Books',
            'books' => $books,
            'categories' => $categories,
            'editBook' => null,
            'showCreateForm' => $this->request->getGet('create') === 'true' || ($action === 'create' && $this->request->getMethod() !== 'POST'),
            'showEditForm' => false
        ];
        
        $viewPath = $userRole === 'librarian' ? 'librarian/manage_books' : 'admin/manage_books';
        return view($viewPath, $data);
    }

    public function manageCategories()
    {
        if ($this->session->get('isLoggedIn') !== true) return redirect()->to(base_url('login'));
        $userRole = $this->session->get('role');
        if (!in_array($userRole, ['admin', 'librarian'])) return redirect()->to(base_url('dashboard'));

        $action = $this->request->getGet('action');
        $catID = $this->request->getGet('id');
        $categoryModel = new BookCategoryModel();

        if ($action === 'create' && $this->request->getMethod() === 'POST') {
            if ($this->validate(['name' => 'required|min_length[2]|max_length[100]|is_unique[book_categories.name]'])) {
                $categoryModel->insert([
                    'name' => $this->request->getPost('name'),
                    'description' => $this->request->getPost('description') ?: null,
                ]);
                $this->session->setFlashdata('success', 'Category created!');
            } else {
                $this->session->setFlashdata('errors', $this->validation->getErrors());
            }
            $redirect = $userRole === 'librarian' ? 'librarian/manage_books' : 'admin/manage_books';
            return redirect()->to(base_url($redirect . '?create=true'));
        }

        if ($action === 'delete' && $catID) {
            $booksInCat = $this->db->table('books')->where('category_id', $catID)->countAllResults();
            if ($booksInCat > 0) {
                $this->session->setFlashdata('error', 'Cannot delete category with books assigned.');
            } else {
                $categoryModel->delete($catID);
                $this->session->setFlashdata('success', 'Category deleted!');
            }
            $redirect = $userRole === 'librarian' ? 'librarian/manage_books' : 'admin/manage_books';
            return redirect()->to(base_url($redirect));
        }

        $redirect = $userRole === 'librarian' ? 'librarian/manage_books' : 'admin/manage_books';
        return redirect()->to(base_url($redirect));
    }

    public function manageBorrowings()
    {
        if ($this->session->get('isLoggedIn') !== true) return redirect()->to(base_url('login'));
        $userRole = $this->session->get('role');
        if (!in_array($userRole, ['admin', 'librarian'])) return redirect()->to(base_url('dashboard'));

        $borrowingModel = new BorrowingModel();
        $borrowingModel->updateOverdueStatuses();
        
        $action = $this->request->getGet('action');
        $borrowingID = $this->request->getGet('id');

        // Issue a book
        if ($action === 'issue' && $this->request->getMethod() === 'POST') {
            $rules = [
                'book_id'  => 'required|integer',
                'user_id'  => 'required|integer',
                'due_date' => 'required|valid_date[Y-m-d]'
            ];

            if ($this->validate($rules)) {
                $bookId = $this->request->getPost('book_id');
                $userId = $this->request->getPost('user_id');
                $dueDate = $this->request->getPost('due_date');

                $bookModel = new BookModel();
                $book = $bookModel->find($bookId);
                if (!$book || $book['available_copies'] <= 0) {
                    $this->session->setFlashdata('error', 'Book is not available.');
                    $redirect = $userRole === 'librarian' ? 'librarian/manage_borrowings' : 'admin/manage_borrowings';
                    return redirect()->to(base_url($redirect));
                }

                if ($borrowingModel->isBookBorrowedByUser($bookId, $userId)) {
                    $this->session->setFlashdata('error', 'User already has this book.');
                    $redirect = $userRole === 'librarian' ? 'librarian/manage_borrowings' : 'admin/manage_borrowings';
                    return redirect()->to(base_url($redirect));
                }

                $borrower = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
                $maxBooks = ($borrower['role'] === 'teacher') ? 5 : 3;
                $activeCount = $borrowingModel->getActiveBorrowingCount($userId);
                
                if ($activeCount >= $maxBooks) {
                    $this->session->setFlashdata('error', "Borrowing limit reached ({$maxBooks} books).");
                    $redirect = $userRole === 'librarian' ? 'librarian/manage_borrowings' : 'admin/manage_borrowings';
                    return redirect()->to(base_url($redirect));
                }

                $fineModel = new FineModel();
                if ($fineModel->getUserTotalUnpaidFines($userId) > 0) {
                    $this->session->setFlashdata('error', 'User has unpaid fines. Settle first.');
                    $redirect = $userRole === 'librarian' ? 'librarian/manage_borrowings' : 'admin/manage_borrowings';
                    return redirect()->to(base_url($redirect));
                }

                $borrowingData = [
                    'book_id'     => $bookId,
                    'user_id'     => $userId,
                    'borrow_date' => date('Y-m-d'),
                    'due_date'    => $dueDate,
                    'status'      => 'borrowed',
                    'issued_by'   => $this->session->get('userID'),
                    'remarks'     => $this->request->getPost('remarks') ?: null,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s')
                ];

                if ($borrowingModel->insert($borrowingData)) {
                    $bookModel->decrementAvailableCopies($bookId);
                    $this->createNotification($userId, "Book '{$book['title']}' issued to you. Due: {$dueDate}");
                    $this->session->setFlashdata('success', 'Book issued successfully!');
                } else {
                    $this->session->setFlashdata('error', 'Failed to issue book.');
                }
            } else {
                $this->session->setFlashdata('errors', $this->validation->getErrors());
            }
            $redirect = $userRole === 'librarian' ? 'librarian/manage_borrowings' : 'admin/manage_borrowings';
            return redirect()->to(base_url($redirect));
        }

        // Return a book
        if ($action === 'return' && $borrowingID) {
            $borrowing = $borrowingModel->find($borrowingID);
            if (!$borrowing || $borrowing['status'] === 'returned') {
                $this->session->setFlashdata('error', 'Invalid borrowing record.');
                $redirect = $userRole === 'librarian' ? 'librarian/manage_borrowings' : 'admin/manage_borrowings';
                return redirect()->to(base_url($redirect));
            }

            $returnDate = date('Y-m-d');
            $borrowingModel->update($borrowingID, [
                'return_date' => $returnDate,
                'status'      => 'returned',
                'returned_to' => $this->session->get('userID'),
                'updated_at'  => date('Y-m-d H:i:s')
            ]);

            $bookModel = new BookModel();
            $bookModel->incrementAvailableCopies($borrowing['book_id']);

            $fineModel = new FineModel();
            $fineAmount = $fineModel->calculateFine($borrowing['due_date'], $returnDate);
            if ($fineAmount > 0) {
                $fineModel->insert([
                    'borrowing_id' => $borrowingID,
                    'user_id'      => $borrowing['user_id'],
                    'amount'       => $fineAmount,
                    'reason'       => 'Overdue - ' . ceil($fineAmount / 10) . ' day(s) late',
                    'status'       => 'unpaid',
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s')
                ]);
                $this->createNotification($borrowing['user_id'], 
                    "Overdue fine: ₱" . number_format($fineAmount, 2) . ". Please settle.");
            }

            $book = $bookModel->find($borrowing['book_id']);
            $this->createNotification($borrowing['user_id'], "Book '{$book['title']}' returned successfully.");
            $msg = 'Book returned!' . ($fineAmount > 0 ? " Fine: ₱" . number_format($fineAmount, 2) : '');
            $this->session->setFlashdata('success', $msg);
            
            $redirect = $userRole === 'librarian' ? 'librarian/manage_borrowings' : 'admin/manage_borrowings';
            return redirect()->to(base_url($redirect));
        }

        $statusFilter = $this->request->getGet('status');
        $filters = [];
        if ($statusFilter && in_array($statusFilter, ['borrowed', 'returned', 'overdue'])) {
            $filters['status'] = $statusFilter;
        }

        $borrowings = $borrowingModel->getBorrowingsWithDetails($filters);
        $bookModel = new BookModel();
        $availableBooks = $bookModel->getAvailableBooks();
        $borrowers = $this->db->table('users')
            ->whereIn('role', ['student', 'teacher'])
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        $data = [
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'email'  => $this->session->get('email'),
                'role'   => $this->session->get('role')
            ],
            'title' => 'Manage Borrowings',
            'borrowings' => $borrowings,
            'availableBooks' => $availableBooks,
            'borrowers' => $borrowers,
            'statusFilter' => $statusFilter
        ];
        
        $viewPath = $userRole === 'librarian' ? 'librarian/manage_borrowings' : 'admin/manage_borrowings';
        return view($viewPath, $data);
    }

    public function manageFines()
    {
        if ($this->session->get('isLoggedIn') !== true) return redirect()->to(base_url('login'));
        $userRole = $this->session->get('role');
        if (!in_array($userRole, ['admin', 'librarian'])) return redirect()->to(base_url('dashboard'));

        $fineModel = new FineModel();
        $action = $this->request->getGet('action');
        $fineID = $this->request->getGet('id');

        if ($action === 'pay' && $fineID) {
            $fine = $fineModel->find($fineID);
            if ($fine && $fine['status'] === 'unpaid') {
                $fineModel->update($fineID, [
                    'status' => 'paid',
                    'paid_date' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $this->createNotification($fine['user_id'], 
                    "Fine of ₱" . number_format($fine['amount'], 2) . " marked as paid.");
                $this->session->setFlashdata('success', 'Fine marked as paid!');
            }
            $redirect = $userRole === 'librarian' ? 'librarian/manage_fines' : 'admin/manage_fines';
            return redirect()->to(base_url($redirect));
        }

        $statusFilter = $this->request->getGet('status');
        $filters = [];
        if ($statusFilter && in_array($statusFilter, ['unpaid', 'paid'])) {
            $filters['status'] = $statusFilter;
        }

        $data = [
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'email'  => $this->session->get('email'),
                'role'   => $this->session->get('role')
            ],
            'title' => 'Manage Fines',
            'fines' => $fineModel->getFinesWithDetails($filters),
            'statusFilter' => $statusFilter
        ];

        $viewPath = $userRole === 'librarian' ? 'librarian/manage_fines' : 'admin/manage_fines';
        return view($viewPath, $data);
    }

    public function catalog()
    {
        if ($this->session->get('isLoggedIn') !== true) return redirect()->to(base_url('login'));
        $userRole = $this->session->get('role');
        if (!in_array($userRole, ['student', 'teacher'])) return redirect()->to(base_url('dashboard'));

        $bookModel = new BookModel();
        $categoryModel = new BookCategoryModel();
        $borrowingModel = new BorrowingModel();
        $reservationModel = new ReservationModel();
        $userId = $this->session->get('userID');

        $search = $this->request->getGet('search');
        $categoryFilter = $this->request->getGet('category');
        
        if ($search) {
            $books = $bookModel->searchBooks($search);
        } elseif ($categoryFilter) {
            $books = $bookModel->getBooksByCategory($categoryFilter);
        } else {
            $books = $bookModel->getBooksWithCategory();
        }

        foreach ($books as &$book) {
            $book['is_borrowed'] = $borrowingModel->isBookBorrowedByUser($book['id'], $userId);
            $book['is_reserved'] = $reservationModel->hasActiveReservation($book['id'], $userId);
        }

        $data = [
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'email'  => $this->session->get('email'),
                'role'   => $this->session->get('role')
            ],
            'title' => 'Book Catalog - Maruhom Library',
            'books' => $books,
            'categories' => $categoryModel->findAll(),
            'search' => $search,
            'categoryFilter' => $categoryFilter
        ];

        return view($userRole === 'teacher' ? 'teacher/catalog' : 'student/catalog', $data);
    }

    public function myBorrowings()
    {
        if ($this->session->get('isLoggedIn') !== true) return redirect()->to(base_url('login'));
        $userRole = $this->session->get('role');
        if (!in_array($userRole, ['student', 'teacher'])) return redirect()->to(base_url('dashboard'));

        $userId = $this->session->get('userID');
        $borrowingModel = new BorrowingModel();
        $borrowingModel->updateOverdueStatuses();
        $fineModel = new FineModel();

        $data = [
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'email'  => $this->session->get('email'),
                'role'   => $this->session->get('role')
            ],
            'title' => 'My Borrowings - Maruhom Library',
            'activeBorrowings' => $borrowingModel->getUserActiveBorrowings($userId),
            'borrowingHistory' => $borrowingModel->getUserBorrowingHistory($userId),
            'unpaidFines' => $fineModel->getUserUnpaidFines($userId),
            'totalUnpaidFines' => $fineModel->getUserTotalUnpaidFines($userId)
        ];

        return view($userRole === 'teacher' ? 'teacher/my_borrowings' : 'student/my_borrowings', $data);
    }

    public function reserveBook()
    {
        if ($this->session->get('isLoggedIn') !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Login required.']);
        }
        $userRole = $this->session->get('role');
        if (!in_array($userRole, ['student', 'teacher'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $bookId = $this->request->getPost('book_id');
        $userId = $this->session->get('userID');
        $reservationModel = new ReservationModel();

        if ($reservationModel->hasActiveReservation($bookId, $userId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Already reserved.', 'csrf_hash' => csrf_hash()]);
        }

        $fineModel = new FineModel();
        if ($fineModel->getUserTotalUnpaidFines($userId) > 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unpaid fines exist.', 'csrf_hash' => csrf_hash()]);
        }

        if ($reservationModel->insert([
            'book_id'          => $bookId,
            'user_id'          => $userId,
            'reservation_date' => date('Y-m-d H:i:s'),
            'expiry_date'      => date('Y-m-d H:i:s', strtotime('+48 hours')),
            'status'           => 'pending',
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s')
        ])) {
            $bookModel = new BookModel();
            $book = $bookModel->find($bookId);
            $librarians = $this->db->table('users')->whereIn('role', ['librarian', 'admin'])->get()->getResultArray();
            foreach ($librarians as $lib) {
                $this->createNotification($lib['id'], $this->session->get('name') . " reserved '{$book['title']}'");
            }
            return $this->response->setJSON(['success' => true, 'message' => 'Reserved! Valid for 48 hours.', 'csrf_hash' => csrf_hash()]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Reservation failed.', 'csrf_hash' => csrf_hash()]);
    }

    public function cancelReservation()
    {
        if ($this->session->get('isLoggedIn') !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Login required.']);
        }

        $reservationId = $this->request->getPost('reservation_id');
        $userId = $this->session->get('userID');
        $reservationModel = new ReservationModel();

        $reservation = $reservationModel->find($reservationId);
        if (!$reservation || $reservation['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid reservation.', 'csrf_hash' => csrf_hash()]);
        }

        $reservationModel->update($reservationId, ['status' => 'cancelled', 'updated_at' => date('Y-m-d H:i:s')]);
        return $this->response->setJSON(['success' => true, 'message' => 'Reservation cancelled.', 'csrf_hash' => csrf_hash()]);
    }
}
