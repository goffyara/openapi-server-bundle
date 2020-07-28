<?php

declare(strict_types=1);

namespace OnMoon\OpenApiServerBundle\Command;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use function is_dir;
use function Safe\rmdir;
use function Safe\sprintf;
use function Safe\unlink;

class DeleteGeneratedCodeCommand extends Command
{
    public const COMMAND = 'open-api:delete';

    private string $rootPath;

    public function __construct(string $rootPath, ?string $name = null)
    {
        $this->rootPath = $rootPath;

        parent::__construct($name);
    }

    /**
     * phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var string
     */
    protected static $defaultName = self::COMMAND;

    protected function configure(): void
    {
        $this
            ->setDescription('Deletes API server code generated by the bundle')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('yes', 'y', InputOption::VALUE_NONE, 'Disable confirmation prompt'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! (bool) $input->getOption('yes')) {
            /** @var QuestionHelper $questionHelper */
            $questionHelper = $this->getHelper('question');
            $question       = new ConfirmationQuestion(
                sprintf(
                    'Delete all contents of the directory %s? (y/n): ',
                    $this->rootPath
                ),
                false
            );

            if (! (bool) $questionHelper->ask($input, $output, $question)) {
                return 0;
            }
        }

        $this->recursiveDelete($this->rootPath);
        $output->writeln(sprintf('All contents of directory were deleted: %s', $this->rootPath));

        return 0;
    }

    private function recursiveDelete(string $directoryPath): void
    {
        if (! is_dir($directoryPath)) {
            return;
        }

        /** @var SplFileInfo[] $iterator */
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->rootPath,
                RecursiveDirectoryIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $directoryOrFile) {
            if ($directoryOrFile->isDir()) {
                rmdir($directoryOrFile->getPathname());
            } else {
                unlink($directoryOrFile->getPathname());
            }
        }
    }
}
