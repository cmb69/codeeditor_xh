<?php

namespace Codeeditor;

use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;

class MainCommandTest extends TestCase
{
    public function testIgnoresUnrelatedRequests(): void
    {
        $editor = $this->createMock(Editor::class);
        $editor->expects($this->never())->method("init");
        $sut = new MainCommand($editor);
        $request = new FakeRequest();
        $sut($request);
    }

    public function testInitializesEditorForTemplate(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&file=template"]);
        $editor = $this->createMock(Editor::class);
        $editor->expects($this->once())->method("init")->with(
            $request,
            ["xh_file_edit"],
            "",
            "php",
            false,
        );
        $sut = new MainCommand($editor);
        $sut($request);
    }

    public function testInitializesEditorForStylesheet(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&file=stylesheet"]);
        $editor = $this->createMock(Editor::class);
        $editor->expects($this->once())->method("init")->with(
            $request,
            ["xh_file_edit"],
            "",
            "css",
            false,
        );
        $sut = new MainCommand($editor);
        $sut($request);
    }
}
