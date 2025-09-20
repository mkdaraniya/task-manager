<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TaskManager') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            --info-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
            --sidebar-active: #3498db;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --border-radius: 15px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            background: var(--sidebar-bg) !important;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            transition: var(--transition);
            overflow-y: auto;
            width: 260px;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        .sidebar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.2rem;
            text-decoration: none !important;
            padding: 20px 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            position: relative;
        }

        .sidebar-brand::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-gradient);
            opacity: 0.1;
            transition: var(--transition);
        }

        .sidebar-brand:hover::before {
            opacity: 0.2;
        }

        .sidebar-brand .brand-icon {
            font-size: 1.5rem;
            margin-right: 8px;
            transition: var(--transition);
        }

        .sidebar.collapsed .sidebar-brand .brand-text {
            display: none;
        }

        .sidebar.collapsed .sidebar-brand .brand-icon {
            margin-right: 0;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 15px;
            margin: 3px 10px;
            border-radius: 10px;
            transition: var(--transition);
            position: relative;
            display: flex;
            align-items: center;
            font-weight: 500;
            overflow: hidden;
            font-size: 0.9rem;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transform-origin: left;
            transition: var(--transition);
            z-index: -1;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            transform: translateX(3px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .sidebar .nav-link:hover::before,
        .sidebar .nav-link.active::before {
            transform: scaleX(1);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1rem;
            width: 18px;
            text-align: center;
            transition: var(--transition);
        }

        .sidebar.collapsed .nav-link {
            padding: 12px;
            margin: 3px 5px;
            text-align: center;
            justify-content: center;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .sidebar.collapsed .nav-link .nav-text {
            display: none;
        }

        /* Main Content Area */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            width: calc(100% - 260px);
        }

        .main-content.expanded {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        /* Header Styles */
        .main-header {
            background: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            border-radius: 0 0 20px 20px;
            padding: 15px 25px;
            margin-bottom: 25px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header-brand {
            font-weight: 600;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.3rem;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: #6c757d;
            font-size: 1.2rem;
            padding: 8px;
            border-radius: 8px;
            transition: var(--transition);
        }

        .sidebar-toggle:hover {
            background: #f8f9fa;
            color: #495057;
        }

        /* Search Bar */
        .search-container {
            position: relative;
            max-width: 350px;
        }

        .search-container .form-control {
            background: #f8f9fa;
            border: none;
            border-radius: 20px;
            padding: 10px 40px 10px 15px;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .search-container .form-control:focus {
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-color: transparent;
        }

        .search-container .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* User Avatar */
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: var(--transition);
        }

        .user-avatar:hover {
            transform: scale(1.05);
        }

        /* Notification Styles */
        .notification-btn {
            background: none;
            border: none;
            position: relative;
            padding: 8px;
            border-radius: 8px;
            transition: var(--transition);
            color: #6c757d;
        }

        .notification-btn:hover {
            background: #f8f9fa;
            color: #495057;
        }

        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--danger-gradient);
            border: 2px solid white;
            min-width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            color: white;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Dropdown Enhancements */
        .dropdown-menu {
            border: none;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 10px;
            margin-top: 10px;
            min-width: 260px;
        }

        .dropdown-item {
            padding: 10px 12px;
            border-radius: 6px;
            transition: var(--transition);
            border: none;
            background: none;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            color: #495057;
        }

        .dropdown-header {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Alert Enhancements */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 12px 18px;
            margin-bottom: 15px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
            font-size: 0.9rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1, #b3d7ff);
            color: #0c5460;
        }

        /* Footer Styles */
        .main-footer {
            background: white;
            border-top: 1px solid #e9ecef;
            padding: 20px 25px;
            text-align: center;
            border-radius: 20px 20px 0 0;
            margin-top: auto;
            box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.05);
            font-size: 0.9rem;
        }

        .footer-heart {
            color: #e74c3c;
            animation: heartbeat 2s ease-in-out infinite;
        }

        @keyframes heartbeat {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -260px;
                transition: var(--transition);
            }

            .sidebar.show {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .main-content.expanded {
                margin-left: 0;
                width: 100%;
            }

            .main-header {
                border-radius: 0;
                margin-bottom: 15px;
                padding: 12px 15px;
            }

            .search-container {
                display: none;
            }

            .sidebar-brand {
                font-size: 1.1rem;
                padding: 15px 10px;
            }

            .user-avatar {
                width: 32px;
                height: 32px;
            }
        }

        @media (max-width: 576px) {
            .main-header {
                padding: 10px 12px;
            }

            .main-content {
                padding: 0 5px;
            }

            .user-avatar {
                width: 30px;
                height: 30px;
            }

            .sidebar-brand {
                font-size: 1rem;
                padding: 12px 8px;
            }

            .sidebar .nav-link {
                padding: 10px 12px;
                font-size: 0.85rem;
            }
        }

        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Utility Classes */
        .text-gradient {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-gradient {
            background: var(--primary-gradient);
            border: none;
            color: white;
            transition: var(--transition);
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
    </style>
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <nav class="sidebar" id="sidebar">
                <div class="sidebar-sticky">
                    <!-- Brand -->
                    <a href="{{ route('dashboard') }}" class="sidebar-brand">
                        <i class="bi bi-check2-square brand-icon"></i>
                        <span class="brand-text">TaskManager</span>
                    </a>

                    <!-- Navigation Links -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('projects.index') }}"
                                class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                                <i class="bi bi-folder"></i>
                                <span class="nav-text">Projects</span>
                            </a>
                        </li>
                        <li class="nav-item has-submenu">
                            <a href="{{ route('tasks.index') }}" class="nav-link">
                                <i class="bi bi-list-task"></i>
                                <span class="nav-text">Tasks</span>
                                <i class="bi bi-chevron-down ms-auto"></i>
                            </a>

                            <ul class="submenu list-unstyled ms-4">
                                @php
                                    $boards = \App\Models\Board::with('project')->orderBy('name')->get();
                                @endphp

                                @forelse ($boards as $board)
                                    <li>
                                        <a href="{{ route('boards.kanban', $board) }}"
                                            class="nav-link {{ request()->routeIs('boards.kanban') && request()->board?->id === $board->id ? 'active' : '' }}">
                                            <i class="bi bi-kanban"></i>
                                            {{ $board->name }}
                                            <small
                                                class="text-muted">({{ $board->project->name ?? 'No Project' }})</small>
                                        </a>
                                    </li>
                                @empty
                                    <li><span class="text-muted">No boards yet</span></li>
                                @endforelse
                            </ul>
                        </li>


                        <li class="nav-item">
                            <a href="{{ route('teams.index') }}" class="nav-link">
                                <i class="bi bi-people"></i>
                                <span class="nav-text">Team</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.index') }}"
                                class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <i class="bi bi-graph-up"></i>
                                <span class="nav-text">Reports</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('calendar.index') }}" class="nav-link">
                                <i class="bi bi-calendar3"></i>
                                <span class="nav-text">Calendar</span>
                            </a>
                        </li>

                        @if (Auth::user()->hasRole('admin'))
                            <li class="nav-item mt-2">
                                <div class="px-3 py-1">
                                    <small class="text-muted text-uppercase fw-bold"
                                        style="font-size: 0.65rem; letter-spacing: 1px;">
                                        <span class="nav-text">Admin</span>
                                    </small>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                                    <i class="bi bi-gear"></i>
                                    <span class="nav-text">Settings</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="bi bi-people-fill"></i>
                                    <span class="nav-text">Users</span>
                                </a>
                            </li>
                        @endif

                        <!-- Help Section -->
                        <li class="nav-item mt-3">
                            <div class="px-3 py-1">
                                <small class="text-muted text-uppercase fw-bold"
                                    style="font-size: 0.65rem; letter-spacing: 1px;">
                                    <span class="nav-text">Support</span>
                                </small>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-question-circle"></i>
                                <span class="nav-text">Help Center</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-chat-dots"></i>
                                <span class="nav-text">Contact</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="main-content" id="mainContent">
                <!-- Header -->
                <nav class="main-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Left Section -->
                        <div class="d-flex align-items-center">
                            <button class="sidebar-toggle me-3" onclick="toggleSidebar()">
                                <i class="bi bi-list"></i>
                            </button>
                            <span class="header-brand d-none d-md-block">Dashboard</span>
                        </div>

                        <!-- Center Section - Search -->
                        {{-- <div class="search-container d-none d-lg-block">
                            <input type="text" class="form-control"
                                placeholder="Search projects, tasks, team members...">
                            <i class="bi bi-search search-icon"></i>
                        </div> --}}

                        <!-- Right Section -->
                        <div class="d-flex align-items-center">
                            <!-- Mobile Search Button -->
                            <button class="btn btn-link d-lg-none me-2" onclick="toggleMobileSearch()">
                                <i class="bi bi-search"></i>
                            </button>

                            <!-- Notifications -->
                            <div class="dropdown me-3">
                                <button class="notification-btn" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-bell fs-5"></i>
                                    @if (Auth::user()->unreadNotifications->count() > 0)
                                        <span
                                            class="notification-badge">{{ Auth::user()->unreadNotifications->count() }}</span>
                                    @endif
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                                        <span>Notifications</span>
                                        @if (Auth::user()->unreadNotifications->count() > 0)
                                            <span
                                                class="badge bg-primary">{{ Auth::user()->unreadNotifications->count() }}</span>
                                        @endif
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>

                                    @forelse(Auth::user()->unreadNotifications->take(5) as $notification)
                                        <li>
                                            <a class="dropdown-item" href="{{ $notification->data['url'] ?? '#' }}">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-3 mt-1">
                                                        <i class="bi bi-info-circle text-primary"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-semibold mb-1">
                                                            {{ $notification->data['title'] ?? 'New Notification' }}
                                                        </div>
                                                        <div class="text-muted small mb-1">
                                                            {{ $notification->data['message'] ?? 'You have a new notification' }}
                                                        </div>
                                                        <div class="text-muted small">
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        @if (!$loop->last)
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                        @endif
                                    @empty
                                        <li class="dropdown-item text-muted text-center py-4">
                                            <i class="bi bi-bell-slash mb-2 d-block"></i>
                                            No new notifications
                                        </li>
                                    @endforelse

                                    @if (Auth::user()->unreadNotifications->count() > 0)
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-center text-primary" href="#">
                                                <i class="bi bi-arrow-right me-1"></i>View All Notifications
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <!-- User Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-link d-flex align-items-center text-decoration-none p-0"
                                    data-bs-toggle="dropdown">
                                    <img src="{{ Auth::user()->profile->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=667eea&color=fff&bold=true' }}"
                                        alt="Avatar" class="user-avatar me-3">
                                    <div class="text-start d-none d-md-block">
                                        <div class="fw-semibold text-dark">{{ Auth::user()->name }}</div>
                                        <div class="text-muted small">{{ Auth::user()->email }}</div>
                                    </div>
                                    <i class="bi bi-chevron-down ms-2 text-muted d-none d-md-block"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="dropdown-header">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ Auth::user()->profile->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=667eea&color=fff&bold=true' }}"
                                                alt="Avatar" class="user-avatar me-3"
                                                style="width: 40px; height: 40px;">
                                            <div>
                                                <div class="fw-semibold">{{ Auth::user()->name }}</div>
                                                <div class="text-muted small">{{ Auth::user()->email }}</div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.profile') }}">
                                            <i class="bi bi-person me-2"></i>My Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bi bi-gear me-2"></i>Account Settings
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bi bi-palette me-2"></i>Preferences
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.logout-all') }}">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout All Devices
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-power me-2"></i>Sign Out
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Search Bar -->
                    <div class="search-container d-lg-none mt-3" id="mobileSearch" style="display: none !important;">
                        <input type="text" class="form-control" placeholder="Search...">
                        <i class="bi bi-search search-icon"></i>
                    </div>
                </nav>

                <!-- Content Area -->
                <div class="flex-grow-1 px-3">
                    <!-- Alert Messages -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Warning!</strong> {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Info!</strong> {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Main Content -->
                    @yield('content')
                </div>

                <!-- Footer -->
                <footer class="main-footer">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <span class="text-muted">
                                    &copy; {{ date('Y') }} TaskManager. Made with
                                    <span class="footer-heart">♥</span>
                                    for productivity enthusiasts.
                                </span>
                            </div>
                            <div class="col-md-6 text-md-end text-center mt-2 mt-md-0">
                                <div class="d-flex justify-content-md-end justify-content-center align-items-center">
                                    <span class="text-muted me-3">Version 1.0.0</span>
                                    <a href="#" class="text-muted me-2" title="Help"><i
                                            class="bi bi-question-circle"></i></a>
                                    <a href="#" class="text-muted me-2" title="Documentation"><i
                                            class="bi bi-book"></i></a>
                                    <a href="#" class="text-muted" title="Support"><i
                                            class="bi bi-chat-dots"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay d-md-none" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JavaScript -->
    <script>
        // Global Variables
        let sidebarCollapsed = false;
        let isMobile = window.innerWidth < 768;

        // DOM Ready
        document.addEventListener('DOMContentLoaded', function() {
            initializeLayout();
            initializeTooltips();
            initializeAlerts();
            handleResponsiveLayout();
        });

        // Initialize Layout
        function initializeLayout() {
            // Hide loading overlay
            setTimeout(() => {
                document.getElementById('loadingOverlay').classList.remove('show');
            }, 500);

            // Set active navigation
            setActiveNavigation();

            // Initialize search functionality
            initializeSearch();
        }

        // Sidebar Toggle Function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            if (isMobile) {
                // Mobile: Show/Hide sidebar
                sidebar.classList.toggle('show');
                document.body.classList.toggle('sidebar-open');

                // Add/remove overlay
                let overlay = document.getElementById('sidebarOverlay');
                if (!overlay && sidebar.classList.contains('show')) {
                    overlay = document.createElement('div');
                    overlay.id = 'sidebarOverlay';
                    overlay.className = 'position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-md-none';
                    overlay.style.zIndex = '999';
                    overlay.onclick = closeMobileSidebar;
                    document.body.appendChild(overlay);
                }

                if (!sidebar.classList.contains('show') && overlay) {
                    overlay.remove();
                }
            } else {
                // Desktop: Collapse/Expand sidebar
                sidebarCollapsed = !sidebarCollapsed;
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');

                // Save preference
                localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
            }
        }

        // Close Mobile Sidebar
        function closeMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.remove('show');
            document.body.classList.remove('sidebar-open');

            if (overlay) {
                overlay.classList.remove('show');
                setTimeout(() => overlay.remove(), 300);
            }
        }

        // Toggle Mobile Search
        function toggleMobileSearch() {
            const mobileSearch = document.getElementById('mobileSearch');
            const isVisible = mobileSearch.style.display !== 'none';

            if (isVisible) {
                mobileSearch.style.display = 'none';
            } else {
                mobileSearch.style.display = 'block';
                mobileSearch.querySelector('input').focus();
            }
        }

        // Set Active Navigation
        function setActiveNavigation() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.sidebar .nav-link');

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        }

        // Initialize Search
        function initializeSearch() {
            const searchInputs = document.querySelectorAll('.search-container input');

            searchInputs.forEach(input => {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        performSearch(this.value);
                    }
                });

                // Add search suggestions (placeholder for future implementation)
                input.addEventListener('input', function(e) {
                    // Implement search suggestions here
                    console.log('Searching for:', e.target.value);
                });
            });
        }

        // Perform Search
        function performSearch(query) {
            if (query.trim() === '') return;

            // Show loading
            showLoading();

            // Simulate search (replace with actual search implementation)
            setTimeout(() => {
                hideLoading();
                // Redirect to search results or show results
                console.log('Search results for:', query);

                // Example: Redirect to search page
                // window.location.href = `/search?q=${encodeURIComponent(query)}`;
            }, 1000);
        }

        // Initialize Tooltips
        function initializeTooltips() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Initialize Alerts
        function initializeAlerts() {
            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        }

        // Handle Responsive Layout
        function handleResponsiveLayout() {
            window.addEventListener('resize', function() {
                const newIsMobile = window.innerWidth < 768;

                if (newIsMobile !== isMobile) {
                    isMobile = newIsMobile;

                    if (isMobile) {
                        // Switching to mobile
                        const sidebar = document.getElementById('sidebar');
                        const mainContent = document.getElementById('mainContent');

                        sidebar.classList.remove('collapsed');
                        mainContent.classList.remove('expanded');
                        closeMobileSidebar();
                    } else {
                        // Switching to desktop
                        const sidebar = document.getElementById('sidebar');
                        const mainContent = document.getElementById('mainContent');

                        sidebar.classList.remove('show');
                        document.body.classList.remove('sidebar-open');

                        // Restore collapsed state from localStorage
                        const savedCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                        if (savedCollapsed) {
                            sidebar.classList.add('collapsed');
                            mainContent.classList.add('expanded');
                            sidebarCollapsed = true;
                        }
                    }
                }
            });
        }

        // Loading Functions
        function showLoading() {
            document.getElementById('loadingOverlay').classList.add('show');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('show');
        }

        // Enhanced SweetAlert2 Functions
        function showSuccessAlert(title, message, callback = null) {
            Swal.fire({
                icon: 'success',
                title: title,
                text: message,
                background: '#fff',
                color: '#2c3e50',
                confirmButtonColor: '#667eea',
                customClass: {
                    popup: 'border-0 shadow-lg',
                    confirmButton: 'btn-gradient'
                }
            }).then((result) => {
                if (callback && result.isConfirmed) {
                    callback();
                }
            });
        }

        function showErrorAlert(title, message) {
            Swal.fire({
                icon: 'error',
                title: title,
                text: message,
                background: '#fff',
                color: '#2c3e50',
                confirmButtonColor: '#ff6b6b',
                customClass: {
                    popup: 'border-0 shadow-lg',
                    confirmButton: 'btn btn-danger'
                }
            });
        }

        function showConfirmAlert(title, message, confirmCallback, cancelCallback = null) {
            Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'Cancel',
                background: '#fff',
                color: '#2c3e50',
                customClass: {
                    popup: 'border-0 shadow-lg',
                    confirmButton: 'btn-gradient',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed && confirmCallback) {
                    confirmCallback();
                } else if (result.isDismissed && cancelCallback) {
                    cancelCallback();
                }
            });
        }

        // Keyboard Shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('.search-container input:not([style*="display: none"])');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }

            // Ctrl/Cmd + B for sidebar toggle
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                toggleSidebar();
            }

            // Escape key to close mobile sidebar
            if (e.key === 'Escape' && isMobile) {
                closeMobileSidebar();
            }
        });

        // AJAX Setup for Laravel
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                showLoading();
            },
            complete: function() {
                hideLoading();
            },
            error: function(xhr, status, error) {
                hideLoading();

                let message = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                showErrorAlert('Error', message);
            }
        });

        // Notification Mark as Read
        function markNotificationAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    // Update notification badge
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        const count = parseInt(badge.textContent) - 1;
                        if (count <= 0) {
                            badge.remove();
                        } else {
                            badge.textContent = count;
                        }
                    }
                }
            }).catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }

        // Initialize theme from localStorage
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.body.setAttribute('data-theme', savedTheme);
        }

        // Theme toggle function (for future use)
        function toggleTheme() {
            const currentTheme = document.body.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        // Restore sidebar state on page load
        window.addEventListener('load', function() {
            if (!isMobile) {
                const savedCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (savedCollapsed) {
                    const sidebar = document.getElementById('sidebar');
                    const mainContent = document.getElementById('mainContent');

                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                    sidebarCollapsed = true;
                }
            }
        });
    </script>

    <!-- Additional Scripts Stack -->
    @stack('scripts')
</body>

</html>
