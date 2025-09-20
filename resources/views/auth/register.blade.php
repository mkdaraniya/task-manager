@extends('welcome')

@section('content')
    <div class="section d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-5">
                            <h3 class="text-center mb-4 fw-bold">Create an Account</h3>

                            <!-- Validation Errors -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input id="name" type="text" class="form-control" name="name"
                                        value="{{ old('name') }}" required autofocus>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input id="email" type="email" class="form-control" name="email"
                                        value="{{ old('email') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input id="password" type="password" class="form-control" name="password" required
                                        autocomplete="new-password">
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input id="password_confirmation" type="password" class="form-control"
                                        name="password_confirmation" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    Register
                                </button>
                            </form>

                            <div class="text-center mt-3">
                                <small>Already have an account? <a href="{{ route('login') }}">Login</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
