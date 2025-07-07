<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrowLocal – Local Food, Connected</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(90deg, #eaf5fa 0%, #f0fbe9 100%);
            color: #263238;
        }
        .growlocal-hero {
            background: linear-gradient(90deg, #53c7fa 0, #38e4b0 100%);
            color: #fff;
            border-radius: 2rem;
            box-shadow: 0 8px 32px 0 rgba(60, 164, 186, 0.08);
        }

        .cta-btn {
            font-weight: 600;
            border-radius: 1.5rem;
            padding: 0.75rem 2.5rem;
            font-size: 1.2rem;
            box-shadow: 0 2px 12px 0 rgba(56,228,176,0.08);
        }
        .feature-card {
            border-radius: 1.5rem;
            background: #fff;
            box-shadow: 0 4px 16px 0 rgba(83,199,250,0.04);
            padding: 2rem 1.5rem;
            min-height: 240px;
            transition: box-shadow 0.15s;
        }
        .feature-card:hover {
            box-shadow: 0 8px 24px 0 rgba(56,228,176,0.15);
        }
        .growlocal-logo {
            font-size: 2.5rem;
            font-weight: 900;
            letter-spacing: -1.5px;
        }
        .nav-link {
            color: #263238 !important;
            font-weight: 600;
        }
        .nav-link:hover {
            color: #53c7fa !important;
        }
        .text-gradient {
            background: linear-gradient(90deg, #53c7fa, #38e4b0 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }
        .footer {
            border-top: 1px solid #e3f2fd;
            font-size: 0.96rem;
        }
        .stats-bar {
            background: rgba(56,228,176,0.12);
            border-radius: 1.25rem;
            box-shadow: 0 4px 20px 0 rgba(83,199,250,0.07);
            margin: 3rem 0 2rem 0;
        }
        .stat-item {
            font-size: 1.4rem;
            font-weight: 700;
            color: #278c66;
        }
        .stat-label {
            color: #39898c;
            font-size: 1rem;
            font-weight: 400;
            opacity: 0.82;
        }
        .testimonial-card {
            background: #fff;
            border-radius: 1.25rem;
            padding: 1.6rem 1.2rem;
            box-shadow: 0 2px 10px 0 rgba(56,228,176,0.06);
            margin: 0 0.75rem;
            min-height: 210px;
        }
        .testimonial-name {
            font-weight: 700;
            font-size: 1.05rem;
            color: #2563eb;
        }
        .testimonial-role {
            font-size: 0.95rem;
            color: #7d93a9;
        }
        /* Custom SVG style */
        .hero-svg {
            width: 100%;
            max-width: 450px;
            display: block;
            margin: 0 auto;
        }
        @media (max-width: 991.98px) {
            .growlocal-hero {
                text-align: center !important;
            }
        }
        @media (max-width: 767.98px) {
            .stats-bar {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white py-2 shadow-sm">
        <div class="container">
            <span class="growlocal-logo text-gradient">GrowLocal</span>
            <div class="ms-auto d-flex">
                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2 px-4 rounded-pill fw-bold">Login</a>
                <a href="{{ route('register') }}" class="btn btn-success px-4 rounded-pill fw-bold">Sign up</a>
            </div>
        </div>
    </nav>

        <div class="container py-5 growlocalhheader">
            <div class="row align-items-center justify-content-center mb-5">
                <div class="col-lg-7 growlocal-hero p-5 text-lg-start">
                    <h1 class="display-4 fw-bold mb-3">
                        <span class="text-gradient">GrowLocal</span><br>
                        <span style="opacity:0.92;">The trusted platform for local produce and supply chains</span>
                    </h1>
                    <p class="lead mb-4" style="opacity:0.93;">
                        Full traceability, smarter crop planning, real accountability.<br>
                        Growers, schools and distributors — all working together for a more local, resilient food system.
                    </p>
                    <a href="{{ route('register') }}" class="cta-btn btn btn-light text-success shadow fw-bold me-2">Get Started</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light cta-btn ms-1">Login</a>
                </div>
                <div class="col-lg-5 text-center">

                </div>
            </div>
        <main>
            <!-- Stats Bar -->
            <div class="row justify-content-center text-center stats-bar px-2 py-4 mb-4 g-3">
                <div class="col-md-3 col-6">
                    <div class="stat-item">{{ $growersCount }}</div>
                    <div class="stat-label">Welsh Growers (2025)</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">{{ number_format($vegTraced) }}kg</div>
                    <div class="stat-label">Veg Traced in Wales</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">£{{ number_format($growerValue, 2) }}</div>
                    <div class="stat-label">Value Paid to Growers</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">{{ $tracePercent }}%</div>
                    <div class="stat-label">Batch Traceability</div>
                </div>
                
            </div>

            <!-- Features Section -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="feature-card h-100 text-center">
                        <div class="fs-1 mb-3">📦</div>
                        <h4 class="fw-bold mb-2">Traceable Deliveries</h4>
                        <p>Every batch tracked, every delivery note one click away. Full traceability from field to fork.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card h-100 text-center">
                        <div class="fs-1 mb-3">🌱</div>
                        <h4 class="fw-bold mb-2">Crop Planning</h4>
                        <p>Growers and distributors plan together for less waste, better value, and more certainty.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card h-100 text-center">
                        <div class="fs-1 mb-3">📊</div>
                        <h4 class="fw-bold mb-2">Digital Simplicity</h4>
                        <p>Automatic stats, instant insights, downloadable reports, and simple digital record-keeping.</p>
                    </div>
                </div>
            </div>

            <!-- Testimonials Carousel -->
            <div class="row mb-5 justify-content-center">
                <div class="col-lg-10">
                    <h3 class="fw-bold mb-4 text-center text-gradient">What our partners say</h3>
                    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner text-center">
                            <div class="carousel-item active">
                                <div class="testimonial-card">
                                    <p class="mb-2 fst-italic">“GrowLocal changed how we plan and deliver as a farm. Less paperwork, more time in the fields, and better results with local schools.”</p>
                                    <span class="testimonial-name">Jess M.</span>
                                    <div class="testimonial-role">Grower, Carmarthenshire</div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="testimonial-card">
                                    <p class="mb-2 fst-italic">“The traceability gives our kitchen real confidence — we know exactly where produce came from, and it supports our community.”</p>
                                    <span class="testimonial-name">Dylan R.</span>
                                    <div class="testimonial-role">School Catering Manager</div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="testimonial-card">
                                    <p class="mb-2 fst-italic">“As a distributor, the planning dashboard lets us manage supply and demand so much better. It's made local food work at scale.”</p>
                                    <span class="testimonial-name">Amy K.</span>
                                    <div class="testimonial-role">Distributor, Food Vale</div>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon bg-success rounded-circle" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon bg-success rounded-circle" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Call to Action Strip -->
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto">
                    <div class="feature-card p-4 text-center">
                        <h4 class="fw-bold mb-2">Ready to build a better local food system?</h4>
                        <p class="mb-3">Sign up, and join the movement for better food, real data, and stronger communities.</p>
                        <a href="{{ route('register') }}" class="btn btn-success px-5 py-3 fw-bold rounded-pill">Join GrowLocal</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="footer bg-white py-3 mt-5 text-center text-muted">
        &copy; {{ now()->year }} GrowLocal – Made by and for real local producers.
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>