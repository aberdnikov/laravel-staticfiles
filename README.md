# How To Use

## Регистрация провайдера:
в 
~~~
../app/config/app.php
~~~
добавим в секцию провайдеров
~~~
'providers'       => [
	'Illuminate\Foundation\Providers\ArtisanServiceProvider',
	'Illuminate\Auth\AuthServiceProvider',
	... 
	'Larakit\Larastatic\LarastaticServiceProvider',
],
~~~

## Подключаемые стили и скрипты вынесем в отдельный файл, для этого в 
~~~
../app/start/global.php
~~~
в самый конец добавим строку:
~~~
require app_path() . '/staticfiles.php';
~~~
## Создадим файл 
~~~
../app/staticfiles.php
~~~
и заполним его инструкциями по подключению JS/CSS
~~~
<?php
\Larakit\Larastatic\Css::instance()
   ->add('http://fonts.googleapis.com/css?family=Open+Sans:300&subset=cyrillic')
   ->add('http://fonts.googleapis.com/css?family=Oswald:400,700,300')
   ->add('/packages/components/font-awesome/css/font-awesome.css')
   ->add('/packages/components/animate.css/animate.css')
   ->add('/packages/components/jquery-pace/jquery-pace.js')
   ->add('/packages/components/jquery-notific8/jquery.notific8.min.css')
;
\Larakit\Larastatic\Js::instance()
  ->add('/!/build/bootstrap.min.js')
  ->add('/!/static/js/main.js')
;
~~~

## Все, сейчас чтобы добавленные стили и скрипты вставились во все страницы сайта надо в вашем шаблонизаторе прописать вызов
~~~
<html>
    <head>
        <title>title</title>
        {{ \Larakit\Larastatic\Css::instance()->__toString(); }}
    </head>
    <body>
		{{ \Larakit\Larastatic\Js::instance()->__toString(); }}
    </body>
</html>
~~~

## Если у вас подключен шаблонизатор Twig через мостик "rcrowe/twigbridge", то 
1) опубликуйте в app конфиг пакета
~~~
php artisan config:publish rcrowe/twigbridge
~~~
2) перейдите в 
~~~
../app/config/packages/rcrowe/twigbridge/extensions.php 
~~~
3) впишите в секцию enabled
~~~
return [

    /*
    |--------------------------------------------------------------------------
    | Extensions
    |--------------------------------------------------------------------------
    |
    | Enabled extensions.
    |
    | `Twig_Extension_Debug` is enabled automatically if twig.debug is TRUE.
    |
    */
    'enabled'   => [
        'TwigBridge\Extension\Loader\Facades',
        'TwigBridge\Extension\Loader\Filters',
        'TwigBridge\Extension\Loader\Functions',
		...
		'Larakit\Larastatic\Twig'
~~~
4) теперь в ваших шаблонах можете вызывать вставку css & js

~~~ 
<html>
    <head>
        <title>title</title>
        {{ css() }}
    </head>
    <body>
		{{ js() }}
    </body>
</html>
~~~ 
