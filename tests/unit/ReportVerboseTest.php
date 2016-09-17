<?php

use Cheppers\LintReport\Reporter\VerboseReporter;
use Cheppers\LintReport\ReportWrapperInterface;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class TaskScssLintRunTest.
 */
// @codingStandardsIgnoreStart
class ReportVerboseTest extends ReportTestBase
{
    // @codingStandardsIgnoreEnd

    /**
     * {@inheritdoc}
     */
    protected $reporterName = 'verbose';

    /**
     * {@inheritdoc}
     */
    protected $reporterOutputExtension = 'txt';

    /**
     * @dataProvider casesGenerate
     *
     * @param ReportWrapperInterface $reportWrapper
     * @param array $source
     * @param string|null $filePathStyle
     * @param string $expected
     */
    public function testGenerate(
        ReportWrapperInterface $reportWrapper,
        array $source,
        $filePathStyle,
        $expected
    ) {
        $reporter = new VerboseReporter();
        $destination = new BufferedOutput();
        $reporter
            ->setReportWrapper($reportWrapper)
            ->setBasePath('/foo')
            ->setFilePathStyle($filePathStyle)
            ->generate($source, $destination);
        $actual = $destination->fetch();
        static::assertEquals($expected, $actual);
    }
}
