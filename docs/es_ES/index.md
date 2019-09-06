---
layout: default
title: Index
lang: fr_FR
---

# Description

Plugin permettant une gestion de log personnalisée dans vos scénarios.
Il est possible de créer autant de log que voulu, différent niveau de log sont possible pour chaque fichier de log.
Cela permet d'organiser vos logs de scénario selon vos préférences, de par exemple regrouper toutes les actions sur un équipement dans le même log quelque soit le scénario.

La consultation des logs se fait via l'interface standard de Jeedom.
La purge des logs est également gérée par la config générale de Jeedom.

# Installation

Afin d’utiliser le plugin, vous devez le télécharger, l’installer et l’activer comme tout plugin Jeedom.

# Configuration de l'équipement

Le plugin se trouve dans le menu Plugins > Programmation.
Après avoir créé un nouvel équipement, les options habituelles sont disponnible.

Un équipement correspond à un log, le nom de l'équipement sera utilisé comme nom du fichier log.

> **Tip**
>
> Afin d'éviter des problèmes potentiels, le nom de l'équipementd doit être uniquement composé des lettres de a à z, en miniscule, et du caractère souligné "_", le première caractère doit être une lettre.

De plus, vous pouvez sélectionner le niveau de log à écrire: Debug, Info, Warning, Erreur.

# Les commandes

Chaque équipement (log) dispose d'une commande de type message par niveau de log:

- debug
- info
- warning
- error

Il suffit d'appeler la commande voulu avec le message et le log sera écrit en fonction du niveau de log configuré sur l'équipement.

# Le widget

Un widget core standard sera affiché avec les commandes sélectionnées (à configurer dans la page "Commandes" de l'équipement).