<?php

namespace Cheppers\LintReport\Reporter;

use Cheppers\LintReport\ReporterInterface;
use Cheppers\LintReport\ReportWrapperInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class BaseReporter.
 *
 * @package Cheppers\LintReport\Reporter
 */
abstract class BaseReporter implements ReporterInterface
{

    /**
     * @param \League\Container\ContainerInterface $container
     */
    public static function lintReportConfigureContainer($container)
    {
        $container->share('lintCheckstyleReporter', CheckstyleReporter::class);
        $container->share('lintSummaryReporter', SummaryReporter::class);
        $container->share('lintVerboseReporter', VerboseReporter::class);
    }

    /**
     * @var ReportWrapperInterface
     */
    protected $reportWrapper = null;

    /**
     * Output destination.
     *
     * @var string|\Symfony\Component\Console\Output\OutputInterface
     */
    protected $destination = null;

    /**
     * Output destination mode.
     *
     * @var string
     */
    protected $destinationMode = 'w';

    /**
     * Output destination.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $destinationOutput = null;

    /**
     * File handler.
     *
     * @var resource
     */
    protected $destinationResource = null;

    /**
     * @var string
     */
    protected $basePath = '';

    /**
     * @var string|null
     */
    protected $filePathStyle = null;

    /**
     * ReportBase constructor.
     */
    public function __construct()
    {
        $this->setBasePath(getcwd());
    }

    /**
     * @return ReportWrapperInterface
     */
    public function getReportWrapper()
    {
        return $this->reportWrapper;
    }

    /**
     * @param ReportWrapperInterface $reportWrapper
     *
     * @return $this
     */
    public function setReportWrapper(ReportWrapperInterface $reportWrapper)
    {
        $this->reportWrapper = $reportWrapper;

        return $this;
    }

    /**
     * @return string|\Symfony\Component\Console\Output\OutputInterface
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param string|\Symfony\Component\Console\Output\OutputInterface $destination
     *
     * @return $this
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationMode()
    {
        return $this->destinationMode;
    }

    /**
     * @param string $destinationMode
     *
     * @return $this
     */
    public function setDestinationMode($destinationMode)
    {
        $this->destinationMode = $destinationMode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $basePath
     *
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilePathStyle()
    {
        return $this->filePathStyle;
    }

    /**
     * Allowed values are: "relative", "absolute", null.
     *
     * @param string|null $value
     *
     * @return $this
     */
    public function setFilePathStyle($value)
    {
        if (!in_array($value, ['relative', 'absolute', null])) {
            throw new \InvalidArgumentException();
        }

        $this->filePathStyle = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return $this
            ->initDestination()
            ->doIt()
            ->closeDestination();
    }

    /**
     * Initialize the output destination based on the Jar values.
     *
     * @param string|OutputInterface $destination
     * @param string $destination_mode
     *
     * @return $this
     */
    protected function initDestination()
    {
        $destination = $this->getDestination();
        $destinationMode = $this->getDestinationMode();
        if (is_string($destination)) {
            $fs = new Filesystem();
            $fs->mkdir(dirname($destination));

            $this->destinationResource = fopen($destination, $destinationMode);
            $this->destinationOutput = new StreamOutput(
                $this->destinationResource,
                OutputInterface::VERBOSITY_NORMAL,
                false
            );
        } else {
            $this->destinationOutput = $destination;
        }

        return $this;
    }

    /**
     * Close the destination resource if it was opened here.
     *
     * @return $this
     */
    protected function closeDestination()
    {
        if ($this->destinationResource) {
            fclose($this->destinationResource);
        }

        return $this;
    }

    /**
     * Convert the source report.
     *
     * @return $this
     */
    abstract protected function doIt();

    /**
     * @param string $filePath
     *
     * @return string
     */
    protected function normalizeFilePath($filePath)
    {
        $file_path_style = $this->getFilePathStyle();
        if ($file_path_style === null) {
            return $filePath;
        }

        $ds = DIRECTORY_SEPARATOR;
        $isAbsolute = $this->isAbsoluteFilePath($filePath);
        if ($isAbsolute && $file_path_style === 'relative') {
            return preg_replace('@^' . preg_quote($this->getBasePath() . $ds, '@') . '@', '', $filePath);
        } elseif (!$isAbsolute && $file_path_style === 'absolute') {
            return $this->getBasePath() . $ds . $filePath;
        }

        return $filePath;
    }

    /**
     * @param string $file_path
     *
     * @return bool
     */
    protected function isAbsoluteFilePath($file_path)
    {
        $is_win = DIRECTORY_SEPARATOR === '\\';


        return $is_win ? preg_match('@^[a-zA-z]:@', $file_path) : strpos($file_path, '/') === 0;
    }

    /**
     * Set colors.
     *
     * @param string $severity
     *   Severity identifier.
     * @param string $text
     *   Text to decorate.
     *
     * @return string
     *   Decorated text.
     */
    protected function highlightHeaderBySeverity($severity, $text)
    {
        $patterns = [
            'warning' => '<fg=yellow;options=bold>%s</fg=yellow;options=bold>',
            'error' => '<fg=red;options=bold>%s</fg=red;options=bold>',
        ];

        $pattern = isset($patterns[$severity]) ? $patterns[$severity] : '<info>%s</info>';

        return sprintf($pattern, $text);
    }

    /**
     * Set colors.
     *
     * @param string $severity
     *   Severity identifier.
     * @param string $text
     *   Text to decorate.
     *
     * @return string
     *   Decorated text.
     */
    protected function highlightNormalBySeverity($severity, $text)
    {
        $patterns = [
            'warning' => '<fg=yellow>%s</fg=yellow>',
            'error' => '<fg=red>%s</fg=red>',
        ];

        $pattern = isset($patterns[$severity]) ? $patterns[$severity] : '<info>%s</info>';

        return sprintf($pattern, $text);
    }
}
