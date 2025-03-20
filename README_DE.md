# Codeeditor\_XH

Codeeditor\_XH bietet einen Ersatz für die einfachen Textareas
im Administrationsmodus von CMSimple\_XH,
um das Template, das Stylesheet und die Stylesheets der Plugins zu bearbeiten.
Des weiteren kann es als Inhalts-Editor der Website verwendet werden.

Codeeditor\_XH ist kein WYSIWYG oder WYSIWYM Editor,
sondern es erlaubt den Quellcode direkt zu bearbeiten,
und bietet Syntaxhighlighting, Zeilennummerierung,
Code-Einrückung und -Faltung, Anzeige zusammengehöriger Klammernpaare,
optionales Zeilenumbrechen, Filebrowser-Integration
und Suchen und Ersetzen.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Einschränkungen](#einschränkungen)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Codeeditor_XH ist ein Plugin für [CMSimple_XH](https://cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0 und PHP ≥ 7.1.0.

## Download

Das [aktuelle Release](https://github.com/cmb69/codeeditor_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple\_XH-Plugins auch.

1. Sichern Sie die Daten auf Ihrem Server.
1. Entpacken Sie die ZIP-Datei auf Ihrem Rechner.
1. Laden Sie das ganze Verzeichnis `codeeditor/` auf Ihren Server
   in das `plugins/` Verzeichnis von CMSimple\_XH hoch.
1. Vergeben Sie falls nötig Schreibrechte für die Unterverzeichnisse
   `config/`, `css/` und `languages/`.
1. Gehen Sie zu `Plugins` → `Codeeditor` im Administrationsbereich,
   um zu prüfen, ob alle Voraussetzungen erfüllt sind.

## Einstellungen

Die Plugin-Konfiguration erfolgt wie bei vielen anderen
CMSimple\_XH-Plugins auch im Administrationsbereich der Website.
Gehen Sie zu `Plugins` → `Codeeditor`.

Sie können die Voreinstellungen von Codeeditor\_XH unter `Konfiguration` ändern.
Beim Überfahren der Hilfe-Icons mit der Maus
werden Hinweise zu den Einstellungen angezeigt.

Die Lokalisierung wird unter `Sprache` vorgenommen.
Sie können die Sprachtexte in Ihre eigene Sprache übersetzen,
falls keine entsprechende Sprachdatei zur Verfügung steht,
oder diese Ihren Wünschen gemäß anpassen.

Das Aussehen von Codeeditor\_XH kann unter `Stylesheet` angepasst werden.

Weitere Einstellungen können in `plugins/codeeditor/inits/init.json`
vorgenommen werden.
Die Optionen werden im
[CodeMirror Benutzerhandbuch](https://codemirror.net/doc/manual.html#config)
erklärt.

## Verwendung

Um Codeeditor\_XH als Inhalts-Editor zu verwenden,
tragen Sie unter `Einstellungen` → `Konfiguration` → `Editor` → `Name`
`codeeditor` ein.

Der Editor hat weder eine Werkzeugleiste noch ein Kontextmenü,
sondern wird mit Tastenkürzeln bedient,
die in der init Datei konfiguriert werden können.
Einige erwähnenswerte:

| Funktion                    | PC           | Mac             |
|-----------------------------|:------------:|:---------------:|
| Speichern                   | Ctrl-S       | Cmd-S           |
| Suchen                      | Ctrl-F       | Cmd-F           |
| Weitersuchen                | Ctrl-G       | Cmd-G           |
| Rückwärts suchen            | Shift-Ctrl-G | Shift-Cmd-G     |
| Ersetzen                    | Shift-Ctrl-F | Cmd-Alt-F       |
| Alle ersetzen               | Shift-Ctrl-R | Shift-Cmd-Alt-F |
| Vollbild umschalten         | Esc          | Esc             |
| Faltung umschalten          | Ctrl-Q       | Cmd-Q           |
| Zeilenumbruch umschalten    | Alt-W        | Alt-W           |
| Vorschau umschalten         | Crtl-P       | Ctrl-P          |
| Bilder durchsuchen          | Ctrl-I       | Cmd-I           |
| Downloads durchsuchen       | Ctrl-L       | Cmd-L           |
| Mediadateien durchsuchen    | Ctrl-M       | Cmd-M           |
| Benutzerdateien durchsuchen | Ctrl-U       | Cmd-U           |

## Einschränkungen

Die Filebrowser-Funktionalität wird nur vom Standard-Filebrowser von
CMSimple\_XH unterstützt.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/codeeditor_xh/issues)
oder im [CMSimple\_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Codeeditor\_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Codeeditor\_XH erfolgt in der Hoffnung, daß es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Codeeditor\_XH erhalten haben. Falls nicht, siehe
<https://www.gnu.org/licenses/>.

Copyright 2011-2021 Christoph M. Becker

Tschechische Übersetzung © 2012 Josef Němec  
Slovakische Übersetzung © 2012 Dr. Martin Sereday  
Russische Übersetzung © 2012 Lybomyr Kydray

## Danksagung

Codeeditor\_XH verwendet [CodeMirror](https://codemirror.net/).
Vielen Dank an Marijn Haverbeke für das Entwickeln dieser großartigen Software
und für die Veröffentlichung unter MIT-Lizenz.

Das Plugin-Icon stammt aus dem [Oxygen Icon-Set](http://www.oxygen-icons.org/).
Vielen Dank für die Veröffentlichung dieser Icons unter GPL.

Vielen Dank an die Community im
[CMSimple\_XH Forum](https://www.cmsimpleforum.com/)
für Hinweise, Anregungen und das Testen.

Und zu guter letzt vielen Dank an
[Peter Harteg](https://www.harteg.dk/), den „Vater“ von CMSimple,
und allen Entwicklern von [CMSimple\_XH](http://www.cmsimple-xh.org/de/)
ohne die es dieses phantastische CMS nicht gäbe.
