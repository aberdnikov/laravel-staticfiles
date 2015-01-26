<?php
namespace Larakit\Larastatic;

class File {
    function save($file, $data) {
        /**
         * Блокируем файл при записи
         * http://forum.dklab.ru/viewtopic.php?p=96622#96622
         */
        // Вначале создаем пустой файл, ЕСЛИ ЕГО ЕЩЕ НЕТ.
        // Если же файл существует, это его не разрушит.
        fclose(fopen($file, "a+b"));
        // Блокируем файл.
        $f = fopen($file, "r+b") or die("Не могу открыть файл!");
        flock($f, LOCK_EX); // ждем, пока мы не станем единственными
        // В этой точке мы можем быть уверены, что только эта
        // программа работает с файлом.
        fwrite($f, trim($data));
        fclose($f);
    }

    protected function makeFileName($data, $prefix, $ext) {
        $prefix    = strtolower(preg_replace('/[^A-Za-z0-9_\-\/]/', '-', $prefix));
        $prefix    = $prefix ? ($prefix . '/') : '';
        $file_name = md5(serialize($data));
        return $prefix . substr($file_name, 0, 1) . '/' . substr($file_name, 1, 1) . '/' . $file_name . '.' . $ext;
    }

    protected function buildFile($file) {
        return \Config::get('larakit::larastatic.build_dir') . \Config::get('larakit::larastatic.version') . '/' . $file;
    }

    protected function buildUrl($file) {
        return \Config::get('larakit::larastatic.host') . \Config::get('larakit::larastatic.build_url') . \Config::get('larakit::larastatic.version') . '/' . $file;
    }


}