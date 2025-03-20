<?php

namespace Codeeditor;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\View;

class EditorTest extends TestCase
{
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
