<?php

use Cheppers\LintReport\ReportVerbose;
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
        $reporter = new ReportVerbose();
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
