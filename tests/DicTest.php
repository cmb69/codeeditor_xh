<?php

namespace Codeeditor;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_tx;

        $pth = ["folder" => ["plugins" => ""]];
        $plugin_tx = ["codeeditor" => []];
    }

    public function testMakesEditor(): void
    {
        $this->assertInstanceOf(Editor::class, Dic::editor());
    }

    public function testMakesMainCommand(): void
    {
        $this->assertInstanceOf(MainCommand::class, Dic::mainCommand());
    }

    public function testMakesInfoCommand(): void
    {
        $this->assertInstanceOf(InfoCommand::class, Dic::infoCommand());
    }
}
