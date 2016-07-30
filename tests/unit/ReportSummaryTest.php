<?php

use Cheppers\LintReport\ReportSummary;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class TaskScssLintRunTest.
 */
// @codingStandardsIgnoreStart
class ReportSummaryTest extends ReportTestBase
{
    // @codingStandardsIgnoreEnd

    /**
     * {@inheritdoc}
     */
    protected $reporterName = 'summary';

    /**
     * {@inheritdoc}
     */
    protected $reporterOutputExtension = 'txt';

    /**
     * @dataProvider casesGenerate
     *
     * @param string $sourceType
     * @param array $source
     * @param array $filesParents
     * @param array $errorsParents
     * @param string|null $filePathStyle
     * @param string $expected
     */
    public function testGenerate(
        $sourceType,
        array $source,
        array $filesParents,
        array $errorsParents,
        $filePathStyle,
        $expected
    ) {
        $reporter = new ReportSummary();
        $destination = new BufferedOutput();
        $reporter
            ->setErrorsParents($errorsParents)
            ->setFilesParents($filesParents)
            ->setColumnMapping($sourceType)
            ->setBasePath('/foo')
            ->setFilePathStyle($filePathStyle)
            ->generate($source, $destination);
        $actual = $destination->fetch();
        static::assertEquals($expected, $actual);
    }
}
