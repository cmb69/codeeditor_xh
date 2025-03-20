<?php

namespace Codeeditor;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\View;

class EditorTest extends TestCase
{
    public function testIncludesEditor(): void
    {
        global $hjs;

        $hjs = "";
        $sut = new Editor("../", "night", "", $this->view());
        $sut->doInclude();
        Approvals::verifyHtml($hjs);
    }

    public function testInitializesEditor(): void
    {
        global $hjs, $bjs;

        $hjs = $bjs = "";
        $sut = new Editor("../", "default", "", $this->view());
        $sut->init();
        Approvals::verifyHtml($bjs);
    }

    public function testReplacesTextarea(): void
    {
        $sut = new Editor("../", "default", "", $this->view());
        Approvals::verifyHtml($sut->replace("my_textarea", ""));
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
