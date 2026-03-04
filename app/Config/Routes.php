<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public routes
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Auth routes
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/captcha/image', 'CaptchaController::image');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');

// Admin routes
$routes->get('/admin/manage_users', 'Auth::manageUsers');
$routes->post('/admin/manage_users', 'Auth::manageUsers');
$routes->get('/admin/manage_books', 'Auth::manageBooks');
$routes->post('/admin/manage_books', 'Auth::manageBooks');
$routes->get('/admin/manage_borrowings', 'Auth::manageBorrowings');
$routes->post('/admin/manage_borrowings', 'Auth::manageBorrowings');
$routes->get('/admin/manage_fines', 'Auth::manageFines');
$routes->get('/admin/manage_categories', 'Auth::manageCategories');
$routes->post('/admin/manage_categories', 'Auth::manageCategories');

// Librarian routes
$routes->get('/librarian/manage_books', 'Auth::manageBooks');
$routes->post('/librarian/manage_books', 'Auth::manageBooks');
$routes->get('/librarian/manage_borrowings', 'Auth::manageBorrowings');
$routes->post('/librarian/manage_borrowings', 'Auth::manageBorrowings');
$routes->get('/librarian/manage_fines', 'Auth::manageFines');
$routes->get('/librarian/manage_categories', 'Auth::manageCategories');
$routes->post('/librarian/manage_categories', 'Auth::manageCategories');

// Teacher routes
$routes->get('/teacher/catalog', 'Auth::catalog');
$routes->get('/teacher/my_borrowings', 'Auth::myBorrowings');

// Student routes
$routes->get('/student/catalog', 'Auth::catalog');
$routes->get('/student/my_borrowings', 'Auth::myBorrowings');

// Book reservation (AJAX)
$routes->post('/book/reserve', 'Auth::reserveBook');
$routes->post('/book/cancel_reservation', 'Auth::cancelReservation');

// Notification routes
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');
