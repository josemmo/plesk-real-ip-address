<?php
final class InstallTest extends AbstractTestCase {
    /**
     * Is extension installed
     */
    public function testIsInstalled() {
        $list = $this->runCommand('plesk bin extension -l');
        $this->assertStringContainsString('real-ip-address', $list);
    }
}
