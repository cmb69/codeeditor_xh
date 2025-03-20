<?php

namespace Codeeditor;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $cf, $plugin_cf, $plugin_tx;

        $pth = ["folder" => ["plugins" => ""]];
        $cf = ["filebrowser" => ["external" => ""]];
        $plugin_cf = ["codeeditor" => ["theme" => ""]];
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
