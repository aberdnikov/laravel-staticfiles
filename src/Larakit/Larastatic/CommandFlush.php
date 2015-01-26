<?php
namespace Larakit\Larastatic;

use Illuminate\Support\Arr;

class CommandFlush extends \Illuminate\Console\Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larakit:static-flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush deployed static files';

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
        dd(\Config::get('larakit::larastatic'));
        dd(app('path.public').\Config::get('staticfiles'));
    }


}