<?php
namespace App\Console\Commands;

use App\Jobs\ProcessImage;
use App\Service\ImageProcessor;
use App\Models\ImageB64;
use Illuminate\Console\Command;

use Storage;

use DB;

class ImageJobs extends Command
{

    protected $signature = "imagejobs {id_image_convertido}";

    protected $description = "Rotine of the garbage collector";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $id_image = $this->argument('id_image_convertido');
        $line = ImageB64::find((int) $id_image);
        ProcessImage::dispatch($line)->onQueue('videos');


        $this->info("Done");
        $this->newLine(2);

        return 0;
    }

}
