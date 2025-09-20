@extends('layouts.guest')

@section('content')
    <!-- Hero Section -->
    <section id="hero" class="hero">
        <div class="container">
            <div class="hero-content fade-in">
                <h1>Manage Tasks<br>Like a <strong>Pro</strong></h1>
                <p class="lead">Transform your productivity with our cutting-edge task management platform. Built for
                    teams who demand excellence, designed for individuals who value simplicity.</p>
                <div class="hero-buttons">
                    <a href="/login" class="btn btn-hero-primary">
                        <i class="bi bi-rocket-takeoff me-2"></i>Get Started Free
                    </a>
                    <a href="#features" class="btn btn-hero-secondary">
                        <i class="bi bi-play-circle me-2"></i>Watch Demo
                    </a>
                </div>
            </div>
        </div>

        <!-- Floating Cards -->
        <div class="floating-card">
            <i class="bi bi-check-circle-fill mb-2" style="font-size: 1.5rem;"></i>
            <div><strong>Task Completed</strong><br><small>Project Alpha milestone reached</small></div>
        </div>
        <div class="floating-card">
            <i class="bi bi-people-fill mb-2" style="font-size: 1.5rem;"></i>
            <div><strong>Team Collaboration</strong><br><small>5 members online now</small></div>
        </div>
        <div class="floating-card">
            <i class="bi bi-graph-up mb-2" style="font-size: 1.5rem;"></i>
            <div><strong>Productivity Up</strong><br><small>+47% this week</small></div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section">
        <div class="container">
            <div class="fade-in">
                <h2 class="section-title">About TaskManager</h2>
                <p class="section-subtitle">We're revolutionizing the way teams collaborate and individuals stay
                    organized. Our mission is simple: make productivity accessible, powerful, and completely free.</p>

                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3 class="mb-4">Why Choose Us?</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="feature-icon me-3" style="width: 50px; height: 50px; font-size: 1rem;">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Enterprise Security</h6>
                                        <small class="text-muted">Bank-level encryption</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="feature-icon me-3" style="width: 50px; height: 50px; font-size: 1rem;">
                                        <i class="bi bi-lightning"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Lightning Fast</h6>
                                        <small class="text-muted">Sub-second response</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="feature-icon me-3" style="width: 50px; height: 50px; font-size: 1rem;">
                                        <i class="bi bi-heart"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Always Free</h6>
                                        <small class="text-muted">No hidden costs ever</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="feature-icon me-3" style="width: 50px; height: 50px; font-size: 1rem;">
                                        <i class="bi bi-globe"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Global Access</h6>
                                        <small class="text-muted">Available worldwide</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-center">
                            <div
                                style="width: 300px; height: 300px; background: var(--primary-gradient); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="section">
        <div class="container">
            <div class="fade-in">
                <h2 class="section-title">Powerful Features</h2>
                <p class="section-subtitle">Everything you need to manage projects, collaborate with teams, and boost
                    productivity - all in one place.</p>

                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-kanban"></i>
                            </div>
                            <h5>Project Boards</h5>
                            <p>Visualize your workflow with intuitive Kanban boards. Drag, drop, and organize tasks with
                                unprecedented ease and flexibility.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <h5>Team Collaboration</h5>
                            <p>Real-time updates, instant notifications, and seamless communication. Keep everyone in
                                sync, no matter where they are.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-stopwatch"></i>
                            </div>
                            <h5>Time Tracking</h5>
                            <p>Built-in time tracking with detailed analytics. Understand where time goes and optimize
                                your productivity like never before.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-paperclip"></i>
                            </div>
                            <h5>File Management</h5>
                            <p>Attach files, share documents, and keep all project resources organized in one
                                centralized, secure location.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <h5>Advanced Security</h5>
                            <p>Role-based permissions, data encryption, and enterprise-grade security. Your data is
                                protected with military-grade security.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-gem"></i>
                            </div>
                            <h5>Forever Free</h5>
                            <p>No subscriptions, no limits, no catches. Professional-grade task management that's
                                completely free for everyone, forever.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="section stats-section">
        <div class="container">
            <div class="fade-in">
                <h2 class="section-title" style="color: white;">Trusted Worldwide</h2>
                <p class="section-subtitle" style="color: rgba(255,255,255,0.8);">Join thousands of teams who've
                    transformed their productivity with TaskManager.</p>

                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-item">
                            <span class="stat-number">50K+</span>
                            <div class="stat-label">Active Users</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-item">
                            <span class="stat-number">2M+</span>
                            <div class="stat-label">Tasks Completed</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-item">
                            <span class="stat-number">500+</span>
                            <div class="stat-label">Teams</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-item">
                            <span class="stat-number">99.9%</span>
                            <div class="stat-label">Uptime</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section">
        <div class="container">
            <div class="fade-in">
                <h2 class="section-title">Get In Touch</h2>
                <p class="section-subtitle">Have questions? Need support? We're here to help you succeed. Reach out
                    anytime!</p>

                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="contact-form">
                            <form id="contactForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" placeholder="John Doe"
                                            required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email"
                                            placeholder="john@company.com" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <select class="form-control" id="subject" required>
                                        <option value="">Choose a topic...</option>
                                        <option value="general">General Inquiry</option>
                                        <option value="support">Technical Support</option>
                                        <option value="feedback">Feedback</option>
                                        <option value="partnership">Partnership</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" rows="5" placeholder="Tell us how we can help you..." required></textarea>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-contact">
                                        <i class="bi bi-send me-2"></i>Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row mt-5 text-center">
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <i class="bi bi-envelope-fill" style="font-size: 2rem; color: #667eea;"></i>
                            <h6 class="mt-3">Email</h6>
                            <p class="text-muted">support@taskmanager.pro</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <i class="bi bi-telephone-fill" style="font-size: 2rem; color: #667eea;"></i>
                            <h6 class="mt-3">Phone</h6>
                            <p class="text-muted">+1 (555) 123-4567</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <i class="bi bi-geo-alt-fill" style="font-size: 2rem; color: #667eea;"></i>
                            <h6 class="mt-3">Address</h6>
                            <p class="text-muted">San Francisco, CA<br>United States</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
