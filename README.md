## UKMrfid

Identifiserings-verktøy for landsbyen under UKM-festivalen. Deltakerne får en RFID-brikke på armbåndet, og scanner det ved inn- eller utgang.

Dette gjør det blant annet mye enklere ved uforutsette hendelser som branntilløp å ha kontroll på hvem som er tilstede. Det viktigste med dette systemet er ikke å kunne nekte folk adgang, men å ha kontroll på hvem som er til stede.

####
Systemet er avhengig av følgende database-trigger
```sql
BEGIN
  IF ( (SELECT id FROM person WHERE person.rfid = NEW.rfid) IS NULL ) THEN
    RAISE EXCEPTION 'No user with that RFID registered';
  END IF;
  IF (NEW.direction = 'in' AND (SELECT id FROM person_in_area WHERE person_in_area.person_id=(SELECT id FROM person WHERE person.rfid = NEW.rfid) ) IS NULL ) THEN
      INSERT INTO person_in_area(person_id,area_id) VALUES ( (SELECT id FROM person WHERE person.rfid = NEW.rfid) ,NEW.area);
  ELSIF (NEW.direction = 'out') THEN
    DELETE FROM person_in_area WHERE person_id = (SELECT id FROM person WHERE person.rfid = NEW.rfid);
  END IF;
  RETURN NEW;
END;
```
