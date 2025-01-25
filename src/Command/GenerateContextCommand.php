<?php

namespace TheDevOpser\SymfonyContextBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class GenerateContextCommand extends Command
{
    protected static $defaultName = 'thedevopser:generate:context';
    private string $projectDir;
    private Filesystem $filesystem;

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();
        $this->projectDir = $kernel->getProjectDir();
        $this->filesystem = new Filesystem();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Génère une structure de dossiers pour un contexte métier')
            ->addArgument(
                'context',
                InputArgument::REQUIRED,
                'Le nom du contexte métier'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = ucfirst($input->getArgument('context'));
        
        if (empty($context)) {
            throw new \RuntimeException('Le nom du contexte ne peut pas être vide');
        }
        
        $contextPath = rtrim($this->projectDir, '/') . '/src/' . $context;

        if ($this->filesystem->exists($contextPath)) {
            $output->writeln(sprintf('<error>Le contexte %s existe déjà !</error>', $context));
            return Command::FAILURE;
        }

        $this->createDirectoryStructure($contextPath, $output);
        $this->createReadmeFile($contextPath, $context, $output);
        $this->updateDoctrineConfig($context, $output);
        $this->updateRoutesConfig($context, $output);
        
        $output->writeln(sprintf('<info>La structure pour le contexte %s a été générée avec succès !</info>', $context));
        
        return Command::SUCCESS;
    }

    private function createDirectoryStructure(string $contextPath, OutputInterface $output): void
    {
        $directories = [
            $contextPath,
            $contextPath . '/Domain',
            $contextPath . '/Domain/Entity',
            $contextPath . '/Domain/Interfaces',
            $contextPath . '/Application',
            $contextPath . '/Application/Command',
            $contextPath . '/Application/Query',
            $contextPath . '/Application/Event',
            $contextPath . '/Application/Service',
            $contextPath . '/Infrastructure',
            $contextPath . '/Infrastructure/Doctrine',
            $contextPath . '/Infrastructure/Persistence',
            $contextPath . '/Presenter',
            $contextPath . '/Presenter/Controller',
            $contextPath . '/Presenter/Form',
            $contextPath . '/Presenter/Voter',
        ];

        foreach ($directories as $directory) {
            $this->filesystem->mkdir($directory);
            $output->writeln(sprintf('Création du dossier : <info>%s</info>', $directory));
        }
    }

    private function createReadmeFile(string $contextPath, string $context, OutputInterface $output): void
    {
        $readmePath = $contextPath . '/README.md';
        $readmeContent = <<<EOT
# {$context} Context

Ce dossier contient le code source lié au contexte métier {$context}.

## Structure

- `Domain/`: Le cœur métier
  - `Entity/`: Les entités et value objects
  - `Interfaces/`: Les interfaces des repositories
  - `Service/`: Les services métier
  - `Event/`: Les événements domaine
  - `Exception/`: Les exceptions métier

- `Application/`: La couche application
  - `Command/`: Les commandes et leurs handlers
  - `Query/`: Les requêtes et leurs handlers
  - `Event/`: Les gestionnaires d'événements
  - `Service/`: Les services applicatifs

- `Infrastructure/`: La couche infrastructure
  - `Doctrine/`: L'implémentation des repositories
  - `Persistence/`: Configuration de la persistance

- `Presenter/`: La couche présentation
  - `Controller/`: Les contrôleurs
  - `Form/`: Les types de formulaires
  - `Voter/`: Les voters
EOT;

        $this->filesystem->dumpFile($readmePath, $readmeContent);
        $output->writeln(sprintf('Création du fichier : <info>%s</info>', $readmePath));
    }

    private function updateDoctrineConfig(string $context, OutputInterface $output): void
    {
        $doctrinePath = $this->projectDir . '/config/packages/doctrine.yaml';
        
        if (!$this->filesystem->exists($doctrinePath)) {
            $output->writeln('<comment>Le fichier doctrine.yaml n\'existe pas, création...</comment>');
            $this->filesystem->dumpFile($doctrinePath, "doctrine:\n    orm:\n        mappings:");
        }

        $doctrineContent = file_get_contents($doctrinePath);
        $contextMapping = <<<YAML

            {$context}:
                is_bundle: false
                type: attribute
                dir: '%kernel.project_dir%/src/{$context}/Domain/Entity'
                prefix: 'App\\{$context}\\Domain\\Entity'
                alias: {$context}
YAML;

        if (!str_contains($doctrineContent, $context . ':')) {
            $doctrineContent = str_replace('mappings:', 'mappings:' . $contextMapping, $doctrineContent);
            $this->filesystem->dumpFile($doctrinePath, $doctrineContent);
            $output->writeln(sprintf('<info>Configuration Doctrine mise à jour pour %s</info>', $context));
        }
    }

    private function updateRoutesConfig(string $context, OutputInterface $output): void
    {
        $routesPath = $this->projectDir . '/config/routes/annotations.yaml';
        
        if (!$this->filesystem->exists($routesPath)) {
            $output->writeln('<comment>Le fichier annotations.yaml n\'existe pas, création...</comment>');
            $this->filesystem->dumpFile($routesPath, "");
        }

        $routesContent = file_get_contents($routesPath);
        $contextRoutes = <<<YAML
{$context}_controllers:
    resource: ../../src/{$context}/Presenter/Controller/
    type: attribute
    prefix: /{$context}

YAML;

        if (!str_contains($routesContent, $context . '_controllers:')) {
            $this->filesystem->appendToFile($routesPath, "\n" . $contextRoutes);
            $output->writeln(sprintf('<info>Configuration des routes mise à jour pour %s</info>', $context));
        }
    }
}