# Komfortkasse-Modul für PrestaShop

Das Komfortkasse-Modul für PrestaShop automatisiert den Zahlungsabgleich für **Vorkasse, Rechnung und Nachnahme**. Komfortkasse ordnet eingehende Überweisungen automatisch den offenen Bestellungen Ihres Shops zu und setzt den Bestellstatus, sobald das Geld da ist. Das Modul verbindet den PrestaShop-Shop mit Komfortkasse; den Abgleich mit dem Bankkonto übernimmt Komfortkasse.

[Komfortkasse für PrestaShop](https://komfortkasse.eu/shopsysteme/prestashop/) · [Kostenlos registrieren](https://ssl.komfortkasse.eu/public/register.jsf) · [Installationsanleitung](https://komfortkasse.freshdesk.com/support/solutions/articles/14000138236) · [Hilfe & Dokumentation](https://komfortkasse.freshdesk.com/support/solutions/14000075022)

## Eckdaten

| Merkmal | Angabe |
|---|---|
| Shopsystem | [PrestaShop](https://prestashop.com/) |
| Unterstützte Versionen | PrestaShop ab 1.7 |
| Zahlungsarten | Vorkasse (Vorauskasse, Banküberweisung), Rechnung (Kauf auf Rechnung), Nachnahme |
| Aufgabe des Moduls | Überträgt offene Bestellungen an Komfortkasse und setzt den Bestellstatus bei Zahlungseingang oder Storno |
| Technischer Name | `ltckomfortkasse` (Ordner `modules/ltckomfortkasse`) |
| Für wen | Betreiber von PrestaShop-Shops und Agenturen, die solche Shops betreuen |
| Kosten | Das Modul ist als Download über die [Landingpage](https://komfortkasse.eu/shopsysteme/prestashop/) und als Quellcode auf GitHub kostenlos. Im [PrestaShop Addons Marketplace](https://addons.prestashop.com/de/andere-zahlungsmethoden/21967-zahlung-per-uberweisung-automatisieren-mit-komfortkasse.html) ist es kostenpflichtig und enthält ein Startguthaben für Komfortkasse. Für Komfortkasse gelten die [Preise](https://komfortkasse.eu/preise/) auf komfortkasse.eu. |
| Anbieter | LTC Information Services GmbH, [komfortkasse.eu](https://komfortkasse.eu/) |

## Was ist Komfortkasse?

Komfortkasse ist ein Online-Dienst der LTC Information Services GmbH, der Zahlungen per Banküberweisung für Onlineshops automatisiert. Komfortkasse ruft die Zahlungseingänge vom Bankkonto ab, ordnet sie den offenen Bestellungen zu und meldet den Zahlungsstatus an den Shop zurück. Der Dienst richtet sich an Online-Händler in Deutschland, Österreich und der Schweiz.

Komfortkasse unterstützt die Zahlungsarten, bei denen das Geld per Überweisung kommt:

- [Vorkasse](https://komfortkasse.eu/loesungen/vorkasse/): Der Käufer überweist vor dem Versand. Komfortkasse prüft den Zahlungseingang und setzt den Bestellstatus im Shop automatisch.
- [Rechnung](https://komfortkasse.eu/loesungen/rechnung/): Die Ware wird sofort geliefert, der Käufer überweist danach. Komfortkasse überwacht die Zahlung und sendet bei Bedarf Zahlungserinnerungen und Mahnungen.
- [Nachnahme](https://komfortkasse.eu/loesungen/nachnahme/): Der Versanddienstleister kassiert bei der Zustellung und überweist den Betrag. Komfortkasse überwacht diese Überweisung.

### So funktioniert Komfortkasse mit PrestaShop

1. Das Komfortkasse-Modul überträgt die offenen Bestellungen aus PrestaShop an Ihren Komfortkasse-Account.
2. Komfortkasse ist mit Ihrem Bankkonto verbunden ([unterstützte Banken](https://komfortkasse.eu/banken/)) und ordnet eingehende Zahlungen den Bestellungen zu.
3. Das Modul setzt die Bestellung in PrestaShop auf den gewählten Status, z. B. „Zahlung eingegangen“, oder auf „storniert“, wenn keine Zahlung kommt.

Komfortkasse versendet außerdem Zahlungserinnerungen an Käufer, behandelt Teil- und Überzahlungen und wickelt Rückerstattungen ab. Alle Funktionen sind auf [komfortkasse.eu](https://komfortkasse.eu/) beschrieben.

## Funktionen des Komfortkasse-Moduls für PrestaShop

- **Bestellungen übertragen:** Das Modul exportiert offene Bestellungen nach Zahlungsart (Vorkasse, Rechnung, Nachnahme). Welche Bestellungen dazugehören, legen Sie je Zahlungsart über die Einstellungen „relevante Zahlungsmodule“ und „Status offen“ fest. Übertragen werden unter anderem Bestellnummer, Betrag, Währung, E-Mail-Adresse, Rechnungs- und Lieferadresse sowie die Rechnungsnummer.
- **Bestellnummern wählen:** Über die Einstellung „Darstellung von Bestellnummern“ zeigt Komfortkasse entweder die Bestellreferenz von PrestaShop (Buchstaben, z. B. ABCDEFGH) oder die numerische Bestell-ID (z. B. 00001234).
- **Bestellstatus setzen:** Bei Zahlungseingang und bei Storno bzw. ausbleibender Zahlung ändert das Modul den Bestellstatus in PrestaShop automatisch. Die Status sind je Zahlungsart frei wählbar, z. B. „Vorkasse: Status Zahlung erhalten“ oder „Nachnahme: Status Zahlung ungeklärt“. Die Änderung läuft über die Bestellhistorie von PrestaShop; ist für einen Status eine E-Mail an den Kunden eingestellt, versendet PrestaShop sie.
- **Echtzeit-Übertragung:** Sobald eine Bestellung in einen offenen Status wechselt, zum Beispiel direkt nach dem Bestellabschluss, meldet das Modul sie über den PrestaShop-Hook `actionOrderStatusPostUpdate` sofort an Komfortkasse. So erhält der Käufer die Zahlungsinformationen ohne Wartezeit. Die Meldung geht ausschließlich per HTTPS mit Zertifikatsprüfung; schlägt sie fehl, übernimmt Komfortkasse die Bestellung beim regelmäßigen Abgleich. Eine zusätzliche Einrichtung ist nicht nötig.
- **Rechnungs-PDF bereitstellen:** Komfortkasse kann die Rechnung einer Bestellung als PDF aus PrestaShop abrufen.
- **Vorhandene Zahlungsmodule nutzen:** Das Modul arbeitet mit den aktiven Zahlungsmodulen des Shops, z. B. „Banküberweisung“ für Vorkasse. Es fügt im Checkout keine eigene Zahlungsart hinzu.
- **Verschlüsselte Übertragung:** Das Modul überträgt die Daten verschlüsselt, bevorzugt mit OpenSSL. Verschlüsselung, Zugriffscode und Schlüssel setzt Komfortkasse beim Verbinden des Shops automatisch.
- **Multistore:** Zugriffscode, Schlüssel und Einstellungen lassen sich je Shop einer PrestaShop-Multistore-Installation hinterlegen. Das Modul liest die Einstellungen passend zum Shop der jeweiligen Bestellung.

## Voraussetzungen

- PrestaShop ab Version 1.7
- Ein Komfortkasse-Account ([kostenlos registrieren](https://ssl.komfortkasse.eu/public/register.jsf))
- Ein Bankkonto, das Sie mit Komfortkasse verbinden ([unterstützte Banken](https://komfortkasse.eu/banken/)), oder ein integriertes Bankkonto von Komfortkasse
- Ein aktives Zahlungsmodul je genutzter Zahlungsart, für Vorkasse in der Regel „Banküberweisung“. Für Kauf auf Rechnung bringt PrestaShop standardmäßig kein Zahlungsmodul mit; dafür ist ein zusätzliches Modul nötig.
- Empfohlen: PHP mit den Erweiterungen OpenSSL und cURL, für die Verschlüsselung und für die Echtzeit-Übertragung per HTTPS

## Installation in PrestaShop

Die Installation besteht aus vier Schritten: Modul installieren, Zahlungsarten und Bestellstatus einstellen, den Shop mit dem Komfortkasse-Account verbinden und den Versand der Bankverbindung durch PrestaShop anpassen.

1. **Modul installieren:** Das Modul im [PrestaShop Addons Marketplace](https://addons.prestashop.com/de/andere-zahlungsmethoden/21967-zahlung-per-uberweisung-automatisieren-mit-komfortkasse.html) beziehen oder kostenlos als ZIP-Datei über die [Landingpage](https://komfortkasse.eu/shopsysteme/prestashop/) herunterladen und im PrestaShop Back Office unter „Module“ hochladen. Dort nach „Komfortkasse“ suchen und „Installieren“ wählen. Beim direkt von Komfortkasse heruntergeladenen Modul die Warnung bestätigen, dass PrestaShop das Modul nicht verifizieren kann. Nach der Installation öffnet PrestaShop die Einstellungen des Moduls.
2. **Einstellungen festlegen:** „Export Bestellungen“ und „Update von Bestellungen“ müssen aktiv sein. Je Zahlungsart wählen Sie die relevanten Zahlungsmodule, die Status für offene Bestellungen und die Status, die das Modul bei Zahlungseingang bzw. ausbleibender Zahlung setzt. Für Rechnung und Nachnahme legen Sie in PrestaShop vorher zwei zusätzliche Bestellstatus an, einen für versendete und bezahlte und einen für versendete, aber nicht bezahlte Bestellungen; eine Standardinstallation von PrestaShop hat solche Status nicht.
3. **Shop verbinden:** Im Komfortkasse-Händlerbereich PrestaShop als Shopsystem wählen, die URL des Shops eintragen und speichern. Danach im Aktionsmenü ≡ die Aktion „Verbindung herstellen“ wählen. Verschlüsselung, Zugriffscode, API-Schlüssel sowie öffentlichen und privaten Schlüssel setzt Komfortkasse dabei automatisch; diese Felder in den Moduleinstellungen nicht ändern.
4. **Versand der Bankverbindung anpassen:** Im Normalfall erhalten Ihre Kunden die Zahlungsinformationen direkt von Komfortkasse. Ersetzen Sie deshalb in den Einstellungen des Zahlungsmoduls „Banküberweisung“ (Module → Zahlung) die Bankverbindung durch einen Hinweis, z. B. „Sie erhalten die Bankverbindung in einer weiteren E-Mail“. Sollen die Zahlungsinformationen weiterhin aus dem Shop kommen, entfällt dieser Schritt.

Ausführliche Anleitung mit Screenshots: [PrestaShop-Anleitung](https://komfortkasse.freshdesk.com/support/solutions/articles/14000138236). Die allgemeine Einrichtung beschreibt [Erste Schritte: Einrichtung und Verbindung zum Shop](https://komfortkasse.freshdesk.com/support/solutions/articles/14000114082).

## Update

Ein Update der kostenlosen ZIP-Version installieren Sie auf demselben Weg wie das Modul selbst: Die neue ZIP-Datei von der [Landingpage](https://komfortkasse.eu/shopsysteme/prestashop/) im Back Office unter „Module“ hochladen. Die Einstellungen bleiben erhalten; ändert sich ihr Format, passen die Upgrade-Skripte im Ordner `upgrade/` sie an.

## Häufige Fragen

### Was kostet das Komfortkasse-Modul für PrestaShop?

Das Modul ist kostenlos, wenn Sie es über die [Landingpage](https://komfortkasse.eu/shopsysteme/prestashop/) oder aus diesem Repository beziehen; im PrestaShop Addons Marketplace ist es kostenpflichtig und enthält ein Startguthaben für Komfortkasse. Für Komfortkasse selbst fällt eine prozentuale Gebühr pro Zahlungseingang an, ohne Grundgebühr und ohne Einrichtungsgebühr. Die aktuellen Sätze stehen auf der [Preisseite](https://komfortkasse.eu/preise/). Neue Accounts können Komfortkasse kostenlos testen.

### Ist das Komfortkasse-Modul ein Zahlungsmodul?

Nein. Das Komfortkasse-Modul fügt im Checkout keine Zahlungsart hinzu. Es arbeitet mit den Zahlungsmodulen, die der PrestaShop-Shop bereits nutzt, zum Beispiel „Banküberweisung“ für Vorkasse. Welche Zahlungsmodule das Modul berücksichtigt, legen Sie in den Einstellungen je Zahlungsart fest. In der Modulliste von PrestaShop erscheint Komfortkasse zwar in der Kategorie für Zahlungsmodule (`payments_gateways`), bietet dem Käufer aber keine eigene Zahlungsart an.

### Was passiert, wenn die Bestellnummer fehlt oder der Betrag abweicht?

Komfortkasse ordnet Zahlungen fehlertolerant zu. Die Zuordnung funktioniert auch, wenn die Bestellnummer im Verwendungszweck fehlt, der Name des Zahlers nicht zum Besteller passt oder der Betrag abweicht. Auch Teil- und Überzahlungen behandelt Komfortkasse.

### Was passiert, wenn ein Käufer nicht zahlt?

Komfortkasse sendet automatisch Zahlungserinnerungen an den Käufer. Bleibt die Zahlung aus, setzt das Modul die Bestellung in PrestaShop auf den Status, den Sie dafür gewählt haben. Die Einstellungen unterscheiden nach Zahlungsart: „Vorkasse: Status storniert“, „Rechnung: Status keine Zahlung/Inkasso“ und „Nachnahme: Status Zahlung ungeklärt“.

### Welche Banken unterstützt Komfortkasse?

Komfortkasse arbeitet mit Ihrem eigenen Bankkonto oder mit einem integrierten Bankkonto von Komfortkasse. Welche Banken sich anbinden lassen, zeigt die Liste der [unterstützten Banken](https://komfortkasse.eu/banken/).

### Wo finde ich das Komfortkasse-Modul im PrestaShop Addons Marketplace?

Im PrestaShop Addons Marketplace heißt das Modul „Zahlung per Überweisung automatisieren mit Komfortkasse“ ([zum Eintrag](https://addons.prestashop.com/de/andere-zahlungsmethoden/21967-zahlung-per-uberweisung-automatisieren-mit-komfortkasse.html)). Dort ist es kostenpflichtig und enthält ein Startguthaben für Komfortkasse. Dasselbe Modul mit dem technischen Namen `ltckomfortkasse` gibt es kostenlos als Download auf der [Landingpage](https://komfortkasse.eu/shopsysteme/prestashop/) und als Quellcode in diesem Repository.

### Müssen für das Modul Dateien von PrestaShop geändert werden?

Nein. Das Komfortkasse-Modul liegt vollständig im Ordner `modules/ltckomfortkasse`, verwendet keine Overrides und bindet sich über den PrestaShop-Hook `actionOrderStatusPostUpdate` ein. Komfortkasse ruft die Bestellungen über `modules/ltckomfortkasse/lib/callback.php` ab; diese Datei nimmt nur festgelegte Aktionen mit gültigem Zugriffscode an. Die übrigen PHP-Dateien des Moduls sind per `.htaccess` gegen direkten Aufruf gesperrt (Apache).

### Gibt es Komfortkasse auch für andere Shopsysteme?

Ja. Komfortkasse gibt es unter anderem für Shopify, Shopware, WooCommerce, Gambio und xt:Commerce. Alle Systeme stehen in der [Übersicht der Shopsysteme](https://komfortkasse.eu/shopsysteme/), weitere Plugin-Repositories unter [github.com/ltckomfortkasse](https://github.com/ltckomfortkasse).

## Support

Fragen zur Einrichtung beantworten die [Hilfe-Seiten](https://komfortkasse.freshdesk.com/support/solutions/14000075022) und der [Komfortkasse-Support](https://komfortkasse.eu/kontakt/).

## English

**Komfortkasse module for PrestaShop.** Komfortkasse automatically matches incoming bank transfers to open orders in your shop and updates the order status as soon as the payment has arrived. It supports prepayment by bank transfer (Vorkasse), invoice and cash on delivery. Komfortkasse is a service of LTC Information Services GmbH for online merchants in Germany, Austria and Switzerland.

The module supports PrestaShop 1.7 or later. It does not add a payment method to the checkout; it works with the shop's existing payment modules (e.g. bank wire). The module is free of charge when downloaded from the landing page or this repository; on the PrestaShop Addons Marketplace it is a paid listing that includes starting credit for Komfortkasse. Komfortkasse itself is a paid service ([pricing](https://komfortkasse.eu/preise/)).

Installation: install the module in the PrestaShop back office under "Modules", enable "Export orders" and "Update order status", select the payment modules and order statuses for prepayment, invoice and cash on delivery, then connect the shop in your Komfortkasse account. More information: [Komfortkasse for PrestaShop](https://komfortkasse.eu/shopsysteme/prestashop/) · [Installation guide (English)](https://komfortkasse.freshdesk.com/support/solutions/articles/14000152617)
