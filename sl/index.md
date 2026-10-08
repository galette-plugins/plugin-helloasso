---
title: Galette HelloAsso
description: Vtičnik za upravljanje plačil članarin in donacij prek storitve HelloAsso
---

> **Note** — HelloAsso primarily serves **French** associations and non-profit
> organizations.

Ta vtičnik omogoča:

* a payment form,
* zgodovina plačil,
* automatic creation of contributions in Galette once payments are validated.

![Payment form visible by users *not logged* into their
account](images/form-public.jpg)

> **Note** — Ta vtičnik zahteva, da je vaša namestitev sistema Galette javno
> dostopna in deluje z veljavnim potrdilom SSL.

## Namestitev

First of all, download the plugin:

* [Get latest HelloAsso
  plugin!](https://github.com/galette-plugins/plugin-helloasso/releases/latest)
* [Get HelloAsso plugin nightly
  build!](https://github.com/galette-plugins/plugin-helloasso/releases/tag/nightly)

Extract the downloaded archive into Galette `plugins` directory. For example, on
Linux (replacing *{url}* and *{version}* with the corresponding values):

```
$ cd /var/www/html/galette/plugins
$ wget {url}
$ tar xjvf galette-plugin-helloasso-{version}.tar.bz2
```

## Inicializacija podatkovne baze

In order to work, this plugin requires several tables in the database. See the
[Galette plugins management
interface](https://doc.galette.eu/en/master/plugins/index.html#plugins-managment).

And that's it; the *HelloAsso* plugin is installed. :)

## Plugin usage

Once the plugin is installed, a *Helloasso* group is added to the Galette menu
when a user is logged-in, allowing administrators and staff members to define
the settings of the plugin and view the payments history.

![Plugin's menu](images/menu.jpg)

Obrazec za plačilo je na voljo na javnih straneh Galette.

Only *logged-in* users can pay contributions *with membership extension* (or
membership fees).

![Payment form visible by logged-in users](images/form.jpg)

Visitors (users *not logged* into their account) can only pay contributions
*without membership extension* (or donations). In this case, no contribution is
automatically created in Galette, the payment only appears in the plugin's
payment history with the value "None" in the "Member" column.

![Zgodovina plačil](images/history.jpg)

## Nastavitve

![Nastavitve](images/settings.jpg)

### Nastavitve

* **Povratni URL za nastavitev v HelloAsso**: ta URL vnesite v polje *Mon URL de
  callback* v razdelku »Intégrations et API« v računu vašega društva na
  platformi HelloAsso.
* **Enable test mode**: to use the test mode, first create a test account on
  [helloasso-sandbox.com](https://www.helloasso-sandbox.com). You can then check
  how the plugin works without making real online payments.

  > **Warning** — In this mode, never use real credit card numbers, but only
  > test cards (see the list of test cards from the [Stripe
  > documentation](https://docs.stripe.com/testing#cards) or from the [Worldline
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

* **Accept SEPA transfers** : by default, only payments with a credit card are
  proposed on HelloAsso's payment form. Enable this option if you also want to
  propose on the form SEPA transfers payments.
* **Contribution types**: in this table, you can disable the [contribution types
  configured in
  Galette](https://doc.galette.eu/en/master/usermanual/contributions.html#contributions-types)
  that you do not want to be proposed as a payment reason on the payment form.

  *Contribution types with a zero amount, or whose amount is not configured,
  will not be offered as payment reasons on the form, even if they are not
  marked as inactive in the table.*

  > **Note** — A description, displayed below each payment reason proposed on
  > the payment form, can be defined from the [configuration of the
  > contributions
  > types](https://doc.galette.eu/en/master/usermanual/contributions.html#contributions-types)
  > of Galette.

> **Note** — It is possible to decide who can access the payment form in
> Galette's settings. Choose the desired option in the [public pages visibility
> parameters](https://doc.galette.eu/en/master/usermanual/preferences.html#parameters).

### Stanje povezave

Ta zaslon prikazuje, ali je vtičnik pravilno nastavljen in povezan s storitvijo
HelloAsso.

![Stanje povezave s HelloAsso](images/status.jpg)
