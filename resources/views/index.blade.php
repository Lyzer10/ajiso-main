@extends('layouts.app')

@section('title', 'AJISO | Home')

@section('nav-links')
    <li>
        <a class="nav-link scrollto active" href="#hero">{{ __('Home') }}</a>
    </li>
    <li>
        <a class="nav-link scrollto" href="#about">{{ __('About') }}</a>
    </li>
    <li>
        <a class="nav-link scrollto" href="#faq">{{ __('FAQs') }}</a>
    </li>
    <li>
        <a class="nav-link" href="{{ route('login', app()->getLocale()) }}">
            {{ __('Login') }}
        </a>
    </li>
@endsection

@section('hero')
    <div class="container">
        <div class="row align-items-center justify-content-between" style="min-height: 70vh;">
            <div class="col-lg-6 pt-5 pt-lg-0" data-aos="zoom-out">
                <h1 class="text-dark">{{ __('AJISO Legal Aid Digital System') }}</h1>
                <h2 class="text-muted">{{ __('Action for Justice in Society') }}</h2>
                <p class="text-muted mb-4">
                    {{ __('We connect people with legal support, streamline case management, and provide real-time visibility for both beneficiaries and legal aid providers across Tanzania.') }}
                </p>
                <div class="text-center text-lg-start">
                    <a href="#about" class="btn-get-started scrollto">{{ __('Get Started') }}</a>
                </div>
            </div>
            <div class="col-lg-5 hero-img text-center" data-aos="zoom-out" data-aos-delay="200">
                <img src="{{ asset('assets/images/hero-img.png') }}" class="img-fluid animated" alt="AJISO on devices">
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- ======= About Section ======= -->
    <section id="about" class="about">
        <div class="container-fluid">

            <div class="row">
            <div class="col-xl-5 col-lg-6 d-flex justify-content-center align-items-stretch" data-aos="fade-right">
                <img src="{{ asset('assets/images/hero.png') }}" class="img-fluid animated" alt="AJISO illustration">
            </div>

            <div class="col-xl-7 col-lg-6 icon-boxes d-flex flex-column align-items-stretch justify-content-center py-5 px-lg-5" data-aos="fade-left">
                <h3>{{ __('Helping one person at a time.') }}</h3>
                <p>
                    {{ __('Action for Justice in Society (AJISO) exists to promote human rights, access to justice and socio-economic empowerment of women and vulnerable children. The AJISO Legal Aid System enables online applications, requests and correspondence and provides real-time visibility of case status to both beneficiaries and legal providers.') }}
                </p>

                <div class="icon-box" data-aos="zoom-in" data-aos-delay="100">
                <div class="icon"><i class="bx bx-fingerprint"></i></div>
                <h4 class="title"><a href="">{{ __('Simple') }}</a></h4>
                <p class="description">
                    {{ __('AJISO provides a modern, user-friendly platform that makes it easy to request legal support and follow case progress.') }}
                    </p>
                </div>

                <div class="icon-box" data-aos="zoom-in" data-aos-delay="200">
                <div class="icon"><i class="bx bx-gift"></i></div>
                <h4 class="title"><a href="">{{ __('Elegant') }}</a></h4>
                <p class="description">
                    {{ __('It speeds up processing, reduces paperwork, and improves data integrity and accuracy across the system.') }}
                </p>
                </div>

                <div class="icon-box" data-aos="zoom-in" data-aos-delay="300">
                <div class="icon"><i class="bx bx-atom"></i></div>
                <h4 class="title"><a href="">{{ __('Secure') }}</a></h4>
                <p class="description">
                    {{ __('Built with strong security practices to ensure availability, privacy, and safe access to services.') }}
                </p>
                </div>

            </div>
            </div>

        </div>
    </section><!-- End About Section -->

    <!-- ======= Testimonials Section ======= -->
                        <section id="testimonials" class="testimonials section-bg py-5">
  <div class="container">


    <div class="section-title" data-aos="fade-up">
              <h2 class="fw-bold">{{ __('Testimonials') }}</h2>

           <p class="text-">{{ __('What Others Are Saying About Us') }}</p>
        </div>

    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">

        <!-- Testimonial 1 -->
        <div class="carousel-item active">
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="card shadow border-0 rounded-3 p-4">
                <div class="card-body text-center">
                  <i class="fas fa-quote-left fa-2x text-primary mb-3"></i>
                  <p class="lead fst-italic">
                    {{ __('I had been struggling with an employment issue for months, but the legal advice I received from AJISO was clear and effective. They helped me understand my rights and took the pressure off my shoulders.') }}
                  </p>
                  <h5 class="fw-bold mt-3">ELFAS LAIZER</h5>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 2 -->
        <div class="carousel-item">
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="card shadow border-0 rounded-3 p-4">
                <div class="card-body text-center">
                  <i class="fas fa-quote-left fa-2x text-primary mb-3"></i>
                  <p class="lead fst-italic">
                    {{ __('We never thought we could afford proper legal representation, but AJISO made it possible. They treated us with respect and handled our case with care. Their support changed our life.') }}
                  </p>
                  <h5 class="fw-bold mt-3">ZENA MJAKA FAKI</h5>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 3 -->
        <div class="carousel-item">
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="card shadow border-0 rounded-3 p-4">
                <div class="card-body text-center">
                  <i class="fas fa-quote-left fa-2x text-primary mb-3"></i>
                  <p class="lead fst-italic">
                    {{ __('AJISO gave me hope when I thought all was lost. Their legal team was professional and guided me through every step of my land appeal case. I am forever grateful for their support.') }}
                  </p>
                  <h5 class="fw-bold mt-3">YUNUS HASSAN YUNUS</h5>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 4 -->
        <div class="carousel-item">
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="card shadow border-0 rounded-3 p-4">
                <div class="card-body text-center">
                  <i class="fas fa-quote-left fa-2x text-primary mb-3"></i>
                  <p class="lead fst-italic">
                    {{ __('As a woman facing eviction from my husband\'s land, I did not know where to turn. AJISO listened to my case and fought for my rights. Without their help, I would not have the home I have today.') }}
                  </p>
                  <h5 class="fw-bold mt-3">ELIZABETH NGAKA</h5>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Controls -->
      <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>

  </div>
</section>


    <!-- ======= F.A.Q Section ======= -->
    <section id="faq" class="faq section-bg">
    <div class="container">

        <div class="section-title" data-aos="fade-up">
            <h2>{{ __('F.A.Q') }}</h2>
            <p>{{ __('Frequently Asked Questions') }}</p>
        </div>

        <div class="faq-list">
            <ul>
                <li data-aos="fade-up">
                    <i class="bx bx-help-circle icon-help"></i>
                    <a data-bs-toggle="collapse" class="collapse" data-bs-target="#faq-list-1">
                        {{ __('What is the purpose of the AJISO Legal Aid System?') }}
                        <i class="bx bx-chevron-down icon-show"></i>
                        <i class="bx bx-chevron-up icon-close"></i>
                    </a>
                    <div id="faq-list-1" class="collapse show" data-bs-parent=".faq-list">
                        <p>
                            {{ __('The main purpose of this system is to enable online applications, requests, and correspondence and provide real-time visibility of case status to both beneficiaries and legal providers.') }}
                        </p>
                    </div>
                </li>

                <li data-aos="fade-up" data-aos-delay="100">
                    <i class="bx bx-help-circle icon-help"></i>
                    <a data-bs-toggle="collapse" data-bs-target="#faq-list-2" class="collapsed">
                        {{ __('How do I access the system?') }}
                        <i class="bx bx-chevron-down icon-show"></i>
                        <i class="bx bx-chevron-up icon-close"></i>
                    </a>
                    <div id="faq-list-2" class="collapse" data-bs-parent=".faq-list">
                        <p>
                            {{ __('You can access the system by visiting ajiso.org. You will have to use your username and password to login into the system. All users of the system are created by the administrator of the system.') }}
                        </p>
                    </div>
                </li>

                <li data-aos="fade-up" data-aos-delay="200">
                    <i class="bx bx-help-circle icon-help"></i>
                    <a data-bs-toggle="collapse" data-bs-target="#faq-list-3" class="collapsed">
                        {{ __('How is the availability of the system guaranteed?') }}
                        <i class="bx bx-chevron-down icon-show"></i>
                        <i class="bx bx-chevron-up icon-close"></i>
                    </a>
                    <div id="faq-list-3" class="collapse" data-bs-parent=".faq-list">
                        <p>
                            {{ __('The system is online and available 24/7 through the PC/computer, tablets as well as smartphones, any device connected to the internet.') }}
                        </p>
                    </div>
                </li>

                <li data-aos="fade-up" data-aos-delay="300">
                    <i class="bx bx-help-circle icon-help"></i>
                    <a data-bs-toggle="collapse" data-bs-target="#faq-list-4" class="collapsed">
                        {{ __('How does AJISO Legal Aid System keep information secure and prevent unauthorized access to data?') }}
                        <i class="bx bx-chevron-down icon-show"></i>
                        <i class="bx bx-chevron-up icon-close"></i>
                    </a>
                    <div id="faq-list-4" class="collapse" data-bs-parent=".faq-list">
                        <p>
                            {{ __('The system is built with major security practices. All users of the system can access the system by providing authenticated credentials that are created by the super administrator of the system. Moreover not all users have the access to everything within the system, as access control levels and groups are limited.') }}
                        </p>
                    </div>
                </li>

                <li data-aos="fade-up" data-aos-delay="400">
                    <i class="bx bx-help-circle icon-help"></i>
                    <a data-bs-toggle="collapse" data-bs-target="#faq-list-5" class="collapsed">
                        {{ __('What should I do if I forget my password?') }}
                        <i class="bx bx-chevron-down icon-show"></i>
                        <i class="bx bx-chevron-up icon-close"></i>
                    </a>
                    <div id="faq-list-5" class="collapse" data-bs-parent=".faq-list">
                        <p>
                            {{ __('The system is incorporated with password recovery functionality. You can access this when attempting a login. On login click \"Forgot Password?\" and you will be prompted to fill in your registered email address and a confirmation email will be sent to your inbox. After following the confirmation instructions you will then be redirected to change your password.') }}
                        </p>
                    </div>
                </li>

                <li data-aos="fade-up" data-aos-delay="400">
                    <i class="bx bx-help-circle icon-help"></i>
                    <a data-bs-toggle="collapse" data-bs-target="#faq-list-6" class="collapsed">
                        {{ __('Where do I get help when I face any technical problem with the system?') }}
                        <i class="bx bx-chevron-down icon-show"></i>
                        <i class="bx bx-chevron-up icon-close"></i>
                    </a>
                    <div id="faq-list-6" class="collapse" data-bs-parent=".faq-list">
                        <p>
                            {{ __('When facing any problem with the system do not hesitate to contact us for real-time support through: info@ajiso.org') }}
                        </p>
                    </div>
                </li>
            </ul>
        </div>

    </div>
</section><!-- End F.A.Q Section -->

@endsection
