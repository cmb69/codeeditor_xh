<?php

namespace Codeeditor;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;

class MainCommandTest extends TestCase
{
    public function testIgnoresUnrelatedRequests(): void
    {
        $sut = new MainCommand(new Editor("../", "default", "", $this->view()));
        $request = new FakeRequest();
        $this->assertNull($sut($request)->bjs());
    }

    public function testInitializesEditorForTemplate(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&file=template"]);
        $sut = new MainCommand(new Editor("../", "default", "", $this->view()));
        Approvals::verifyHtml($sut($request)->bjs());
    }

    public function testInitializesEditorForStylesheet(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&file=stylesheet"]);
        $sut = new MainCommand(new Editor("../", "default", "", $this->view()));
        Approvals::verifyHtml($sut($request)->bjs());
    }

    private function view(): View
    {
        return new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["codeeditor"]);
    }
}
