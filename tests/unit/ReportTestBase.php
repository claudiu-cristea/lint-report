<?php
use Symfony\Component\Yaml\Yaml;

/**
 * Class ReportTestBase.
 */
// @codingStandardsIgnoreStart
class ReportTestBase extends \Codeception\Test\Unit
{
    // @codingStandardsIgnoreEnd

    /**
     * @var string
     */
    protected $reporterName = '';

    /**
     * @var string
     */
    protected $reporterOutputExtension = '';

    /**
     * @return array
     */
    public function casesGenerate()
    {
        $cases = [];

        $dataDir = rtrim(codecept_data_dir(), '/');
        $file = new \DirectoryIterator("$dataDir/source");

        $sourceType2WrapperClass = [
            'eslint' => \Cheppers\LintReport\Wrapper\ESLint\ReportWrapper::class,
            'phpcs' => \Cheppers\LintReport\Wrapper\Phpcs\ReportWrapper::class,
            'scss-lint' => \Cheppers\LintReport\Wrapper\ScssLint\ReportWrapper::class,
            'tslint' => \Cheppers\LintReport\Wrapper\TSLint\ReportWrapper::class,
        ];

        while ($file->valid()) {
            if ($file->isDir()) {
                $file->next();

                continue;
            }

            $baseName = $file->getBasename();
            list($sourceType, $number, $extension) = explode('.', $baseName);
            $baseName = "$sourceType.$number";

            foreach (['relative', 'absolute', null] as $filePathStyle) {
                $filePathStyleStr = ($filePathStyle ?: 'null');
                $expected = implode('.', [
                    $this->reporterName,
                    $number,
                    $filePathStyleStr,
                    $this->reporterOutputExtension,
                ]);

                $caseId = "{$this->reporterName}.$baseName.$filePathStyleStr";

                $wrapperClass = $sourceType2WrapperClass[$sourceType];

                $cases[$caseId] = [
                    'reportWrapper' => new $wrapperClass(),
                    'source' => null,
                    'filePathStyle' => $filePathStyle,
                    'expected' => file_get_contents("$dataDir/expected/$expected"),
                ];

                if ($extension === 'json') {
                    $cases[$caseId]['source'] = json_decode(file_get_contents($file->getPathname()), true);
                } else {
                    $cases[$caseId]['source'] = $this->yamlParse($file->getPathname());
                }
            }

            $file->next();
        }

        return $cases;
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    protected function yamlParse($fileName)
    {
        if (function_exists('yaml_parse_file')) {
            return yaml_parse_file($fileName, -1);
        }

        return $this->yamlParseSymfonyMultiDocument($fileName);
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    protected function yamlParseSymfonyMultiDocument($fileName)
    {
        $documents = preg_split(
            '@(^|\n)---\n(?=failures:\n)@',
            file_get_contents($fileName),
            PREG_SPLIT_NO_EMPTY
        );

        for ($i = 0; $i < count($documents); $i++) {
            $documents[$i] = Yaml::parse($documents[$i]);
        }

        return $documents;
    }
}
