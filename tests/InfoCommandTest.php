<?php

namespace Codeeditor;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeSystemChecker;
use Plib\View;

class InfoCommandTest extends TestCase
{
    public function testRendersPluginInfo(): void
    {
        $sut = new InfoCommand(
            "./plugins/codeeditor/",
            new FakeSystemChecker(),
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["codeeditor"])
        );
        Approvals::verifyHtml($sut());
    }
}
