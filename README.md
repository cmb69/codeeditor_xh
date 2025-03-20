# Codeeditor\_XH

Codeeditor\_XH provides a replacement for the simple textarea
in the back-end of CMSimple\_XH to edit the template,
the stylesheet and the plugin stylesheets.
Furthermore it can be used to edit the content of the website.

Instead of being a WYSIWYG or WYSIWYM editor,
it allows to edit the source code directly,
and offers syntax highlighting, line numbering, code indenting
and folding, bracket matching, optional line wrapping,
filebrowser integration and search and replace.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
- [Limitations](#limitations)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

## Requirements

Codeeditor_XH is a plugin for [CMSimple_XH](https://cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0 and PHP ≥ 7.1.0.

## Download

The [lastest release](https://github.com/cmb69/codeeditor_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple\_XH plugins.

1. Backup the data on your server.
1. Unzip the distribution on your computer.
1. Upload the whole directory `codeeditor/` to your server
   into the `plugins/` directory of CMSimple\_XH.
1. Set write permissions for the subdirectories
   `config/`, `css/` and `languages/`.
1. Navigate to `Plugins` → `Codeeditor` in the back-end
   to check if all requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple\_XH
plugins in the back-end of the Website.
Navigate to `Plugins` → `Codeeditor`.

You can change the default settings of Codeeditor\_XH under `Config`.
Hints for the options will be displayed
when hovering over the help icon with your mouse.

Localization is done under `Language`.
You can translate the character strings to your own language
if there is no appropriate language file available,
or customize them according to your needs.

The style of Codeeditor\_XH can be customized under `Stylesheet`.

Further configuration can be done in `plugins/codeeditor/inits/init.json`.
The options are explained in the
[CodeMirror user manual](https://codemirror.net/doc/manual.html#config).

## Usage

To enable Codeeditor\_XH as content editor go to
`Settings` → `Configuration` → `Editor` → `Name` and enter `codeeditor`.

The editor has no toolbar or context menu,
but instead uses keyboard shortcuts,
which can be configured in the init file.
Some noteworthy ones:

| Function             | PC           | Mac             |
|----------------------|:------------:|:---------------:|
| save                 | Ctrl-S       | Cmd-S           |
| find                 | Ctrl-F       | Cmd-F           |
| find next            | Ctrl-G       | Cmd-G           |
| find prev            | Shift-Ctrl-G | Shift-Cmd-G     |
| replace              | Shift-Ctrl-F | Cmd-Alt-F       |
| replace all          | Shift-Ctrl-R | Shift-Cmd-Alt-F |
| toggle fullscreen    | Esc          | Esc             |
| toggle folding       | Ctrl-Q       | Cmd-Q           |
| toggle line wrapping | Alt-W        | Alt-W           |
| toggle preview       | Crtl-P       | Ctrl-P          |
| browse images        | Ctrl-I       | Cmd-I           |
| browse downloads     | Ctrl-L       | Cmd-L           |
| browse media         | Ctrl-M       | Cmd-M           |
| browse userfiles     | Ctrl-U       | Cmd-U           |

## Limitations

The browse functions only support the standard filebrowser of CMSimple\_XH.

## Troubleshooting

Report bugs and ask for support either on
[Github](https://github.com/cmb69/codeeditor_xh/issues)
or in the [CMSimple\_XH Forum](https://cmsimpleforum.com/).

## License

Codeeditor\_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Codeeditor\_XH is distributed in the hope that it will be useful,
but *without any warranty*; without even the implied warranty of
*merchantibility* or *fitness for a particular purpose*. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Codeeditor\_XH.  If not, see <https://www.gnu.org/licenses/>.

Copyright 2011-2021 Christoph M. Becker

Czech translation © 2012 Josef Němec  
Slovak translation © 2012 Dr. Martin Sereday  
Russian translation © 2012 Lybomyr Kydray

## Credits

Codeeditor\_XH is powered by [CodeMirror](https://codemirror.net/).
Many thanks to Marijn Haverbeke for developing this great piece of software
and publishing it under MIT license.

The plugin icon was taken from the
[Oxygen icon set](http://www.oxygen-icons.org/).
Many thanks for publishing these icons under GPL.

Many thanks to the community at the
[CMSimple forum](https://www.cmsimpleforum.com/)
for tips, suggestions and testing.

And last but not least many thanks to
[Peter Harteg](https://www.harteg.dk/), the “father” of CMSimple,
and all developers of [CMSimple\_XH](http://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.
