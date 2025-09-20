@extends('welcome')

@section('content')
<div class="section d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-3 fw-bold">Confirm Password</h3>
                        <p class="text-muted text-center mb-4">
                            This is a secure area of the application. Please confirm your password before continuing.
                        </p>

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

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                Confirm
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
