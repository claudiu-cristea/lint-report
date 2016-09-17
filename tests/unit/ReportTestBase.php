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

        $dir = rtrim(codecept_data_dir(), '/');
        $file = new \DirectoryIterator("$dir/source");

        $parents = [
            'default' => [
                'filesParents' => [],
                'errorsParents' => [],
            ],
            'phpcs' => [
                'filesParents' => ['files'],
                'errorsParents' => ['messages'],
            ],
            'scss-lint' => [
                'filesParents' => [],
                'errorsParents' => [],
            ],
        ];

        $sourceType2WrapperClass = [
            'eslint' => \Cheppers\LintReport\Wrapper\ESLint\ReportWrapper::class,
            'phpcs' => \Cheppers\LintReport\Wrapper\Phpcs\ReportWrapper::class,
            'scss-lint' => \Cheppers\LintReport\Wrapper\ScssLint\ReportWrapper::class,
//            'tslint' => \Cheppers\LintReport\Wrapper\TSLint\RepWrapper::class,
        ];

        while ($file->valid()) {
            if ($file->isDir()) {
                $file->next();

                continue;
            }

            $base_name = $file->getBasename('.json');
            list($sourceType, $number) = explode('.', $base_name);

            foreach (['relative', 'absolute', null] as $file_path_style) {
                $file_path_style_str = ($file_path_style ?: 'null');
                $expected = implode('.', [
                    $this->reporterName,
                    $number,
                    $file_path_style_str,
                    $this->reporterOutputExtension,
                ]);

                $caseId = "{$this->reporterName}.$base_name.$file_path_style_str";

                $wrapperClass = $sourceType2WrapperClass[$sourceType];

                $cases[$caseId] = [
                    'reportWrapper' => new $wrapperClass(),
                    'source' => null,
                    'filePathStyle' => $file_path_style,
                    'expected' => file_get_contents("$dir/expected/$expected"),
                ];

                if ($file->getExtension() === 'json') {
                    $cases[$caseId]['source'] = json_decode(file_get_contents($file->getPathname()), true);
                } else {
                    $cases[$caseId]['source'] = Yaml::parse(file_get_contents($file->getPathname()));
                }
            }

            $file->next();
        }

        return $cases;
    }
}
