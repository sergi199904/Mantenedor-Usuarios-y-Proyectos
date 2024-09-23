<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Gestion de Proyectos</title>

    <link rel="stylesheet" href="assets/css/minified.css">

    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
</head>

<body>
    <header class="header-section header-cl-black">
        <div class="container">
            <div class="header-wrapper">
                <div class="logo">
                    <a href="./">
                        <img src="assets/images/logo/logo2.png" alt="logo">
                    </a>
                </div>
                <div class="header-bar d-lg-none">
                </div>
                @if (Auth::check())
                    <form action="{{ Route('usuario.logout') }}" method="POST">
                        @csrf
                        <button class="header-button d-none d-sm-inline-block" type="submit">Cerrar Sesión</button>
                    </form>
                @else
                    <a href="/login" class="header-button d-none d-sm-inline-block">Iniciar Sesión</a>
                @endif
            </div>
        </div>
        </div>
    </header>
    <section class="banner-12 pos-rel oh">
        <div class="extra-bg bg_img" data-background="assets/images/banner/banner-12-bg.jpg"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="banner-content-12">
                        <h1 class="title">Tech Solutions Gestion de Proyectos</h1>
                        <p>
                            Es una herramienta diseñada para gestionar proyectos.
                        </p>

                        @foreach ($proyectos as $proyecto)
                            @if ($proyecto->id != 1)
                                <hr>
                                <p><b>{{ $proyecto->nombre }}: </b> {{ $proyecto->descripcion }}</p>
                            @endif
                        @endforeach
                        <div class="banner-button-group">
                            <a href="/login" class="button-4">INGRESAR</a>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-lg-block d-none">
                    <div class="banner-12-thumb">
                        <img src="assets/images/banner/unnamed.png" alt="banner">
                    </div>
                </div>

            </div>
        </div>
        </div>
        </div>
        </div>
    </section>
    <section class="app-video-section padding-top-2 padding-bottom oh" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="app-video-thumb">
                        <div class="rtl">
                            <img src="assets/images/feature/ex-video.png" alt="feature">
                        </div>
                        <a class="video-button popup" href="https://www.youtube.com/watch?v=ObZwFExwzOo">
                            <i class="flaticon-play"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="advance-feature-content">
                        <div class="section-header left-style mb-olpo">
                            <h5 class="cate">Gestion de proyectos</h5>

                            <p> Facilita la Gestion de proyectos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--
    <section class="faq-section padding-top padding-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="faq-header">
                        <div class="cate">
                            <img src="assets/images/cate.png" alt="cate">
                        </div>
                        <h2 class="title"></h2>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="faq-wrapper mb--38">
                        <div class="faq-item">
                            <div class="faq-thumb">
                                <i class="flaticon-pdf"></i>
                            </div>
                            <div class="faq-content">
                                <h4 class="title"></h4>
                                <p>
                                    
                                </p>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-thumb">
                                <i class="flaticon-pdf"></i>
                            </div>
                            <div class="faq-content">
                                <h4 class="title"></h4>
                                <p>
                                   
                                </p>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-thumb">
                                <i class="flaticon-pdf"></i>
                            </div>
                            <div class="faq-content">
                                <h4 class="title"></h4>
                                <p>
                                    
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    -->


    <footer class="footer-section bg_img" data-background="assets/images/footer/footer-bg.jpg">
        <div class="container">
            <div class="footer-top padding-top padding-bottom">
                <div class="logo">
                    <a href="./">
                        <img src="assets/images/logo/footer-logo.png" alt="logo">
                    </a>
                </div>

            </div>
            <div class="footer-bottom">
                <ul class="footer-link">
                    <li>
                        <a target="_blank" href="https://matcon.cmmedu.uchile.cl/">MATCON</a>
                    </li>
                    <li>
                        <a target="_blank" href="https://cmmedu.uchile.cl/desarrollo-profesional/suma-y-sigue/">SUMA Y
                            SIGUE</a>
                    </li>
                    <li>
                        <a target="_blank" href="https://www.sumoprimeroenterreno.cl/">SUMO PRIMERO</a>
                    </li>
                </ul>
            </div>
            <div class="copyright">
                <p>
                    Instituto Profesional San Sebastian<a target="_blank" href="https://ipss.cl/">ipss.cl/</a>
                </p>
            </div>
        </div>
    </footer>
    <script src="assets/js/minified.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>
