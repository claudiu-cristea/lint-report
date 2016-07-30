<?php

use Cheppers\LintReport\ReportCheckstyle;
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
        $reporter = new ReportCheckstyle();
        $destination = new BufferedOutput();
        $reporter
            ->setErrorsParents($errorsParents)
            ->setFilesParents($filesParents)
            ->setColumnMapping($sourceType)
            ->setBasePath('/foo')
            ->setFilePathStyle($filePathStyle)
            ->generate($source, $destination);
        static::assertEquals($expected, $destination->fetch());
    }
}
