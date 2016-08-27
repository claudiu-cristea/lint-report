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

    public function testSetFilePathStyle()
    {
        $reporter = new ReportCheckstyle();
        try {
            $reporter->setFilePathStyle('invalid');
            $this->fail('Expected exception is missing.');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, 'Invalid value cannot be applied.');
        }
    }

    public function testSetColumnMapping()
    {
        $reporter = new ReportCheckstyle();

        $reporter->setColumnMapping('phpcs');
        $this->assertEquals(
            ReportCheckstyle::$columnMappings['phpcs'],
            $reporter->getColumnMapping()
        );

        $reporter->setColumnMapping('');
        $this->assertEquals(
            ReportCheckstyle::$columnMappings['default'],
            $reporter->getColumnMapping()
        );

        try {
            $reporter->setColumnMapping('invalid');
            $this->fail('Expected exception is missing.');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, 'Invalid value cannot be applied.');
        }
    }
}
