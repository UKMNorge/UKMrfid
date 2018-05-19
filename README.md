## UKMrfid

Identifiserings-verktøy for landsbyen under UKM-festivalen. Deltakerne får en RFID-brikke på armbåndet, og scanner det ved inn- eller utgang.

Dette gjør det blant annet mye enklere ved uforutsette hendelser som branntilløp å ha kontroll på hvem som er tilstede. Det viktigste med dette systemet er ikke å kunne nekte folk adgang, men å ha kontroll på hvem som er til stede.

## Setup-guide
- Sjekk at SSL-sertifikat er generert med wildcard eller rfid.ukm.dev.
- Sjekk at UKMconfig.inc.php har fått `SLACK_UKMRFID_WEBHOOK_URL` og `SLACK_UKMRFID_CHANNEL` definert.
- Sjekk at /tmp er skrivbart for alle ( `chmod -R 777 /tmp` )

## Funksjonsbeskrivelse

Enkel API-backend som tar i mot en JSON-encoded datapakke til inn-/ut-adresse. 

- Timestamp
- Unik hash av transaction
- Bruker-ID (p_id, kanskje?)

Data-pakken lagres lokalt på id-stasjonen fram til HTTP 200 OK {status: "success"} er mottatt. Deretter slettes den fra køen. Håndteres i JS med ajax på inn-/ut-stasjonene. Kan også slette pakke ved HTTP 200 OK {status: "duplicate package"}, muligens?

Det settes opp 4-6 id-stasjoner ved inn- og utganger, og systemet må kunne håndtere 600 personer som sjekker inn eller ut i løpet av få minutter. 

Alle datapakker som committes til databasen vil sjekkes for unik ID først. JSON-statuser rapporteres tilbake til nettsiden, inkludert brukerens navn? 

Vi bruker monolog for logging, slik at alle requester der vi ser en feil kan logges i sin helhet og feilsøkes.

## Rapporter

Statusrapport må kunne hentes ut direkte i nettleser på f.eks et nettbrett - sortert på fylke / gjeng. Det vil gjøre det lettere å få oversikt dersom det skjer noe og det må gjøres en opptelling. 

## Edgecases

Èn av de viktigste tingene å definere i dette systemet er hvordan man skal håndtere edge cases som at en bruker allerede er registrert som "inne", og deretter prøver å gå inn igjen. Skal det varsles om dette eller er det OK? Samme på vei ut - gjør dette egentlig noe?

Ved en evakuering etter f.eks et branntilløp, skal folk registrere seg på vei ut, eller er det åpne sluser da? Rapporter bør kunne hentes ut som viser status på et klokkeslett, slik at man kan se status før / etter evakuering er igangsatt.

Dersom siden refreshes før opplastingskøen er tom bør brukeren varsles - evt lagre de i local storage / JS-cookie?

## Hardware

For at registreringen skal gå smooth vil deltakerne ha en RFID-brikke integrert i UKM-båndet de har fått rundt armen. Denne inneholder en unik kode per deltaker, som er forhåndsregistrert på en person. Et viktig kontrollpunkt er altså at de deles ut til rett person ved ankomst.

Vi bruker lageret av Raspberry Pi's som registreringsenheter, satt opp med en USB RFID-scanner og en skjerm som viser nettsiden. Pi'ene kjører en nettleser med et skript som fyller inn bruker-koden i et input-felt (gjerne usynlig for brukeren) og trykker enter ved skanning av chip.

## Poeng å tenke på

- Kanskje statusrapporten bør være en del av reiseledernes UKMfestival-side? Fint å sjekke før felles avreise/ankomst til-/fra landsbyen, etc.
- Check-in bør gå veldig fort. Blink en grønn greie ved OK, kanskje en gul "VENT" mens den sjekker. Ikke stopp registrering av nye etter at OK kommer, kanskje?
- Hvilken bruker-ID skal vi pushe fram og tilbake?
- Alle inn- og utsjekkinger bør tabellføres. Men hvordan bør "inne" og "ute"-statusen sjekkes? Generer on-the-fly fra tabellene, eller ha en egen "inne"-tabell/"status"-tabell?
- Hvordan kan vi verifisere at stasjonen er en av "våre", og ikke en tilfeldig stasjon som settes opp ett sted? Inpute en kode på stasjonen når den først settes opp, kanskje?