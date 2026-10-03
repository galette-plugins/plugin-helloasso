---
title: Galette HelloAsso
description: Vtičnik za upravljanje plačil članarin in donacij prek storitve HelloAsso
---

Ta vtičnik omogoča:

* spletni obrazec za plačilo,
* zgodovina plačil,
* samodejno ustvarjanje prispevkov po potrditvi plačil.

![Obrazec za plačilo, kot ga vidi uporabnik, ki *ni prijavljen* v svoj
račun](images/form-public.jpg)

> **Note** — Ta vtičnik zahteva, da je vaša namestitev sistema Galette javno
> dostopna in deluje z veljavnim potrdilom SSL.

## Namestitev

Najprej prenesite vtičnik:

[![Prenesite najnovejšo različico vtičnika
HelloAsso!](https://img.shields.io/badge/1.0.0-HelloAsso-ffb619?style=for-the-badge&logo=php&logoColor=white&label=1.0.0&color=ffb619)](https://github.com/galette-plugins/plugin-helloasso/releases/tag/1.0.0)
[![Pridobite nočno različico vtičnika
HelloAsso!](https://img.shields.io/badge/Nightly-HelloAsso-ffb619?style=for-the-badge&logo=php&logoColor=white&label=Nightly&color=ffb619)](https://galette.eu/download/plugins/galette-plugin-helloasso-dev.tar.bz2)

Razširite preneseni arhiv v mapo `plugins` programa Galette. Na primer v sistemu
Linux (pri čemer *{url}* in *{version}* nadomestite z ustreznimi vrednostmi):

```
$ cd /var/www/html/galette/plugins
$ wget {url}
$ tar xjvf galette-plugin-helloasso-{version}.tar.bz2
```

## Inicializacija podatkovne baze

Ta vtičnik potrebuje več tabel v podatkovni bazi. Glejte [vmesnik za upravljanje
vtičnikov v
Galette](https://doc.galette.eu/en/master/plugins/#plugins-management-interface).

In to je to, vtičnik *HelloAsso* je nameščen. :)

## Uporaba vtičnika

Ko je vtičnik nameščen, se v meniju programa Galette – ob prijavi uporabnika –
prikaže skupina *Helloasso*. Ta skrbnikom in osebju omogoča nastavljanje
parametrov vtičnika ter pregledovanje zgodovine plačil.

![Meni vtičnika](images/menu.jpg)

Obrazec za plačilo je na voljo na javnih straneh Galette.

Samo uporabniki, ki so *prijavljeni* v svoj račun, lahko plačajo prispevke *s
podaljšanjem članstva* (članarino).

![Obrazec za plačilo, kot ga vidi uporabnik, prijavljen v svoj
račun](images/form.jpg)

Obiskovalci (uporabniki, ki *niso prijavljeni* v svoj račun) lahko plačajo
prispevke le *brez podaljšanja članstva* (kot donacije). V tem primeru se v
sistemu Galette ne ustvari zapis o prispevku: plačilo je vidno le v zgodovini
plačil vtičnika.

![Zgodovina plačil](images/history.jpg)

## Nastavitve

![Nastavitve](images/settings.jpg)

### Nastavitve

* **Povratni URL za nastavitev v HelloAsso**: ta URL vnesite v polje *Mon URL de
  callback* v razdelku »Intégrations et API« v računu vašega društva na
  platformi HelloAsso.
* **Omogočite testni način**: za uporabo testnega načina najprej ustvarite
  testni račun na [helloasso-sandbox.com](https://www.helloasso-sandbox.com).
  Tako lahko preverite delovanje vtičnika, ne da bi izvajali dejanska spletna
  plačila. **OPOZORILO**: *v tem načinu nikoli ne uporabljajte pravih številk
  kreditnih kartic, temveč le testne kartice (glejte seznam testnih kartic
  ponudnikov
  [Stripe](https://docs.stripe.com/testing?numbers-or-method-or-token=card-numbers#visa)
  ali
  [Worldline](https://docs.sips.worldline-solutions.com/fr/cartes-de-test.html)).*
* **Vaš organizationSlug**: najdete ga v naslovni vrstici brskalnika, ko ste
  prijavljeni v račun HelloAsso svojega društva. Gre za prvi del poti v
  URL-naslovu vašega računa. Na primer, v URL-naslovu
  `https://admin.helloasso.com/{organizationSlug}/accueil` je to
  *{organizationSlug}*.
* **Vaš clientId**: našli ga boste v polju *Mon clientID* v razdelku
  »Intégrations et API« v računu vašega društva na platformi HelloAsso.
* **Vaš clientSecret**: našli ga boste v polju *Mon clientSecret* v razdelku
  *Intégrations et API* v računu vašega društva na platformi HelloAsso.

![Razdelek »Intégrations et API« računa HelloAsso](images/helloasso-account.jpg)

* **Vrste prispevkov**: v tej tabeli lahko onemogočite [vrste prispevkov,
  konfigurirane v
  Galette](https://doc.galette.eu/en/master/usermanual/contributions.html#contributions-types),
  ki jih ne želite ponuditi kot namen plačila na spletnem obrazcu za plačilo.

  *Vrste prispevkov z zneskom nič ali brez določenega zneska se na obrazcu
  nikoli ne ponudijo kot nameni plačila, tudi če v tabeli niso označene kot
  neaktivne.*

### Stanje povezave

Ta zaslon prikazuje, ali je vtičnik pravilno nastavljen in povezan s storitvijo
HelloAsso.

![Stanje povezave s HelloAsso](images/status.jpg)
