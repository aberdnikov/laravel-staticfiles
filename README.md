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

## Как использовать готовые пакеты со статикой
1) заходите на packagist.org и вписываете в поле поиска название нужного пакета, например jquery, bootstrap, jqueryui, angular, etc...
Скорее всего этот пакет будет у вендора "components"
2) вписываете пакет в composer.json
~~~ 
{
    "require": {
        "components/bootstrap": "*",
        "components/jqueryui": "*",
        "components/font-awesome": "*",
        "components/animate.css": "*",
        "components/jquery-pace": "*",
        "components/jquery-notific8": "dev-master",
        "components/jquery": "*"
    }, 
}
~~~ 
3) опубликуйте в app конфиг пакета "larakit/larastatic" чтобы его можно было перезаписывать
~~~ 
$php artisan config:publish larakit/larastatic
~~~ 

4) вписываете эти же пакеты, подключенные в composer в 
~~~
../app/config/packages/larakit/larastatic/larastatic.php
~~~
в секцию used
~~~
    'used'       => [
        'components/jquery'          => null,
        "components/bootstrap"       => null,
        "components/jqueryui"        => null,
        "components/font-awesome"    => null,
        "components/animate.css"     => null,
        "components/jquery-pace"     => null,
        "components/jquery-notific8" => 'dist',
        "{package}" 				 => '{path}',
    ]
~~~
где ключ - это название пакета, а значение - это путь к статике, которую надо будет вытащить в директорию доступную по HTTP (по умолчанию "public" или true)
5) после того как это сделано можете вызвать в консоли процедуру
~~~
php artisan larakit:static-deploy 
~~~
которая произведет выкладку статики в public 
php artisan asset:publish {package} --path={path}
для каждого пакета из 
~~~
Config::get('larakit::larastatic.used');
~~~

6) почистить выложенную статику можно запустив команду
~~~
php artisan larakit:static-flush
~~~
 
 
## Возможности и рекомендации
Пакет умеет собирать в один файл и минимизировать статику. Все билды версифицированы, что исключает кеширование на стороне клиента.
Для режима разработки отключите сборку билдов, а на продакшн-сервере включите. 
Этим вы значительно уменьшите количество выполняемых к серверу запросов для получения статики.
