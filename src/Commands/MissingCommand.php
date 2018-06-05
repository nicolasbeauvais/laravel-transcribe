<?php

namespace NicolasBeauvais\Transcribe\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use NicolasBeauvais\Transcribe\Manager;

class MissingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transcribe:missing {--default}';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $description = 'Find missing translation values and fill them.';

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
     *
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
        $this->info('Looking for missing translations...');

        $languages = $this->manager->languages();

        $missing = $this->getMissing($languages);

        $values = $this->translateValues($missing);

        $this->info('Done!');
    }

    /**
     * Collect translation values from console via questions.
     *
     * @param array $missing
     *
     * @return array
     */
    private function translateValues(array $missing)
    {
        $values = [];

        foreach ($missing as $missingKey) {
            $value = $this->ask(
                "<fg=yellow>{$missingKey}</> translation", $this->getDefaultValue($missingKey)
            );

            preg_match('/^([^\.]*)\.(.*):(.*)/', $missingKey, $matches);

            $this->manager->fillKeys(
                $matches[1],
                [$matches[2] => [$matches[3] => $value]]
            );

            $this->line("\"<fg=yellow>{$missingKey}</>\" was set to \"<fg=yellow>{$value}</>\" successfully.");
        }

        return $values;
    }

    /**
     * Get translation in default locale for the given key.
     *
     * @param string $missingKey
     *
     * @return string
     */
    private function getDefaultValue($missingKey)
    {
        if (!$this->option('default')) {
            return;
        }

        try {
            $missingKey = explode(':', $missingKey)[0];

            $decomposedKey = explode('.', $missingKey);
            $file = array_shift($decomposedKey);
            $key = implode('.', $decomposedKey);

            $filePath = $this->manager->files()[$file][config('app.fallback_locale')];

            return config('app.fallback_locale') . ':' . array_get($this->manager->getFileContent($filePath), $key);
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * Get an array of keys that have missing values with a hint
     * from another language translation file if possible.
     *
     * ex: [ ['key' => 'product.color.nl', 'hint' => 'en = "color"'] ]
     *
     * @param array $languages
     *
     * @return array
     */
    private function getMissing(array $languages)
    {
        $files = $this->manager->files();

        // Array of content of all files indexed by fileName.languageKey
        $filesResults = [];

        // The final output of the method
        $missing = [];

        // Here we collect the file results
        foreach ($files as $fileName => $languageFiles) {
            foreach ($languageFiles as $languageKey => $filePath) {
                $filesResults[$fileName][$languageKey] = $this->manager->getFileContent($filePath);
            }
        }

        $values = Arr::dot($filesResults);

        $emptyValues = array_filter($values, function ($value) {
            return $value == '';
        });

        // Adding all keys that has values = ''
        foreach ($emptyValues as $dottedValue => $emptyValue) {
            list($fileName, $languageKey, $key) = explode('.', $dottedValue, 3);

            $missing[] = "{$fileName}.{$key}:{$languageKey}";
        }

        $missing = array_merge($missing, $this->manager->getKeysExistingInALanguageButNotTheOther($values));

        return $missing;
    }
}
