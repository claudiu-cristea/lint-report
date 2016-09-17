<?php

use Cheppers\LintReport\ReportCheckstyle;
use Cheppers\LintReport\Reporter\CheckstyleReporter;
use Cheppers\LintReport\ReportWrapperInterface;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class TaskScssLintRunTest.
 */
// @codingStandardsIgnoreStart
class ReportCheckstyleTest extends ReportTestBase
{
    // @codingStandardsIgnoreEnd

    /**
     * {@inheritdoc}
     */
    protected $reporterName = 'checkstyle';

    /**
     * {@inheritdoc}
     */
    protected $reporterOutputExtension = 'xml';

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
        $reporter = new CheckstyleReporter();
        $destination = new BufferedOutput();
        $reporter
            ->setReportWrapper($reportWrapper)
            ->setBasePath('/foo')
            ->setFilePathStyle($filePathStyle)
            ->generate($source, $destination);
        static::assertEquals($expected, $destination->fetch());
    }

    public function testSetFilePathStyle()
    {
        $reporter = new CheckstyleReporter();
        try {
            $reporter->setFilePathStyle('invalid');
            $this->fail('Expected exception is missing.');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, 'Invalid value cannot be applied.');
        }
    }
}
