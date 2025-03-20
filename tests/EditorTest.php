<?php

namespace Codeeditor;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;

class EditorTest extends TestCase
{
    public function testIncludesEditor(): void
    {
        global $hjs;

        $hjs = "";
        $sut = new Editor("../", "night", "", $this->view());
        $sut->doInclude(new FakeRequest());
        Approvals::verifyHtml($hjs);
    }

    public function testIncludesEditorWithFilebrowserWhenAdmin(): void
    {
        global $hjs;

        $hjs = "";
        $sut = new Editor("../", "night", "", $this->view());
        $request = new FakeRequest([
            "url" => "http://example.com/de/",
            "admin" => true,
        ]);
        $sut->doInclude($request);
        Approvals::verifyHtml($hjs);
    }

    public function testInitializesEditor(): void
    {
        global $hjs, $bjs;

        $hjs = $bjs = "";
        $sut = new Editor("../", "default", "", $this->view());
        $sut->init(new FakeRequest());
        $this->assertStringContainsString("<script src=\"../codeeditor/codeeditor.min.js\"></script>", $hjs);
        Approvals::verifyHtml($bjs);
    }

    public function testReplacesTextarea(): void
    {
        $sut = new Editor("../", "default", "", $this->view());
        Approvals::verifyHtml($sut->replace("my_textarea", ""));
    }

    public function testWarnsAboutInvalidConfig(): void
    {
        global $e;

        $e = "";
        $sut = new Editor("../", "default", "", $this->view());
        $this->assertSame(
            "codeeditor.instantiate('my_textarea', {}, true);",
            $sut->replace("my_textarea", "{invalid}")
        );
        $this->assertStringContainsString("Invalid Codeeditor_XH configuration:", $e);
    }

    public function testFindsCodeMirrorThemes(): void
    {
        $sut = new Editor("../", "default", "", $this->view());
        $this->assertContains("night", $sut->getThemes());
    }

    private function view(): View
    {
        return new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["codeeditor"]);
    }
}
