---
title: Galette HelloAsso
description: Plugin pour gérer les paiements de cotisations et de dons via HelloAsso
---

> **Note** — HelloAsso s'adresse principalement aux associations et
> organisations à but non lucratif **françaises**.

Ce plugin fournit :

* un formulaire de paiement,
* un historique des paiements,
* la création automatique de contributions dans Galette une fois les paiements
  validés.

![Formulaire de paiement visible par les utilisateurs *non connectés* à leur
compte](images/form-public.jpg)

> **Note** — Ce plugin nécessite que votre instance de Galette soit accessible
> publiquement et servie avec un certificat SSL valide.

## Installation

Tout d'abord, téléchargez le plugin :

* [Obtenez le dernier plugin HelloAsso
  !](https://github.com/galette-plugins/plugin-helloasso/releases/latest)
* [Obtenez la nightly du plugin HelloAsso
  !](https://github.com/galette-plugins/plugin-helloasso/releases/tag/nightly)

Décompressez l'archive téléchargée dans le répertoire `plugins` de Galette. Par
exemple, sous Linux (en remplaçant *{url}* et *{version}* par les valeurs
correspondantes) :

```
$ cd /var/www/html/galette/plugins
$ wget {url}
$ tar xjvf galette-plugin-helloasso-{version}.tar.bz2
```

## Initialisation de la base de données

Pour fonctionner, ce plugin requiert des tables dans la base de données.
Référez-vous [à l'interface de gestion des plugins de
Galette](https://doc.galette.eu/en/master/plugins/index.html#plugins-managment).

Et c'est tout ; le plugin *HelloAsso* est installé. :)

## Utilisation du plugin

Une fois le plugin installé, un groupe *Helloasso* est ajouté au menu de Galette
lorsqu’un utilisateur est connecté, permettant aux administrateurs et membres du
bureau de définir les préférences du plugin et de consulter l'historique des
paiements.

![Menu du plugin](images/menu.jpg)

Le formulaire de paiement est accessible depuis les pages publiques de Galette.

Seuls les utilisateurs *connectés* peuvent payer des contributions *avec
extension d'adhésion* (ou cotisations).

![Formulaire de paiement visible par les utilisateurs
connectés](images/form.jpg)

Les visiteurs (utilisateurs *non connectés* à leur compte) ne peuvent payer que
des contributions *sans extension d’adhésion* (ou dons). Dans ce cas, aucune
contribution n’est créée automatiquement dans Galette, le paiement apparaît
uniquement dans l’historique des paiements du plugin avec la valeur "Aucun" dans
la colonne "Adhérent".

![Historique des paiements](images/history.jpg)

## Préférences

![Préférences](images/settings.jpg)

### Paramètres

* **URL de callback à configurer dans Helloasso** : cette URL est à renseigner
  dans le champ *"Mon URL de callback"* dans la section "Intégrations et API" du
  compte de votre association sur HelloAsso.
* **Activer le mode test** : pour utiliser le mode test, créez d'abord un compte
  de test sur [helloasso-sandbox.com](https://www.helloasso-sandbox.com). Vous
  pourrez ensuite tester le fonctionnement du plugin sans effectuer de
  véritables paiements en ligne.

  > **Warning** — Dans ce mode, n’utilisez jamais de véritables numéros de
  > cartes bancaire, mais uniquement des cartes fictives (voir la liste de
  > cartes fictives dans la [documentation de
  > Stripe](https://docs.stripe.com/testing#cards) ou dans la [documentation de
  > Worldline](https://docs.sips.worldline-solutions.com/fr/cartes-de-test.html)).

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

* **Accepter les virements SEPA** : par défaut, seuls les paiements avec une
  carte bancaire sont proposés sur le formulaire de paiement d'HelloAsso.
  Activez cette option si vous souhaitez également proposer sur le formulaire le
  paiement par virements SEPA.
* **Types de contribution** : dans ce tableau, vous pouvez désactiver les [types
  de contribution configurés dans
  Galette](https://doc.galette.eu/en/master/usermanual/contributions.html#contributions-types)
  que vous ne souhaitez pas voir proposés comme motif de paiement sur le
  formulaire de paiement.

  *Les types de contribution dont le montant est nul, ou dont le montant n’est
  pas configuré, ne seront pas proposés comme motifs de paiement sur le
  formulaire, même si ceux-ci ne sont pas marqués comme inactifs dans le
  tableau.*

  > **Note** — Une description, affichée sous chaque motif de paiement proposé
  > sur le formulaire de paiement, peut être définie dans la [configuration des
  > types de
  > contributions](https://doc.galette.eu/en/master/usermanual/contributions.html#contributions-types)
  > de Galette.

> **Note** — Il est possible de décider qui peut accéder au formulaire de
> paiement dans les préférences de Galette. Choisissez l'option souhaitée dans
> les [paramètres de visibilité des pages
> publiques](https://doc.galette.eu/en/master/usermanual/preferences.html#parameters).

### État de la connexion

Cet écran montre si le plugin est correctement paramétré et connecté à
HelloAsso.

![État de connexion à HelloAsso](images/status.jpg)
