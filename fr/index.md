---
title: Galette HelloAsso
description: Plugin pour gérer les paiements de cotisations et de dons via HelloAsso
---

> **Note** — HelloAsso primarily serves **French** associations and non-profit
> organizations.

Ce plugin fournit :

* a payment form,
* un historique des paiements,
* automatic creation of contributions in Galette once payments are validated.

![Payment form visible by users *not logged* into their
account](images/form-public.jpg)

> **Note** — Pour fonctionner, ce plugin nécessite que votre instance de Galette
> soit accessible publiquement et servie avec un certificat SSL valide.

## Installation

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

## Initialisation de la base de données

In order to work, this plugin requires several tables in the database. See the
[Galette plugins management
interface](https://doc.galette.eu/en/master/plugins/index.html#plugins-managment).

And that's it; the *HelloAsso* plugin is installed. :)

## Plugin usage

Once the plugin is installed, a *Helloasso* group is added to the Galette menu
when a user is logged-in, allowing administrators and staff members to define
the settings of the plugin and view the payments history.

![Plugin's menu](images/menu.jpg)

Le formulaire de paiement est accessible depuis les pages publiques de Galette.

Only *logged-in* users can pay contributions *with membership extension* (or
membership fees).

![Payment form visible by logged-in users](images/form.jpg)

Visitors (users *not logged* into their account) can only pay contributions
*without membership extension* (or donations). In this case, no contribution is
automatically created in Galette, the payment only appears in the plugin's
payment history with the value "None" in the "Member" column.

![Écran de l'historique des paiements](images/history.jpg)

## Préférences

![Écran des préférences](images/settings.jpg)

### Paramètres

* **URL de callback à configurer dans Helloasso** : cette URL est à renseigner
  dans le champ *"Mon URL de callback"* dans la section "Intégrations et API" du
  compte de votre association sur HelloAsso.
* **Enable test mode**: to use the test mode, first create a test account on
  [helloasso-sandbox.com](https://www.helloasso-sandbox.com). You can then check
  how the plugin works without making real online payments.

  > **Warning** — In this mode, never use real credit card numbers, but only
  > test cards (see the list of test cards from the [Stripe
  > documentation](https://docs.stripe.com/testing#cards) or from the [Worldline
  > documentation](https://docs.sips.worldline-solutions.com/fr/cartes-de-test.html)).

* **Votre organizationSlug** : vous trouverez cette information dans la barre
  d'URL de votre navigateur connecté au compte de votre association sur
  HelloAsso. Il s'agit de la première partie du chemin d'accès de l'URL de votre
  compte. Par exemple, dans l'URL
  `https://admin.helloasso.com/{organizationSlug}/accueil` il s'agit de
  *{organizationSlug}*.
* **Votre clientId** : vous trouverez cette information dans le champ *"Mon
  clientID"* dans la section "Intégrations et API" du compte de votre
  association sur HelloAsso.
* **Votre clientSecret** : vous trouverez cette information dans le champ *"Mon
  clientSecret"* dans la section "Intégrations et API" du compte de votre
  association sur HelloAsso.

![Section "Intégrations et API" du compte
HelloAsso](images/helloasso-account.jpg)

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

### État de la connexion

Cet écran montre si le plugin est correctement paramétré et connecté à
HelloAsso.

![Écran de l'état de connexion à HelloAsso](images/status.jpg)
