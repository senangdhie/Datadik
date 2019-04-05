<?php
    namespace Swiftlet;

    try {
        require __DIR__ . '/../Swiftlet/Interfaces/App.php';
        require __DIR__ . '/../Swiftlet/App.php';

        $app = new App;

        set_error_handler(array($app, 'error'), E_ALL | E_STRICT);
        ini_set('display_errors',1);
        spl_autoload_register(array($app, 'autoload'));

        require __DIR__ . '/../Swiftlet/Src/blukutuk.php';

        $app->run();
        $app->serve();
    } catch (\Exception $e) {
        if ( !headers_sent() ) {
            header('HTTP/1.1 503 Service Temporarily Unavailable');
            header('Status: 503 Service Temporarily Unavailable');
        }
        $_ex = "<style>* { font-family : 'Open Sans', HelveticaNeue, Helvetica, Arial; }</style><table width=\"350\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin:50px auto;-webkit-border-radius: 5px;-moz-border-radius:5px;border:1px solid #fbeed5;\"><thead><tr><th style=\"background-color:#fbeed5;\"><strong>System Error</strong></th></tr></thead><tbody><tr><td style=\"background-color:#fcf8e3;text-align:center;\">Sistem mengalami masalah saat memanggil halaman tujuan<!--".$e->getMessage()."--></td></tr></tbody></table>";
        exit($_ex);
    }
?>