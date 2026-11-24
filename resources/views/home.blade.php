@extends('layouts.landing.app')

@section('title','Consultkaar | Professional Consultations')

@section('content')

<main>
    <div class="main-body-div">
        <!-- Hero Section -->
        <section class="top-start" style="padding: 120px 0; background: #2a56a7; color: white;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12 text-center hero-content">
                        <h1 class="display-3 font-weight-bold mb-4">Consultkaar</h1>
                        <p class="lead mb-5" style="font-size: 1.5rem;">Expert Advice & Professional Consultations at Your Fingertips.</p>
                        
                        <div class="d-flex justify-content-center mt-5">
                            <div class="mx-3">
                                <a href="#">
                                    <button class="btn btn-outline-light btn-lg px-5 py-3" style="border-radius: 50px;">Get Started</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-5 bg-light">
            <div class="container py-5">
                <div class="row text-center">
                    <div class="col-md-4 mb-4">
                        <div class="p-4 bg-white shadow-sm rounded">
                            <i class="fas fa-user-tie fa-3x mb-3" style="color: #2a56a7;"></i>
                            <h4 class="font-weight-bold">Expert Advisors</h4>
                            <p class="text-muted">Connect with verified professionals across multiple domains.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="p-4 bg-white shadow-sm rounded">
                            <i class="fas fa-clock fa-3x mb-3" style="color: #2a56a7;"></i>
                            <h4 class="font-weight-bold">Flexible Timing</h4>
                            <p class="text-muted">Book consultations at your convenience, anytime, anywhere.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="p-4 bg-white shadow-sm rounded">
                            <i class="fas fa-shield-alt fa-3x mb-3" style="color: #2a56a7;"></i>
                            <h4 class="font-weight-bold">Secure Platform</h4>
                            <p class="text-muted">Your privacy and data security are our top priorities.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Simple About -->
        <section class="py-5">
            <div class="container text-center">
                <h2 class="font-weight-bold mb-4" style="color: #2a56a7;">About Consultkaar</h2>
                <p class="lead text-secondary mx-auto" style="max-width: 700px;">
                   Consultkaar connects clients with trusted advisors across multiple fields, making professional guidance simple, accessible, and convenient.
 
                </p>
            </div>
        </section>
    </div>
</main>

@endsection
