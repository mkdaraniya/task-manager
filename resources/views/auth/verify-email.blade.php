@extends('welcome')

@section('content')
    <div class="section d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-5 text-center">
                            <h3 class="fw-bold mb-3">Verify Your Email</h3>

                            <p class="text-muted mb-4">
                                Thanks for signing up! Before getting started, please verify your email by clicking
                                the link we just sent to you. If you didn’t receive the email, we’ll send another.
                            </p>

                            @if (session('status') == 'verification-link-sent')
                                <div class="alert alert-success">
                                    A new verification link has been sent to your email.
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mt-4">
                                <!-- Resend Email -->
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary">
                                        Resend Verification Email
                                    </button>
                                </form>

                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
