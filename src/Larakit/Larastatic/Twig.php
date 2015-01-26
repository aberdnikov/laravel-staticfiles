<?php namespace Larakit\Larastatic;

use Twig_Extension;
use Twig_SimpleFunction;

class Twig extends Twig_Extension {
    /**
     * {@inheritDoc}
     */
    public function getName() {
        return 'Larakit_Larastatic_Twig';
    }


    /**
     * {@inheritDoc}
     */
    public function getFunctions() {
        return [
            new Twig_SimpleFunction('css', [$this, 'f_css']),
            new Twig_SimpleFunction('js', [$this, 'f_js']),
        ];
    }

    function f_css() {
        return Css::instance()->__toString();
    }

    function f_js() {
        return Js::instance()->__toString();
    }
}