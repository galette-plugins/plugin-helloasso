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

> **Opomba** — Ta vtičnik zahteva, da je vaša namestitev sistema Galette javno
> dostopna in deluje z veljavnim potrdilom SSL.

## Namestitev

Najprej prenesite vtičnik:

[![Prenesite najnovejšo različico vtičnika
HelloAsso!](https://img.shields.io/badge/1.0.0-HelloAsso-ffb619?style=for-the-badge&logo=php&logoColor=white&label=1.0.0&color=ffb619)](https://github.com/galette-plugins/plugin-helloasso/releases/tag/1.0.0)
[![Pridobite nočno različico vtičnika
HelloAsso!](https://img.shields.io/badge/Nightly-HelloAsso-ffb619?style=for-the-badge&logo=php&logoColor=white&label=Nightly&color=ffb619)](https://galette.eu/download/plugins/galette-plugin-helloasso-dev.tar.bz2)

Extract the downloaded archive into the Galette `plugins` directory. For
example, on Linux (replacing *{url}* and *{version}* with the matching values):

```
$ cd /var/www/html/galette/plugins
$ wget {url}
$ tar xjvf galette-plugin-helloasso-{version}.tar.bz2
```

## Database initialization

This plugin needs several tables in the database. See [Galette's plugins
management
interface](https://doc.galette.eu/en/master/plugins/#plugins-management-interface).

And that's it, the *HelloAsso* plugin is installed. :)

## Using the plugin

Once the plugin is installed, a *Helloasso* group is added to the Galette menu
when a user is logged in. It lets administrators and staff members set the
plugin preferences and browse the payment history.

![Plugin menu](images/menu.jpg)

The payment form is available from Galette's public pages.

Only users *logged in* to their account can pay contributions *with membership
extension* (membership fees).

![Payment form as seen by a user logged in to their account](images/form.jpg)

Visitors (users *not logged in* to their account) can only pay contributions
*without membership extension* (donations). In that case, no contribution is
created in Galette: the payment only appears in the plugin's payment history.

![Payment history](images/history.jpg)

## Preferences

![Preferences](images/settings.jpg)

### Settings

* **Callback URL to set in HelloAsso**: enter this URL in the *"Mon URL de
  callback"* field, in the "Intégrations et API" section of your association's
  HelloAsso account.
* **Enable test mode**: to use test mode, first create a test account on
  [helloasso-sandbox.com](https://www.helloasso-sandbox.com). You can then check
  how the plugin works without making real online payments. **WARNING** *in this
  mode, never use real credit card numbers, only test cards (see the test cards
  list from
  [Stripe](https://docs.stripe.com/testing?numbers-or-method-or-token=card-numbers#visa)
  or from
  [Worldline](https://docs.sips.worldline-solutions.com/fr/cartes-de-test.html)).*
* **Your organizationSlug**: you will find it in your browser's address bar
  while logged in to your association's HelloAsso account. It is the first part
  of your account URL path. For example, in the URL
  `https://admin.helloasso.com/{organizationSlug}/accueil` it is
  *{organizationSlug}*.
* **Your clientId**: you will find it in the *"Mon clientID"* field, in the
  "Intégrations et API" section of your association's HelloAsso account.
* **Your clientSecret**: you will find it in the *"Mon clientSecret"* field, in
  the "Intégrations et API" section of your association's HelloAsso account.

![The "Intégrations et API" section of the HelloAsso
account](images/helloasso-account.jpg)

* **Contribution types**: in this table, you can disable the [contribution types
  configured in
  Galette](https://doc.galette.eu/en/master/usermanual/contributions.html#contributions-types)
  that you do not want to offer as a payment purpose on the online payment form.

  *Contribution types with a zero amount, or no amount set, are never offered as
  payment purposes on the form, even if they are not marked as inactive in the
  table.*

### Connection status

This screen shows whether the plugin is correctly set up and connected to
HelloAsso.

![HelloAsso connection status](images/status.jpg)
