<?php
$version    = 'v.1945.05.09';
$build_url  = '/!/build/';
$optimized  = !((bool)\Config::get('app.debug'));
return [
	//версия билда http://site.ru/!/build/{version}/style.css
    'version'    => $version,
	//в эту директорию будут складываться билды
    'build_dir'  => app('path.public') . $build_url,
	//относительный урл для формирования ссылки на билд
    'build_url'  => $build_url,
	//хост, с которого раздается статика
	//можете купить еще один домен и сделать его алиасом для текущего сайта
	//тогда если будете раздавать статику со второго домена будет оптимизация в том
	//что не будет посылаться авторизационная кука при запросе статики (экономия трафика)
    'host'       => 'http://' . \Illuminate\Support\Arr::get($_SERVER, 'HTTP_HOST'),
	//опции раздачи CSS 
    'css'        => [
		//css добавленный как Css::instance()->addInline('.label{color:red}');
        'inline'   => [
			//собирать в билд
            'build' => $optimized,
			//минимизировать
            'min'   => $optimized,
        ],
		//css добавленный как Css::instance()->add('http://site.ru/!/static/css/styles.css');
        'external' => [
			//собирать в билд
            'build' => $optimized,
			//минимизировать
            'min'   => $optimized,
        ],
    ],
	//опции раздачи JS
    'js'         => [
		//js добавленный как Js::instance()->addOnload('alert(123)'); - то что будет выполнено после загрузки страницы
        'onload'   => [
			//собирать в билд
            'build' => $optimized,
			//минимизировать
            'min'   => $optimized,
        ],
		//js добавленный как Js::instance()->addInline('function ttt(){ return "ttt"; }'); 
        'inline'   => [
			//собирать в билд
            'build' => $optimized,
			//минимизировать
            'min'   => $optimized,
        ],
		//js добавленный как Js::instance()->add('http://site.ru/!/static/js/styles.js');
        'external' => [
			//собирать в билд
            'build' => $optimized,
			//минимизировать
            'min'   => $optimized,
        ],
    ],
	//для этих пакетов будет выполнена команда "asset:publish"
    //for these packages will execute the command "asset:publish"
	//не забудьте прописать эти же пакеты в composer.json
    'used'       => [
        /*
        "package-name"=> "path to static (default: '')"
        */
        'components/jquery'          => '',
        "components/bootstrap"       => '',
        "components/jqueryui"        => '',
        "components/font-awesome"    => '',
        "components/animate.css"     => '',
        "components/jquery-pace"     => '',
        "components/jquery-notific8" => 'dist',
    ]
];