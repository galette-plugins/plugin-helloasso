---
title: Galette HelloAsso
description: Vtičnik za upravljanje plačil članarin in donacij prek storitve HelloAsso
---

> **Note** — HelloAsso služi predvsem **francoskim** združenjem in neprofitnim
> organizacijam.

Ta vtičnik omogoča:

* obrazec za plačilo,
* zgodovina plačil,
* samodejno ustvarjanje prispevkov v Galette, ko so plačila potrjena.

![Plačilni obrazec, ki ga vidijo uporabniki *niso prijavljeni* v svoj
račun](images/form-public.jpg)

> **Note** — Ta vtičnik zahteva, da je vaša namestitev sistema Galette javno
> dostopna in deluje z veljavnim potrdilom SSL.

## Namestitev

Najprej prenesite vtičnik:

* [Pridobite najnovejši vtičnik
  HelloAsso!](https://github.com/galette-plugins/plugin-helloasso/releases/latest)
* [Pridobite nočno gradnjo vtičnika
  HelloAsso!](https://github.com/galette-plugins/plugin-helloasso/releases/tag/nightly)

Ekstrahirajte preneseni arhiv v imenik Galette `plugins`. Na primer v sistemu
Linux (zamenjava *{url}* in *{version}* z ustreznima vrednostma):

```
$ cd /var/www/html/galette/plugins
$ wget {url}
$ tar xjvf galette-plugin-helloasso-{version}.tar.bz2
```

## Inicializacija podatkovne baze

Za delovanje ta vtičnik potrebuje več tabel v bazi podatkov. Oglejte si [vmesnik
za upravljanje vtičnikov
Galette](https://doc.galette.eu/en/master/plugins/index.html#plugins-managment).

In to je to; vtičnik *HelloAsso* je nameščen. :)

## Uporaba vtičnika

Ko je vtičnik nameščen, je skupina *Helloasso* dodana v meni Galette, ko je
uporabnik prijavljen, kar administratorjem in članom osebja omogoča, da določijo
nastavitve vtičnika in si ogledajo zgodovino plačil.

![Meni vtičnika](images/menu.jpg)

Obrazec za plačilo je na voljo na javnih straneh Galette.

Samo *prijavljeni* uporabniki lahko plačujejo prispevke *s podaljšanjem
članstva* (oz. članarino).

![Plačilni obrazec viden prijavljenim uporabnikom](images/form.jpg)

Obiskovalci (uporabniki *niso prijavljeni* v svoj račun) lahko plačujejo samo
prispevke *brez podaljšanja članstva* (ali donacije). V tem primeru se prispevek
v Galette ne ustvari samodejno, plačilo se prikaže samo v zgodovini plačil
vtičnika z vrednostjo »Brez« v stolpcu »Član«.

![Zgodovina plačil](images/history.jpg)

## Nastavitve

![Nastavitve](images/settings.jpg)

### Nastavitve

* **Povratni URL za nastavitev v HelloAsso**: ta URL vnesite v polje *Mon URL de
  callback* v razdelku »Intégrations et API« v računu vašega društva na
  platformi HelloAsso.
* **Omogoči testni način**: če želite uporabiti testni način, najprej ustvarite
  testni račun na [helloasso-sandbox.com](https://www.helloasso-sandbox.com).
  Nato lahko preverite, kako vtičnik deluje brez resničnih spletnih plačil.

  > **Warning** — V tem načinu nikoli ne uporabljajte pravih številk kreditnih
  > kartic, ampak samo testne kartice (glejte seznam testnih kartic iz [Stripe
  > documentation](https://docs.stripe.com/testing#cards) ali iz [Worldline
  > documentation](https://docs.sips.worldline-solutions.com/fr/cartes-de-test.html)).

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

* **Sprejmi nakazila SEPA**: privzeto so na plačilnem obrazcu HelloAsso
  predlagana samo plačila s kreditno kartico. Omogočite to možnost, če želite na
  obrazcu SEPA nakazila predlagati tudi plačila.
* **Vrste prispevkov**: v tej tabeli lahko onemogočite [vrste prispevkov,
  konfigurirane v
  Galette](https://doc.galette.eu/en/master/usermanual/contributions.html#contributions-types),
  za katere ne želite, da so predlagane kot razlog za plačilo na obrazcu za
  plačilo.

  *Vrste prispevkov z ničelnim zneskom ali katerih znesek ni konfiguriran, ne
  bodo ponujeni kot razlogi za plačilo na obrazcu, tudi če v tabeli niso
  označeni kot neaktivni.*

  > **Note** — Opis, prikazan pod vsakim plačilnim razlogom, predlaganim na
  > plačilnem obrazcu, je mogoče definirati v [konfiguraciji vrst
  > prispevkov](https://doc.galette.eu/en/master/usermanual/contributions.html#contributions-types)
  > Galette.

> **Note** — V nastavitvah Galette je mogoče določiti, kdo lahko dostopa do
> obrazca za plačilo. Izberite želeno možnost v [parametrih vidnosti javnih
> strani](https://doc.galette.eu/en/master/usermanual/preferences.html#parameters).

### Stanje povezave

Ta zaslon prikazuje, ali je vtičnik pravilno nastavljen in povezan s storitvijo
HelloAsso.

![Stanje povezave s HelloAsso](images/status.jpg)
