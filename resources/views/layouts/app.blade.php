<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Around Odisha E-Paper')</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- Font Awesome 6 --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">


    <style>
        .navbar-brand img {
            height: 40px;
        }

        .city-nav .nav-link {
            color: #333;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 4px;
            margin: 0 2px;
        }

        .city-nav .nav-link:hover,
        .city-nav .nav-link.active {
            background-color: #dc3545;
            color: white;
        }

        .epaper-title {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        #zoomControls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin: 15px 0;
}

#zoomControls .btn {
    min-width: 40px;
}

        .page-controls {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .page-thumbnail {
            border: 2px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 10px;
        }

        .page-thumbnail:hover,
        .page-thumbnail.active {
            border-color: #dc3545;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }

        .page-thumbnail img {
            width: 100%;
            height: auto;
        }

        .main-page-view {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        .main-page-view img {
            max-width: 100%;
            height: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .social-share {
            margin-top: 15px;
        }

        .social-share .btn {
            margin: 2px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-facebook { background: #3b5998; border-color: #3b5998; }
        .btn-twitter { background: #1da1f2; border-color: #1da1f2; }
        .btn-linkedin { background: #0077b5; border-color: #0077b5; }
        .btn-whatsapp { background: #25d366; border-color: #25d366; }
        .btn-print { background: #6c757d; border-color: #6c757d; }
        .btn-email { background: #dc3545; border-color: #dc3545; }

        .download-dropdown .dropdown-toggle::after {
            margin-left: 10px;
        }

        .pagination-controls .btn {
            margin: 0 2px;
        }

        .date-picker {
            max-width: 200px;
        }

        .page-selector {
            max-width: 150px;
        }

        .sidebar-thumbnails {
            max-height: 1651px;
            overflow-y: auto;
        }

        .thumbnail-label {
            text-align: center;
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .sidebar-thumbnails {
                max-height: 200px;
                overflow-x: auto;
                overflow-y: hidden;
                white-space: nowrap;
            }

            .page-thumbnail {
                display: inline-block;
                width: 80px;
                margin-right: 10px;
            }

            .city-nav {
                flex-wrap: wrap;
            }

           

        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('epaper.index') }}">
                 <img src="{{ asset('logo/image.png') }}" alt="Main Logo" height="40">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto city-nav">
                    @foreach(['Odisha', 'Ranchi', 'Delhi', 'Mumbai', 'Kolkata'] as $cityName)
                        <li class="nav-item">
                            <a class="nav-link {{ request('city', 'Odisha') === $cityName ? 'active' : '' }}"
                               href="{{ route('epaper.index', ['city' => $cityName]) }}">
                                {{ $cityName }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('epaper.archive') }}">
                            <i class="fas fa-archive"></i> Archive
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.index') }}">
                            <i class="fas fa-cog"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main -->
    <main class="container py-4">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // CSRF for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    {{-- <script>
    // Auto redirect from query string to clean URL
    const params = new URLSearchParams(window.location.search);
    const city = params.get('city');
    const date = params.get('date');

    if (city && date) {
        // Clean URL pattern
        const cleanUrl = `/archive/${city.toLowerCase()}/${date}`;
        window.location.replace(cleanUrl);
    }
</script> --}}

    @stack('scripts')
</body>
</html>
