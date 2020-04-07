<?php
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase {
    /**
     * Run command in Docker
     * @param  string      $command Command
     * @return string|null          Result
     */
    protected function runCommand(string $command): ?string {
        return shell_exec('docker exec ' . escapeshellarg(getenv('CONTAINER_NAME')) . ' ' . $command);
    }
}
