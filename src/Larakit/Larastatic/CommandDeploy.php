<?php
namespace Larakit\Larastatic;

use Illuminate\Support\Arr;

class CommandDeploy extends \Illuminate\Console\Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larakit:static-deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy static files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
        $used = \Config::get('larastatic::larastatic.used');
        foreach ($used as $lib => $path) {
            $arguments           = [
                'package' => $lib,
            ];
            if(true!==$path){
                $arguments['--path'] = 'vendor/' . $lib . '/' . ($path ? trim($path, '/') . '/' : '');
            }
            \Artisan::call('asset:publish', $arguments, $this->output);
        }
        $this->info('Static files deployed!');
    }


}