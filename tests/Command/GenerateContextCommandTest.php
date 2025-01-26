<?php

namespace TheDevOpser\SymfonyContextBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;
use TheDevOpser\SymfonyContextBundle\Command\GenerateContextCommand;

class GenerateContextCommandTest extends TestCase
{
    private $kernel;
    private $projectDir;
    private $filesystem;
    private $commandTester;

    protected function setUp(): void
    {
        $this->projectDir = dirname(__DIR__) . '/Data/test_' . uniqid();
        $this->filesystem = new Filesystem();
        
        $this->filesystem->remove($this->projectDir);
        $this->filesystem->mkdir($this->projectDir, 0777, true);
        
        $this->kernel = $this->createMock(KernelInterface::class);
        $this->kernel
            ->method('getProjectDir')
            ->willReturn($this->projectDir);
        
        $command = new GenerateContextCommand($this->kernel);
        $this->commandTester = new CommandTester($command);

        $this->filesystem->mkdir($this->projectDir . '/config/packages', 0777, true);
        $this->filesystem->mkdir($this->projectDir . '/config/routes', 0777, true);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->projectDir);
    }

    public function testExecute(): void
    {
        $this->commandTester->execute([
            'context' => 'TestContext'
        ]);

        $this->assertEquals(0, $this->commandTester->getStatusCode());
        
        $contextPath = $this->projectDir . '/src/TestContext';
        
        // Test directory structure
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

        // Vérifier l'existence des dossiers et des .gitkeep
        foreach ($directories as $directory) {
            $this->assertDirectoryExists($directory);
            $this->assertFileExists($directory . '/.gitkeep');
        }

        // Reste des tests existants
        $this->assertFileExists($contextPath . '/README.md');
        $readmeContent = file_get_contents($contextPath . '/README.md');
        $this->assertStringContainsString('TestContext Context', $readmeContent);
        $this->assertStringContainsString('Domain/', $readmeContent);
        $this->assertStringContainsString('Application/', $readmeContent);
        $this->assertStringContainsString('Infrastructure/', $readmeContent);
        $this->assertStringContainsString('Presenter/', $readmeContent);

        $this->assertFileExists($this->projectDir . '/config/packages/doctrine.yaml');
        $this->assertFileExists($this->projectDir . '/config/routes/annotations.yaml');
        
        $doctrineContent = file_get_contents($this->projectDir . '/config/packages/doctrine.yaml');
        $this->assertStringContainsString('TestContext:', $doctrineContent);
        $this->assertStringContainsString('dir: \'%kernel.project_dir%/src/TestContext/Domain/Entity\'', $doctrineContent);
        
        $routesContent = file_get_contents($this->projectDir . '/config/routes/annotations.yaml');
        $this->assertStringContainsString('TestContext_controllers:', $routesContent);
        $this->assertStringContainsString('resource: ../../src/TestContext/Presenter/Controller/', $routesContent);
    }

    public function testExecuteWithExistingContext(): void
    {
        // Création d'un contexte existant avec .gitkeep
        $contextPath = $this->projectDir.'/src/TestContext';
        $this->filesystem->mkdir($contextPath);
        $this->filesystem->touch($contextPath . '/.gitkeep');

        $this->commandTester->execute([
            'context' => 'TestContext'
        ]);

        $this->assertEquals(1, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('existe déjà', $this->commandTester->getDisplay());
    }

    public function testExecuteWithEmptyContextName(): void
    {
        $this->expectException(\RuntimeException::class);
        
        $this->commandTester->execute([
            'context' => ''
        ]);
    }

    public function testExecuteWithInvalidContextName(): void
    {
        $this->commandTester->execute([
            'context' => '123'
        ]);

        $contextPath = $this->projectDir.'/src/123';
        $this->assertDirectoryExists($contextPath);
        $this->assertEquals(0, $this->commandTester->getStatusCode());
    }
}