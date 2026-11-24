<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="icon" href="{{asset('public/assets/landing')}}/image/logo_no_bg.png">

    <!-- Bootstrap+JQuery -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.transitions.css">

    <!-- fontawesome -->
    <link rel="stylesheet" href="{{asset('public/assets/landing/fontawesome/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/landing/fontawesome/css/fontawesome.min.css')}}">

    <!-- Normalize CSS -->
    <link rel="stylesheet" href="{{asset('public/assets/landing')}}/css/normalize.css">

    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{asset('public/assets/landing/css/main.css')}}?v=2.2">
    <style>
        a:hover {
            color: #000000;
            text-decoration: underline;
        }

        .navbar-nav .show+a:first-child{
            color: #000000;
        }

        .dropdown-item:hover {
            background: transparent;
        }

        .dropdown-item.active, .dropdown-item:active {
            background: transparent;
        }
    </style>
    @stack('css_or_js')
</head>

<body>

<header>
    <div class="navbar-div" style="background-color: #2a56a7 !important;">
        <nav class="navbar navbar-expand-md">
            <div class="container">
                <a class="navbar-brand" href="{{route('home')}}">
                    @php($logo=\App\CentralLogics\Helpers::get_settings('logo'))
                    <?php
                        $logoPath = (env('APP_ENV') == 'local') ? asset('storage/business/' . $logo) : asset('storage/app/public/business/' . $logo);        
                    ?>
                    <img  onerror="this.src='{{asset('public/assets/admin/img/160x160/logo2.png')}}'"
                          src="{{$logoPath}}"
                          style="height:auto;width:100%; max-width:200px; max-height:60px">
                </a>
                <button style="background: #FFFFFF; border-radius: 2px;font-size: 13px" class="navbar-toggler" type="button"
                        data-toggle="collapse" data-target="#navbarNav">
                   <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto"></ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link navbar-font text-white" href="{{route('home')}}">{{__('messages.home')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link navbar-font text-white" href="{{route('terms-and-conditions')}}">{{__('messages.terms_and_condition')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link navbar-font text-white" href="{{route('about-us')}}">{{__('messages.about_us')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link navbar-font text-white" href="{{route('contact-us')}}">{{__('messages.contact_us')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link navbar-font text-white" href="{{route('privacy-policy')}}">{{__('messages.privacy_policy')}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>

@yield('content')

<footer>
    <div class="footer-div">
        <footer class="footer-background text-white text-lg-start">
            <div class="container">
                <div class="row d-flex justify-content-center justify-content-md-start text-center text-md-left">
                    <div class="col-lg-3 col-md-3 mb-md-0 company_details">
                        <div class="row d-flex justify-content-center justify-content-md-start text-center text-md-left">
                            <div class="col-md-12 col-sm-12 d-flex justify-content-center justify-content-md-start text-center text-md-left" style="padding: 0">
                                <a class="" href="#">
                                    @php($logo=\App\CentralLogics\Helpers::get_settings('logo'))
                                    <img class="rounded float-left"
                                         onerror="this.src='{{asset('public/assets/admin/img/160x160/logo2.png')}}'"
                                         src="{{asset('storage/app/public/business/'.$logo)}}"
                                         style="max-width: 200px;max-height: 75px">
                                </a>
                            </div>
                        </div>

                        <div class="footer-article-div">
                            <span class="footer-article">
                              
                   Consultkaar connects clients with trusted advisors across multiple fields, making professional guidance simple, accessible, and convenient.

                            </span>
                        </div>

                        <div class="mt-4">
                            <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-white" style="margin-left: 44px"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-white" style="margin-left: 44px"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-white" style="margin-left: 44px"><i class="fab fa-skype"></i></a>
                        </div>
                    </div>

                    <hr class="hr-footer-m">

                    <div class="col-lg-2 col-md-2 mb-0 mb-md-0"></div>
                    <div class="col-lg-2 col-md-2 mb-md-0 footer-items">
                        <span class="footer-title text-uppercase mb-4">{{__('messages.support')}}</span>
                        <ul class="list-unstyled">
                            <li><a href="{{route('about-us')}}" class="footer-item text-white">{{__('messages.about_us')}}</a></li>
                            <li><a href="{{route('contact-us')}}" class="footer-item text-white">{{__('messages.contact_us')}}</a></li>
                            <li><a href="{{route('privacy-policy')}}" class="footer-item text-white">{{__('messages.privacy_policy')}}</a></li>
                            <li><a href="{{route('terms-and-conditions')}}" class="footer-item text-white">{{__('messages.terms_and_condition')}}</a></li>
                        </ul>
                    </div>

                    <hr class="hr-footer-m">

                    <div class="col-lg-2 col-md-2 mb-md-0 footer-items">
                        <span class="footer-title text-uppercase mb-4">Quick Links</span>
                        <ul class="list-unstyled">
                            <li><a href="{{route('home')}}" class="footer-item text-white">Home</a></li>
                            <li><a href="#" class="footer-item text-white">Advisors</a></li>
                        </ul>
                    </div>

                    <hr class="hr-footer-m">

                    <div class="col-lg-3 col-md-3 mb-md-0 footer-items">
                        <span class="footer-title text-uppercase mb-4">Contact Us</span>
                        <ul class="list-unstyled mb-0">
                            <li>
                                <a href="#!" class="footer-item text-white">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>{{\App\CentralLogics\Helpers::get_settings('address')}}</span>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="mailto:{{\App\CentralLogics\Helpers::get_settings('email_address')}}" class="footer-item text-white">
                                    <i class="fas fa-envelope MR-1"></i>
                                    <span class="ml-1">{{\App\CentralLogics\Helpers::get_settings('email_address')}}</span>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="tel:{{\App\CentralLogics\Helpers::get_settings('phone')}}" class="footer-item text-white">
                                    <i class="fas fa-phone MR-1"></i>
                                    <span class="ml-1">{{\App\CentralLogics\Helpers::get_settings('phone')}}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="text-center" style="background-color: rgba(0, 0, 0, 0.2);font-size: 12px">
                {{\App\CentralLogics\Helpers::get_settings('footer_text')}}
                <a class="text-white" href="#">{{\App\CentralLogics\Helpers::get_settings('business_name')}}</a>
            </div>
        </footer>
    </div>
</footer>

<!-- Scripts -->
<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>

@stack('script_2')
</body>

</html>
