<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class RunDevCommand extends Command
{
    protected $signature = 'dev:start';
    protected $description = 'Run Laravel development server and frontend build process';

    // Store processes for cleanup
    private $processes = [];

    public function handle()
    {
        $this->showHeader();
        
        // Check dependencies first
        if (!$this->checkDependencies()) {
            $this->error('Missing required dependencies!');
            return 1;
        }

        $this->info("Starting Laravel Server...");
        $this->runInBackground([$this->getPhpBinary(), 'artisan', 'serve', '--port=8020']);

        $this->info("Starting Development Server...");
        $this->runInBackground(['pnpm', 'run', 'dev']);

        $this->info("Listening Broadcast...");
        $this->runInBackground([$this->getPhpBinary(), 'artisan', 'queue:listen']);

        $this->info("Starting Reverb Server...");
        $this->runInBackground([$this->getPhpBinary(), 'artisan', 'reverb:start', '--host=127.0.0.1', '--port=8081']);

        $this->showSuccessMessage();

        // Keep the process running
        while (true) {
            sleep(1);
        }
        
        return 0;
    }

    private function showHeader()
    {
        $this->line("\n");
        $this->line("==============================================");
        $this->line("    LARAVEL DEVELOPMENT SERVER MANAGER");
        $this->line("==============================================");
        $this->line("\n");
    }

    private function showSuccessMessage()
    {
        $this->line("\n");
        $this->line("==============================================");
        $this->line("            SERVER IS NOW ONLINE");
        $this->line("==============================================");
        
        // Create clickable link using ANSI escape codes
        $url = "http://127.0.0.1:8020";
        $clickableUrl = "\e]8;;{$url}\e\\{$url}\e]8;;\e\\";
        
        $this->line("\n");
        $this->line("   🌐 <options=bold>Local Server:</> <fg=blue;options=bold>{$clickableUrl}</>");
        $this->line("   📊 <options=bold>Vite Dev Server:</> <fg=blue>http://localhost:5173</>");
        $this->line("   🔊 <options=bold>Reverb Server:</> <fg=blue>http://127.0.0.1:8081</>");
        $this->line("\n");
        $this->line("   Press <fg=red>Ctrl+C</> to stop all servers");
        $this->line("\n");
        $this->line("==============================================");
    }

    private function runInBackground(array $command)
    {
        $process = new Process($command);
        $process->setTimeout(null);
        
        // For Windows compatibility
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $process->setOptions(['create_new_console' => true]);
        }
        
        $process->start();
        
        // Store processes for cleanup
        $this->processes[] = $process;
        
        return $process;
    }

    private function getPhpBinary(): string
    {
        return (new PhpExecutableFinder())->find(false) ?: 'php';
    }

    private function checkDependencies(): bool
    {
        $this->info("Checking dependencies...");
        
        $dependencies = [
            'PHP' => $this->getPhpBinary(),
            'Node.js' => $this->findExecutable('node'),
            'pnpm' => $this->findExecutable('pnpm') || $this->findExecutable('npx')
        ];
        
        $allGood = true;
        
        foreach ($dependencies as $name => $result) {
            if ($result) {
                $this->line("   ✅ {$name}");
            } else {
                $this->error("   ❌ {$name}");
                $allGood = false;
            }
        }
        
        return $allGood;
    }

    private function findExecutable(string $command): ?string
    {
        $process = new Process([strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where' : 'which', $command]);
        $process->run();
        
        return $process->isSuccessful() ? trim($process->getOutput()) : null;
    }
    
    public function __destruct()
    {
        // Clean up processes when object is destroyed
        foreach ($this->processes as $process) {
            if ($process->isRunning()) {
                $process->stop();
            }
        }
    }
}