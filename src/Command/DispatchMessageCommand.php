<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Finder\Finder;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'vgr-core:dispatch-message',
    description: 'Dispatch any message from src/Message directory'
)]
class DispatchMessageCommand extends Command
{
    private const string MESSAGE_NAMESPACE = 'VideoGamesRecords\\CoreBundle\\Message\\';
    private const string MESSAGE_DIR = 'Message';

    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly KernelInterface $kernel
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'message-name',
                InputArgument::REQUIRED,
                'Name of the message class (e.g., UpdatePlayerData, UpdateTeamRank)'
            )
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'ID to pass to the message constructor'
            )
            ->setHelp('
This command allows you to dispatch any message from the bundle Message directory.

Examples:
  <info>php bin/console vgr-core:dispatch-message UpdatePlayerData 123</info>
  <info>php bin/console vgr-core:dispatch-message UpdateTeamRank 456</info>
  <info>php bin/console vgr-core:dispatch-message UpdatePlayerChartRank 789</info>

The command will automatically find the message class in any subdirectory of the bundle Message folder.
            ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $messageName = $input->getArgument('message-name');
        $id = (int) $input->getArgument('id');

        try {
            // Find the message class
            $messageClass = $this->findMessageClass($messageName);

            if (!$messageClass) {
                $io->error("Message class '{$messageName}' not found in bundle " . self::MESSAGE_DIR . " directory");
                $this->listAvailableMessages($io);
                return Command::FAILURE;
            }

            // Verify the class exists and is instantiable
            if (!class_exists($messageClass)) {
                $io->error("Class '{$messageClass}' does not exist");
                return Command::FAILURE;
            }

            // Create reflection to check constructor
            $reflectionClass = new ReflectionClass($messageClass);
            $constructor = $reflectionClass->getConstructor();

            if (!$constructor) {
                $io->error("Message class '{$messageClass}' has no constructor");
                return Command::FAILURE;
            }

            // Check if constructor accepts the ID parameter
            $parameters = $constructor->getParameters();
            if (empty($parameters)) {
                $io->error("Message class '{$messageClass}' constructor requires no parameters");
                return Command::FAILURE;
            }

            // Instantiate the message
            $message = new $messageClass($id);

            // Dispatch the message
            $this->bus->dispatch($message);

            $io->success("Message '{$messageName}' dispatched successfully with ID: {$id}");

            return Command::SUCCESS;
        } catch (ReflectionException $e) {
            $io->error("Reflection error: " . $e->getMessage());
            return Command::FAILURE;
        } catch (ExceptionInterface $e) {
            $io->error("Messenger error: " . $e->getMessage());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Find the message class by name in the bundle Message directory
     */
    private function findMessageClass(string $messageName): ?string
    {
        // Get the bundle directory
        $bundleDir = $this->getBundleDirectory();
        $messageDir = $bundleDir . '/' . self::MESSAGE_DIR;

        if (!is_dir($messageDir)) {
            return null;
        }

        $finder = new Finder();
        $finder->files()->in($messageDir)->name('*.php');

        foreach ($finder as $file) {
            $className = $file->getBasename('.php');

            if ($className === $messageName) {
                // Build the full class name with namespace
                $relativePath = $file->getRelativePathname();
                $namespacePath = str_replace(['/', '.php'], ['\\', ''], $relativePath);

                return self::MESSAGE_NAMESPACE . $namespacePath;
            }
        }

        return null;
    }

    /**
     * List available messages for help
     */
    private function listAvailableMessages(SymfonyStyle $io): void
    {
        $bundleDir = $this->getBundleDirectory();
        $messageDir = $bundleDir . '/' . self::MESSAGE_DIR;

        if (!is_dir($messageDir)) {
            $io->note("Message directory not found: {$messageDir}");
            return;
        }

        $finder = new Finder();
        $finder->files()->in($messageDir)->name('*.php');

        $messages = [];
        foreach ($finder as $file) {
            $className = $file->getBasename('.php');
            $relativePath = $file->getRelativePath();

            $category = $relativePath ? str_replace('/', ' > ', $relativePath) : 'Root';
            $messages[$category][] = $className;
        }

        if (empty($messages)) {
            $io->note("No message classes found in {$messageDir}");
            return;
        }

        $io->section("Available messages:");
        foreach ($messages as $category => $classList) {
            $io->writeln("<info>{$category}:</info>");
            foreach ($classList as $className) {
                $io->writeln("  - {$className}");
            }
        }
    }

    /**
     * Get the bundle directory path
     */
    private function getBundleDirectory(): string
    {
        // Get the bundle through kernel bundles
        $bundles = $this->kernel->getBundles();

        foreach ($bundles as $bundle) {
            if ($bundle instanceof \VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreBundle) {
                return dirname((new \ReflectionClass($bundle))->getFileName());
            }
        }

        // Fallback: try to determine from current class location
        $reflection = new \ReflectionClass($this);
        $commandPath = dirname($reflection->getFileName());

        // Go up from Command directory to bundle root
        return dirname($commandPath);
    }
}
