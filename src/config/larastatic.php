<?php
$version    = 'v.1945.05.09';
$build_url  = '/!/build/';
$static_url = '/!/static/';
$optimized  = !((bool)\Config::get('app.debug'));
return [
    'version'    => $version,
    'build_dir'  => app('path.public') . $build_url,
    'build_url'  => $build_url,
    'static_url' => $static_url,
    'host'       => 'http://' . \Illuminate\Support\Arr::get($_SERVER, 'HTTP_HOST'),
    'css'        => [
        'inline'   => [
            'build' => $optimized,
            'min'   => $optimized,
        ],
        'external' => [
            'build' => $optimized,
            'min'   => $optimized,
        ],
    ],
    'js'         => [
        'onload'   => [
            'build' => $optimized,
            'min'   => $optimized,
        ],
        'inline'   => [
            'build' => $optimized,
            'min'   => $optimized,
        ],
        'external' => [
            'build' => $optimized,
            'min'   => $optimized,
        ],
    ],
    //for these packages will execute the command "asset:publish"
    'used'       => [
        /*
        "package-name"=> "path to static (default: null)"
        */
        'components/jquery'          => null,
        "components/bootstrap"       => null,
        "components/jqueryui"        => null,
        "components/font-awesome"    => null,
        "components/animate.css"     => null,
        "components/jquery-pace"     => null,
        "components/jquery-notific8" => 'dist',
    ]
];