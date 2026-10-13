---
title: Galette HelloAsso
description: Dodatak za plaćanje članarine i donacija putem HelloAssoa
---

> **Napomena** — HelloAsso prvenstveno služi **francuskim** udrugama i
> neprofitnim organizacijama.

Dodatak omogućuje:

* obrazac za plaćanje,
* povijest plaćanja,
* automatsko dodavanje uplata u Galette nakon potvrde plaćanja.

![Obrazac za plaćanje vidljiv *neprijavljenim*
korisnicima](images/form-public.jpg)

> **Napomena** — Instalacija Galettea mora biti javno dostupna i imati valjan
> SSL certifikat.

## Instalacija

Najprije preuzmite dodatak:

* [Preuzmite najnoviji dodatak
  HelloAsso!](https://github.com/galette-plugins/plugin-helloasso/releases/latest)
* [Preuzmite noćnu razvojnu verziju dodatka
  HelloAsso!](https://github.com/galette-plugins/plugin-helloasso/releases/tag/nightly)

Raspakirajte preuzetu arhivu u Galetteovu mapu `plugins`. Primjerice, na Linuxu
(zamijenite *{url}* i *{version}* odgovarajućim vrijednostima):

```
$ cd /var/www/html/galette/plugins
$ wget {url}
$ tar xjvf galette-plugin-helloasso-{version}.tar.bz2
```

## Inicijalizacija baze podataka

Dodatak zahtijeva nekoliko tablica u bazi. Pogledajte [sučelje za upravljanje
dodacima
Galettea](https://doc.galette.eu/en/master/plugins/index.html#plugins-managment).

Gotovo; dodatak *HelloAsso* je instaliran. :)

## Uporaba dodatka

Nakon instalacije prijavljenom se korisniku u izborniku pojavljuje odjeljak
*Helloasso*. Administratori i uprava mogu podesiti dodatak i pregledati povijest
plaćanja.

![Izbornik dodatka](images/menu.jpg)

Obrazac za plaćanje dostupan je na Galetteovim javnim stranicama.

Samo *prijavljeni* korisnici mogu izvršiti uplate *koje produljuju članstvo*,
odnosno platiti članarinu.

![Obrazac za plaćanje vidljiv prijavljenim korisnicima](images/form.jpg)

Posjetitelji (*neprijavljeni* korisnici) mogu izvršiti samo uplate *koje ne
produljuju članstvo*, odnosno donacije. U tom se slučaju uplata ne dodaje
automatski u Galette, nego se plaćanje prikazuje samo u povijesti dodatka s
vrijednošću „Nema” u stupcu „Član”.

![Povijest plaćanja](images/history.jpg)

## Postavke

![Postavke](images/settings.jpg)

### Postavke

* **Povratni URL za HelloAsso**: unesite ovu adresu u polje *„Mon URL de
  callback”* u odjeljku „Intégrations et API” HelloAsso računa udruge.
* **Uključi testni način**: najprije izradite testni račun na
  [helloasso-sandbox.com](https://www.helloasso-sandbox.com). Zatim možete
  provjeriti rad dodatka bez stvarnih mrežnih plaćanja.

  > **Upozorenje** — U ovom načinu koristite isključivo testne kartice, nikada
  > stvarne brojeve kartica. Popis testnih kartica nalazi se u [dokumentaciji
  > Stripea](https://docs.stripe.com/testing#cards) ili [dokumentaciji
  > Worldlinea](https://docs.sips.worldline-solutions.com/fr/cartes-de-test.html).

* **Vaš organizationSlug**: nalazi se u adresnoj traci preglednika kad ste
  prijavljeni u HelloAsso račun udruge. To je prvi dio putanje računa.
  Primjerice, u adresi `https://admin.helloasso.com/{organizationSlug}/accueil`
  to je *{organizationSlug}*.
* **Vaš clientId**: nalazi se u polju *„Mon clientID”* u odjeljku „Intégrations
  et API” HelloAsso računa udruge.
* **Vaš clientSecret**: nalazi se u polju *„Mon clientSecret”* u odjeljku
  „Intégrations et API” HelloAsso računa udruge.

![Odjeljak „Intégrations et API” HelloAsso računa](images/helloasso-account.jpg)

* **Prihvati SEPA transfere**: zadano obrazac HelloAssoa nudi samo plaćanje
  karticom. Uključite ovu opciju ako želite ponuditi i SEPA transfere.
* **Vrste uplata**: u tablici možete isključiti [vrste uplata podešene u
  Galetteu](https://doc.galette.eu/en/master/usermanual/contributions.html#contributions-types)
  koje ne želite ponuditi kao svrhu plaćanja u obrascu.

  *Vrste uplata s iznosom nula ili bez zadanog iznosa neće se ponuditi kao svrha
  plaćanja, čak i ako nisu označene kao neaktivne u tablici.*

  > **Napomena** — Opis ispod svake svrhe plaćanja možete zadati u [postavkama
  > vrsta
  > uplata](https://doc.galette.eu/en/master/usermanual/contributions.html#contributions-types)
  > u Galetteu.

> **Napomena** — U Galetteovim postavkama možete odrediti tko pristupa obrascu
> za plaćanje. Odaberite željenu opciju u [postavkama vidljivosti javnih
> stranica](https://doc.galette.eu/en/master/usermanual/preferences.html#parameters).

### Status veze

Ova stranica prikazuje je li dodatak ispravno podešen i povezan s HelloAssoom.

![Status veze s HelloAssoom](images/status.jpg)
