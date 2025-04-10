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
        $sut = new Editor("../", "night", "", $this->view());
        $response = $sut->doInclude(new FakeRequest());
        Approvals::verifyHtml($response->hjs());
    }

    public function testIncludesEditorWithFilebrowserWhenAdmin(): void
    {
        $sut = new Editor("../", "night", "", $this->view());
        $request = new FakeRequest([
            "url" => "http://example.com/de/",
            "admin" => true,
        ]);
        $response = $sut->doInclude($request);
        Approvals::verifyHtml($response->hjs());
    }

    public function testInitializesEditor(): void
    {
        $sut = new Editor("../", "default", "", $this->view());
        $response = $sut->init(new FakeRequest());
        $this->assertStringContainsString(
            "<script src=\"/codeeditor.js?&amp;v=2.3-dev\"></script>",
            $response->hjs()
        );
        Approvals::verifyHtml($response->bjs());
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
