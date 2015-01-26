<?php
namespace Larakit\Larastatic;

use Illuminate\Support\Arr;

class Css extends File {
    /**
     * Внешние подключаемые файлы стилей
     * @var array
     */
    public $css_external;

    /**
     * inline CSS
     *
     * @var string
     */
    public $css_inline;

    static function instance() {
        static $css;
        if (!isset($css)) {
            $css = new Css();
        }
        return $css;
    }

    /**
     * Add external css file
     * @param $css_file
     * @param null $media - condition of use
     * @param null $condition - condition including script, example [if IE 6]
     * @param bool $no_build - flag exclude on build
     * @return $this
     * <!--[if IE 6]><link rel="stylesheet" href="http://habrahabr.ru/css/1302697277/ie6.css" media="all" /><![endif]-->
     */
    function add($css_file, $media = null, $condition = null, $no_build = false) {
        if (!$css_file)
            return $this;

        if (mb_strpos($css_file, '/') === 0) {
            $css_file = \Config::get('larakit::larastatic.host') . $css_file;
        }
        $this->css_external[$css_file] = [
            'condition' => $condition,
            'media'     => $media,
            'no_build'  => $no_build,
        ];
        return $this;
    }

    /**
     * @param string $css_inline
     * @return $this
     */
    function addInline($css_inline) {
        $this->css_inline[md5($css_inline)] = $css_inline;
        return $this;
    }

    /**
     * @return $this
     */
    function clearAll() {
        $this->clearExternal()->clearInline();
    }

    /**
     * @return $this
     */
    function clearInline() {
        $this->css_inline = [];
        return $this;;
    }

    /**
     * @return $this
     */
    function clearExternal() {
        $this->css_external = [];
        return $this;
    }

    function prepare($source, $min = true) {
        if ($min) {
            $source = \CssMin::minify($source);
        }
        return trim($source);
    }


    /**
     * Формирование инлайновых стилей
     * @return <type>
     */
    function getInline($as_html = true) {
        if (!$as_html) {
            return $this->css_inline;
        }
        if (!count($this->css_inline)) {
            return '';
        }
        $css_inline = (implode(PHP_EOL, $this->css_inline));
        $css_inline = $this->prepare($css_inline, \Config::get('larakit::larastatic.css.inline.min', false));
        if (\Config::get('larakit::larastatic.css.inline.build', false)) {
            $build_name = $this->makeFileName($css_inline, 'css/inline', 'css');
            $build_file = $this->buildFile($build_name);
            if (!file_exists($build_file)) {
                if (!file_exists(dirname($build_file))) {
                    mkdir(dirname($build_file), 0777, true);
                }
                $this->save($build_file, $css_inline);
            }
            return $this->getLink($this->buildUrl($build_name), 'all') . "\n        ";
        }
        else {
            return '<style type="text/css">' . PHP_EOL . $css_inline . PHP_EOL . '</style>';
        }
    }

    function replaceRelativePath($url) {
        $parse  = parse_url($url);
        $path   = Arr::get($parse, 'path');
        $scheme = Arr::get($parse, 'scheme');
        $host   = Arr::get($parse, 'host');
        $ret    = [];
        $i      = 0;
        $path   = dirname($path);
        while ('/' != $path) {
            $path = dirname($path);
            $i++;
            $ret[str_repeat('../', $i)] = $scheme . '://' . $host . (('/' != $path) ? $path : '') . '/';
        }
        krsort($ret);
        $source = file_get_contents($url);
        foreach ($ret as $k => $v) {
            $source = str_replace($k, $v, $source);
        }
        return $source;
    }


    /**
     * Внешние стили
     * @return string
     */
    function getExternal($as_html = true) {
        if (!$as_html) {
            return $this->css_external;
        }
        if (!count($this->css_external)) {
            return '';
        }
        $css_code = '';
        /* если не надо собирать файлы в один */
        if (!\Config::get('larakit::larastatic.css.external.build', false)) {
            foreach ($this->css_external as $css => $_css) {
                $css_code .= $this->getLink($css, Arr::get($_css, 'media'), Arr::get($_css, 'condition')) . "\n        ";
            }
        }
        else {
            $build     = [];
            $no_builds = [];
            $css_code  = '';
            foreach ($this->css_external as $css => $_css) {
                $condition = Arr::get($_css, 'condition');
                $media     = Arr::get($_css, 'media');
                $no_build  = Arr::get($_css, 'no_build');
                if ($no_build) {
                    $no_builds[$condition . '|' . $media][] = $css;
                }
                else {
                    $build[$condition . '|' . $media][] = $css;
                }
            }
            foreach ($no_builds as $key => $css) {
                list($condition, $media) = explode('|', $key);
                foreach ($css as $_css) {
                    $css_code .= $this->getLink($_css, $media, $condition) . "\n        ";
                }
            }
            foreach ($build as $key => $css) {
                list($condition, $media) = explode('|', $key);
                $build_name = $this->makeFileName($css, 'css/' . $condition . '/' . $media . '/', 'css');
                $prefix     = ['css'];
                if ($condition) {
                    $prefix[] = str_replace(' ', '', $condition);
                }
                if ($media) {
                    $prefix[] = $media;
                }
                //                print '<pre>';
                $build_name = $this->makeFileName($css, implode('/', $prefix), 'css');
                $build_file = $this->buildFile($build_name);
                if (!file_exists($build_file)) {
                    //соберем билд в первый раз
                    $build = [];
                    foreach ($css as $url) {
                        $_css    = $this->replaceRelativePath($url);
                        $_css    = $this->prepare($_css, (mb_strpos($url, '.min.') === false));
                        $build[] = "/**********************************************************************" . PHP_EOL;
                        $build[] = '* ' . $url . PHP_EOL;
                        $build[] = "**********************************************************************/" . PHP_EOL;
                        $build[] = $_css . PHP_EOL . PHP_EOL;
                    }
                    if (!file_exists(dirname($build_file))) {
                        mkdir(dirname($build_file), 0777, true);
                    }

                    $this->save($build_file, implode('', $build));
                }
                $css_code .= $this->getLink($this->buildUrl($build_name), $media, $condition) . "\n        ";
            }
        }
        return $css_code;
    }

    function getLink($css, $media = null, $condition = null) {
        if ($media) {
            $attr = ['media' => $media];
        }
        else {
            $attr = [];
        }
        return ($condition ? '<!--[' . $condition . ']>' : '') . \HTML::style($css, $attr) . ($condition ? '<![endif]-->' : '');
    }


    /**
     * Формирование обоих списков (внешние и инлайн стили)
     * @return string
     */
    function __toString() {
        try {
            $_css = [];
            $css  = $this->getExternal();
            if ($css) {
                $_css[] = str_replace("\n", "    \n", $css);
            }
            $css_inline = trim($this->getInline(true));
            if ($css_inline) {
                $css_inline = str_replace("\r\n", "\r\n    ", $css_inline);
                $_css[]     = "        " . str_replace("\n", "\n    ", $css_inline);
            }
            return implode("\n", $_css);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


}