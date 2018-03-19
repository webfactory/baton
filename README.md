# Baton

Das Magnum Opus von @xkons für sein Abschlussprojekt im Rahmen der Ausbildung

## Kurzbeschreibung

Für die webfactory GmbH soll eine Webanwendung entwickelt werden, die für PHP-Projekte, welche Composer als Paketmanager nutzen, automatisch einen Abhängigkeitsbaum der installierten Pakete visualisieren kann, aber auch umgekehrt Auskunft geben kann, welche Projekte ein spezielles Composer-Paket einbinden.

Mit Hilfe dieser Anwendung ist die Übersicht über die Paketabhängigkeiten in den über 550 Projekt-Repositories der webfactory GmbH gewährleistet und Entwickler können schnell sehen, welche Major-Versionen eines Pakets noch unterstützt werden müssen.

## Anforderungen

- Composer-Abhängigkeiten werden automatisch über die GitHub- und Kiln-API aus der composer.lock Datei in Projekt-Repositories geholt und in einer MySQL Datenbank gespeichert
- Trennung in Modell-, Präsentations- und Steuerungskomponten mittels dem MVC Pattern (Model View Controller)
- Objektrelationale Abbildung der Datenbank mit dem Doctrine Framework - Testen der Funktionalität der Anwendung mit Unit Tests
- Rückwärtssuche, um Projekt-Repositories zu finden, die ein spezielles Composer-Paket einbinden
- Abhängigkeitsbaum für einzelne Projekt-Repositories - Code wird sorgfältig dokumentiert
