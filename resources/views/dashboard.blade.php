@extends('layouts.app')

@section('content')
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            --info-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --border-radius: 15px;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .dashboard-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px 25px;
            text-align: center;
            box-shadow: var(--card-shadow);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .stats-card.primary::before {
            background: var(--primary-gradient);
        }

        .stats-card.success::before {
            background: var(--success-gradient);
        }

        .stats-card.warning::before {
            background: var(--warning-gradient);
        }

        .stats-card.danger::before {
            background: var(--danger-gradient);
        }

        .stats-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.8rem;
            color: white;
        }

        .stats-card.primary .stats-icon {
            background: var(--primary-gradient);
        }

        .stats-card.success .stats-icon {
            background: var(--success-gradient);
        }

        .stats-card.warning .stats-icon {
            background: var(--warning-gradient);
        }

        .stats-card.danger .stats-icon {
            background: var(--danger-gradient);
        }

        .stats-number {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        .stats-label {
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .activity-item {
            padding: 15px 20px;
            border-left: 4px solid transparent;
            margin-bottom: 12px;
            background: #f8f9fa;
            border-radius: 0 10px 10px 0;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .activity-item.primary {
            border-left-color: #667eea;
        }

        .activity-item.success {
            border-left-color: #4facfe;
        }

        .activity-item.warning {
            border-left-color: #ff9a9e;
        }

        .activity-item.danger {
            border-left-color: #ff6b6b;
        }

        .quick-action-btn {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 25px 20px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #495057;
            display: block;
            height: 100%;
        }

        .quick-action-btn:hover {
            border-color: #667eea;
            background: var(--primary-gradient);
            color: white;
            transform: translateY(-3px);
            box-shadow: var(--card-shadow);
            text-decoration: none;
        }

        .quick-action-btn i {
            font-size: 2.2rem;
            margin-bottom: 12px;
            display: block;
        }

        .custom-progress {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }

        .custom-progress .progress-bar {
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        .welcome-section {
            background: var(--primary-gradient);
            color: white;
            border-radius: var(--border-radius);
            padding: 40px 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .welcome-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .badge {
            border-radius: 20px;
            padding: 6px 12px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .badge.bg-primary {
            background: var(--primary-gradient) !important;
        }

        .badge.bg-success {
            background: var(--success-gradient) !important;
        }

        .badge.bg-warning {
            background: var(--warning-gradient) !important;
        }

        .badge.bg-danger {
            background: var(--danger-gradient) !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
        }

        .chart-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--card-shadow);
            height: 350px;
        }

        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 20px;
                padding: 25px 20px;
            }

            .welcome-section {
                padding: 30px 20px;
                text-align: center;
            }

            .quick-action-btn {
                padding: 20px 15px;
            }

            .chart-container {
                height: 300px;
            }
        }
    </style>

    <div class="container-fluid">
        <!-- Welcome Section -->
        <div class="welcome-section fade-in-up">
            <div class="welcome-content">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="mb-2 fw-bold">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                        <p class="mb-0 opacity-90">Here's what's happening with your projects and team today.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <a href="{{ route('projects.index') }}" class="btn btn-light btn-lg fw-semibold">
                            <i class="bi bi-plus-circle me-2"></i>Create New Project
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Row -->
        <!-- Stats Cards Row -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card primary fade-in-up">
                    <div class="stats-icon">
                        <i class="bi bi-folder2-open"></i>
                    </div>
                    <div class="stats-number counter" data-count="{{ $stats['projects'] }}">0</div>
                    <div class="stats-label">Active Projects</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card success fade-in-up" style="animation-delay: 0.1s;">
                    <div class="stats-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stats-number counter" data-count="{{ $stats['completed_tasks'] }}">0</div>
                    <div class="stats-label">Completed Tasks</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card warning fade-in-up" style="animation-delay: 0.2s;">
                    <div class="stats-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="stats-number counter" data-count="{{ $stats['pending_tasks'] }}">0</div>
                    <div class="stats-label">Pending Tasks</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card danger fade-in-up" style="animation-delay: 0.3s;">
                    <div class="stats-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stats-number counter" data-count="{{ $stats['team_members'] }}">0</div>
                    <div class="stats-label">Team Members</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3 fw-semibold">
                    <i class="bi bi-lightning-charge text-primary me-2"></i>Quick Actions
                </h4>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <a href="{{ route('tasks.index') }}" class="quick-action-btn">
                    <i class="bi bi-plus-circle-fill"></i>
                    <div class="fw-semibold">New Task</div>
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <a href="{{ route('projects.create') }}" class="quick-action-btn">
                    <i class="bi bi-folder-plus"></i>
                    <div class="fw-semibold">New Project</div>
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <a href="{{ route('teams.create') }}" class="quick-action-btn">
                    <i class="bi bi-person-plus-fill"></i>
                    <div class="fw-semibold">Invite User</div>
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <a href="{{ route('reports.index') }}" class="quick-action-btn">
                    <i class="bi bi-graph-up-arrow"></i>
                    <div class="fw-semibold">View Reports</div>
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <a href="{{ route('calendar.index') }}" class="quick-action-btn">
                    <i class="bi bi-calendar3-event"></i>
                    <div class="fw-semibold">Schedule</div>
                </a>
            </div>
            {{-- <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <a href="{{ route('users.settings') }}" class="quick-action-btn">
                    <i class="bi bi-gear-fill"></i>
                    <div class="fw-semibold">Settings</div>
                </a>
            </div> --}}
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Recent Projects -->
            <div class="col-lg-12 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold">
                                <i class="bi bi-folder2 text-primary me-2"></i>Recent Projects
                            </h5>
                            <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">
                                View All <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 py-3">Project</th>
                                        <th class="border-0 py-3">Progress</th>
                                        <th class="border-0 py-3">Team</th>
                                        <th class="border-0 py-3">Due Date</th>
                                        <th class="border-0 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentProjects as $project)
                                        <tr>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary rounded-3 me-3 d-flex align-items-center justify-content-center"
                                                        style="width: 45px; height: 45px;">
                                                        <i class="bi bi-folder2 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ $project['name'] }}</div>
                                                        <small class="text-muted">{{ $project['description'] }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <div class="custom-progress mb-1">
                                                    <div class="progress-bar bg-primary"
                                                        style="width: {{ $project['progress'] }}%"></div>
                                                </div>
                                                <small class="text-muted fw-medium">{{ $project['progress'] }}%
                                                    Complete</small>
                                            </td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    @foreach ($project['team'] as $member)
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member['name']) }}&background=667eea&color=fff"
                                                            class="user-avatar me-1" alt="User">
                                                    @endforeach
                                                    @if ($project['team_count'] > 2)
                                                        <span
                                                            class="badge bg-light text-dark">+{{ $project['team_count'] - 2 }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <div class="fw-medium">{{ $project['due_date'] }}</div>
                                                <small
                                                    class="text-muted">{{ $project['due_date'] == 'N/A' ? '' : now()->diffInDays($project['due_date']) . ' days left' }}</small>
                                            </td>
                                            <td class="py-3">
                                                <span
                                                    class="badge bg-{{ $project['status'] == 'Active' ? 'primary' : ($project['status'] == 'Completed' ? 'success' : 'warning') }}">{{ $project['status'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="col-lg-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-pie-chart text-primary me-2"></i>Tasks by Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="tasksByStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-bar-chart text-primary me-2"></i>Project Progress
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="projectProgressChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Team Performance -->
            <div class="col-lg-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-activity text-primary me-2"></i>Recent Activity
                        </h5>
                    </div>
                    <div class="card-body" id="recent-activities">
                        @foreach ($recentActivities as $index => $activity)
                            <div class="activity-item {{ ['primary', 'success', 'warning', 'danger'][$index % 4] }}">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($activity['user_name']) }}&background=667eea&color=fff"
                                        class="user-avatar me-3" alt="User">
                                    <div class="flex-grow-1">
                                        <div class="fw-medium small">{{ $activity['description'] }}</div>
                                        <div class="text-muted small">{{ $activity['created_at'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-outline-primary">View All
                                Activity</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-trophy text-primary me-2"></i>Team Performance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-medium">Tasks Completed</span>
                                <span
                                    class="fw-bold text-success">{{ round($teamPerformance['tasks_completed']) }}%</span>
                            </div>
                            <div class="custom-progress">
                                <div class="progress-bar bg-success"
                                    style="width: {{ $teamPerformance['tasks_completed'] }}%"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-medium">Project Delivery</span>
                                <span
                                    class="fw-bold text-primary">{{ round($teamPerformance['project_delivery']) }}%</span>
                            </div>
                            <div class="custom-progress">
                                <div class="progress-bar bg-primary"
                                    style="width: {{ $teamPerformance['project_delivery'] }}%"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-medium">Team Satisfaction</span>
                                <span
                                    class="fw-bold text-warning">{{ round($teamPerformance['team_satisfaction']) }}%</span>
                            </div>
                            <div class="custom-progress">
                                <div class="progress-bar bg-warning"
                                    style="width: {{ $teamPerformance['team_satisfaction'] }}%"></div>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">Detailed
                                Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Animations, Charts, and Reverb -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Counter Animation
            function animateCounters() {
                const counters = document.querySelectorAll('.counter');
                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-count'));
                    const duration = 2000;
                    const increment = target / (duration / 16);
                    let current = 0;

                    const updateCounter = () => {
                        if (current < target) {
                            current += increment;
                            counter.textContent = Math.ceil(current);
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent = target;
                        }
                    };
                    updateCounter();
                });
            }

            // Trigger counter animation when stats section is visible
            const observerOptions = {
                threshold: 0.5
            };
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            const statsSection = document.querySelector('.stats-card');
            if (statsSection) observer.observe(statsSection);

            // Initialize Charts
            const tasksByStatusCtx = document.getElementById('tasksByStatusChart').getContext('2d');
            const tasksByStatusChart = new Chart(tasksByStatusCtx, {
                type: 'pie',
                data: {
                    labels: @json($chartData['tasks_by_status']['labels']),
                    datasets: [{
                        data: @json($chartData['tasks_by_status']['data']),
                        backgroundColor: @json($chartData['tasks_by_status']['colors']),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            const projectProgressCtx = document.getElementById('projectProgressChart').getContext('2d');
            const projectProgressChart = new Chart(projectProgressCtx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['project_progress']->pluck('name')),
                    datasets: [{
                        label: 'Progress (%)',
                        data: @json($chartData['project_progress']->pluck('progress')),
                        backgroundColor: '#667eea',
                        borderColor: '#764ba2',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Initialize Reverb
            Pusher.logToConsole = true;
            const pusher = new Pusher('{{ env('REVERB_APP_KEY') }}', {
                wsHost: '{{ env('REVERB_HOST') }}',
                wsPort: {{ env('REVERB_PORT') }},
                forceTLS: false,
                enabledTransports: ['ws']
            });

            const channel = pusher.subscribe('dashboard.{{ auth()->id() }}');
            channel.bind('App\\Events\\DashboardUpdated', function(data) {
                // Update stats
                document.querySelector('.stats-card.primary .counter').setAttribute('data-count', data.stats
                    .projects);
                document.querySelector('.stats-card.success .counter').setAttribute('data-count', data.stats
                    .completed_tasks);
                document.querySelector('.stats-card.warning .counter').setAttribute('data-count', data.stats
                    .pending_tasks);
                document.querySelector('.stats-card.danger .counter').setAttribute('data-count', data.stats
                    .team_members);
                animateCounters();

                // Update recent activities
                const activityContainer = document.getElementById('recent-activities');
                activityContainer.innerHTML = data.recentActivities.map((activity, index) => `
                    <div class="activity-item ${['primary', 'success', 'warning', 'danger'][index % 4]}">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(activity.user_name)}&background=667eea&color=fff"
                                class="user-avatar me-3" alt="User">
                            <div class="flex-grow-1">
                                <div class="fw-medium small">${activity.description}</div>
                                <div class="text-muted small">${activity.created_at}</div>
                            </div>
                        </div>
                    </div>
                `).join('') + `
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-outline-primary">View All Activity</a>
                    </div`;

                // Update charts
                tasksByStatusChart.data.labels = data.chartData.tasks_by_status.labels;
                tasksByStatusChart.data.datasets[0].data = data.chartData.tasks_by_status.data;
                tasksByStatusChart.update();

                projectProgressChart.data.labels = data.chartData.project_progress.map(p => p.name);
                projectProgressChart.data.datasets[0].data = data.chartData.project_progress.map(p => p
                    .progress);
                projectProgressChart.update();
            });

            // Polling fallback
            setInterval(() => {
                fetch('{{ route('dashboard.live-data') }}')
                    .then(response => response.json())
                    .then(data => {
                        // Update stats
                        document.querySelector('.stats-card.primary .counter').setAttribute(
                            'data-count', data.stats.projects);
                        document.querySelector('.stats-card.success .counter').setAttribute(
                            'data-count', data.stats.completed_tasks);
                        document.querySelector('.stats-card.warning .counter').setAttribute(
                            'data-count', data.stats.pending_tasks);
                        document.querySelector('.stats-card.danger .counter').setAttribute('data-count',
                            data.stats.team_members);
                        animateCounters();

                        // Update recent activities
                        const activityContainer = document.getElementById('recent-activities');
                        activityContainer.innerHTML = data.recentActivities.map((activity, index) => `
                            <div class="activity-item ${['primary', 'success', 'warning', 'danger'][index % 4]}">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(activity.user_name)}&background=667eea&color=fff"
                                        class="user-avatar me-3" alt="User">
                                    <div class="flex-grow-1">
                                        <div class="fw-medium small">${activity.description}</div>
                                        <div class="text-muted small">${activity.created_at}</div>
                                    </div>
                                </div>
                            </div>
                        `).join('') + `
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-outline-primary">View All Activity</a>
                            </div`;

                        // Update charts
                        tasksByStatusChart.data.labels = data.chartData.tasks_by_status.labels;
                        tasksByStatusChart.data.datasets[0].data = data.chartData.tasks_by_status.data;
                        tasksByStatusChart.update();

                        projectProgressChart.data.labels = data.chartData.project_progress.map(p => p
                            .name);
                        projectProgressChart.data.datasets[0].data = data.chartData.project_progress
                            .map(p => p.progress);
                        projectProgressChart.update();
                    });
            }, 30000);
        });
    </script>
@endsection
