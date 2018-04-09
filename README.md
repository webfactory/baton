# Baton

Das Magnum Opus von @xkons für sein Abschlussprojekt im Rahmen der Ausbildung

## Roadmap

Aktuell untersützen wir nur GitHub und Kiln für das Importieren.

Das liegt daran, dass die Authentifizierung für das Verarbeiten von privaten Repositories für jede Plattform
extra konfiguriert werden muss und wir bei firmenintern nur GitHub und Kiln verwenden.

Die Verwendung von ssh URLs und dem plattform-agnositschen Composer\GitDriver zum Holen der composer.lock Datei
kann hier Abhilfe schaffen.

## Kurzbeschreibung

Für die webfactory GmbH soll eine Webanwendung entwickelt werden, die für PHP-Projekte, welche Composer als Paketmanager nutzen, automatisch einen Abhängigkeitsbaum der installierten Pakete visualisieren kann, aber auch umgekehrt Auskunft geben kann, welche Projekte ein spezielles Composer-Paket einbinden.

Mit Hilfe dieser Anwendung ist die Übersicht über die Paketabhängigkeiten in den über 550 Projekt-Repositories der webfactory GmbH gewährleistet und Entwickler können schnell sehen, welche Major-Versionen eines Pakets noch unterstützt werden müssen.

## Anforderungen

- Composer-Abhängigkeiten werden automatisch über die GitHub- und Kiln-API aus der composer.lock Datei in Projekt-Repositories geholt und in einer MySQL Datenbank gespeichert
- Trennung in Modell-, Präsentations- und Steuerungskomponten mittels dem MVC Pattern (Model View Controller)
- Objektrelationale Abbildung der Datenbank mit dem Doctrine Framework - Testen der Funktionalität der Anwendung mit Unit Tests
- Rückwärtssuche, um Projekt-Repositories zu finden, die ein spezielles Composer-Paket einbinden
- Abhängigkeitsbaum für einzelne Projekt-Repositories - Code wird sorgfältig dokumentiert

## Projektdokumentation

Die Anforderungen der IHK Bonn für die Dokumentation sind in diesem PDF Ab Seite 17 beschrieben:

https://www.ihk-bonn.de/fileadmin/dokumente/Downloads/Ausbildung/IT-Berufe/IT-Handreichung_Stand_Januar_2018.pdf

Die Projektdokumentation wird aktuell hier gepflegt: 

https://docs.google.com/document/d/1dvWUFY6_NgA87uyRw6DVtQiWx1LAdVV1ZOjUTvIm3Ik/edit?usp=sharing
