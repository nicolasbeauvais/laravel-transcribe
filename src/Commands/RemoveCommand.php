<?php

namespace NicolasBeauvais\Transcribe\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use NicolasBeauvais\Transcribe\Manager;

class RemoveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transcribe:remove {key}';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $description = 'Remove the given key from all language files.';

    /**
     * The Languages manager instance.
     *
     * @var \NicolasBeauvais\Transcribe\Manager
     */
    private $manager;

    /**
     * Array of requested file in different languages.
     *
     * @var array
     */
    protected $files;

    /**
     * ListCommand constructor.
     *
     * @param \NicolasBeauvais\Transcribe\Manager $manager
     * @return void
     */
    public function __construct(Manager $manager)
    {
        parent::__construct();

        $this->manager = $manager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            list($file, $key) = explode('.', $this->argument('key'), 2);
        } catch (\ErrorException $e) {
            $this->error('Could not recognize the key you want to remove.');

            return;
        }

        if ($this->confirm("Are you sure you want to remove \"{$file}.{$key}\"?")) {
            if (Str::contains($file, '::')) {
                try {
                    $parts = explode('::', $file);

                    $this->manager->setPathToVendorPackage($parts[0]);

                    $file = $parts[1];
                } catch (\ErrorException $e) {
                    $this->error('Could not recognize the package.');

                    return;
                }
            }

            $this->manager->removeKey($file, $key);

            $this->info("{$file}.{$key} was removed successfully.");
        }
    }
}
