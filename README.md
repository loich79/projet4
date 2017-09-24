Projet 4 : Développez un back-end pour un client
================================================


Énoncé
======

Ce projet doit être réalisé avec le suivi de votre mentor. Prenez contact avec lui pour en savoir plus.

Contexte
--------

Le musée du Louvre vous a missionné pour un projet ambitieux : créer un nouveau système de réservation et de gestion des tickets en ligne pour diminuer les longues files d’attente et tirer parti de l’usage croissant des smartphones.

Cahier des charges
------------------

L’interface doit être accessible aussi bien sur ordinateur de bureau que tablettes et smartphones, et utiliser pour cela un design responsive.

L’interface doit être fonctionnelle, claire et rapide avant tout. Le client ne souhaite pas surcharger le site d’informations peu utiles : l’objectif est de permettre aux visiteurs d’acheter un billet rapidement.

Il existe 2 types de billets : le billet « Journée » et le billet « Demi-journée » (il ne permet de rentrer qu’à partir de 14h00). Le musée est ouvert tous les jours sauf le mardi (et fermé les 1er mai, 1er novembre et 25 décembre).

Le musée propose plusieurs types de tarifs :

 - Un tarif « normal » à partir de 12 ans à 16 €
-- Un tarif « enfant » à partir de 4 ans et jusqu’à 12 ans, à 8 € (l’entrée est gratuite pour les enfants de moins de 4 ans)
 - Un tarif « senior » à partir de 60 ans pour 12  €
 - Un tarif « réduit » de 10 € accordé dans certaines conditions (étudiant, employé du musée, d’un service du Ministère de la Culture, militaire…)

Pour commander, on doit sélectionner :

 - Le jour de la visite
 - Le type de billet (Journée, Demi-journée…). On peut commander un billet pour le jour même mais on ne peut plus commander de billet « Journée » une fois 14h00 passées.
 - Le nombre de billets souhaités

Le client précise qu’il n’est pas possible de réserver pour les jours passés (!), les dimanches, les jours fériés et les jours où plus de 1000 billets ont été vendus en tout pour ne pas dépasser la capacité du musée.

Pour chaque billet, l’utilisateur doit indiquer son nom, son prénom, son pays et sa date de naissance. Elle déterminera le tarif du billet.

Si la personne dispose du tarif réduit, elle doit simplement cocher la case « Tarif réduit ». Le site doit indiquer qu’il sera nécessaire de présenter sa carte d’étudiant, militaire ou équivalent lors de l’entrée pour prouver qu’on bénéficie bien du tarif réduit.

Le site récupèrera par ailleurs l’e-mail du visiteur afin de lui envoyer les billets. Il ne nécessitera pas de créer un compte pour commander.

Le visiteur doit pouvoir payer avec la solution Stripe par carte bancaire.

Le site doit gérer le retour du paiement. En cas d’erreur, il invite à recommencer l’opération. Si tout s’est bien passé, la commande est enregistrée et les billets sont envoyés au visiteur.

Vous utiliserez les environnements de test fournis par Stripe pour simuler la transaction, afin de ne pas avoir besoin de rentrer votre propre carte bleue.

(La création d'un back-office pour lister les clients et commandes n'est pas demandée. Seule l'interface client est nécessaire ici.)

_Le billet_


Un email de confirmation sera envoyé à l’utilisateur et fera foi de billet.

Le mail doit indiquer:

 - Le nom et le logo du musée
 - La date de la réservation
 - Le tarif
 - Le nom de chaque visiteur
 - Le code de la réservation (un ensemble de lettres et de chiffres) 

Livrables attendus
-

 - Document de présentation de la solution pour le client, incluant la note de cadrage (PDF)
 - Code source complet du projet versionné avec Git, développé avec le framework PHP Symfony
 - Quelques (4-5) tests unitaires et fonctionnels que l’on peut exécuter
 
Compétences à valider
-

- Elaborer les différents éléments d’un projet
- Prendre en main le framework Symfony
- Créer un site Internet, de sa conception à sa livraison



Commit 
======

1/ first commit
-
Initialisation du repository avec les fichiers créés de base par Symfony

2/ 	Création du template pour la base et Ajout des entités
-

3/ Création de la premiere page du tunnel : choix des billets
-

4/ 	Création de la deuxieme page du tunnel : infos visiteurs
-

5/ Refactoring des entités
-
déplacement des attributs type et dateVisite de billet vers commande

6/ Ajout des validateurs pour les attributs des entités
-

7/ Ajout de la gestion de paiement et de l'envoi de l'email
-

8/ factorisation du controleur : découpage en services
-

9/ factorisation du validateur date de visite : re-découpage
-

10/ Ajout CSS à la page index
-

11/ Ajout du step wizard
-

12/ Ajout CSS à la page choixVisite
-

13/ Ajout CSS à la page infosVisiteurs
-

14/ Ajout CSS aux pages paiement et confirmationPaiement
-

15/ ajout de la gestion des erreurs et de leur affichage
-

16/ ajout tests unitaires pour calculateurTarif et route "/" + correction d'erreur detecté par l'analyse insight de sensiolabs
-

17/ ajout tests unitaires pour les routes + modifications de la logique d'affichage des email + création d'un service pour l'envoi de l'email
-

18/ correction suite analyse par sensiolabs insight
-

19/ correction suite analyse par sensiolabs insight #2
-

20/ ajout de tests unitaires et fonctionnels + data fixtures + déplacement du code js de la page choix visiteurs dans un fichier .js
-

21/ ajustement de l'affichage (correction css) + Ajout de textes informatifs
-

22/ ajustement de l'affichage responsive (correction css)
-

23/ modification de la gestion des commandes de 0€ + ajout de commentaires et mise à jour du readme
-

24/ correction orthographique et css (image décalée en version small)
-
