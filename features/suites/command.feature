# language: fr
Fonctionnalité: Tester les commandes

Scénario: Je devrais avoir toutes les commandes d'implémentées
    Alors la commande "elasticsearch:index" devrait exister
    Et    la commande "elasticsearch:index:auth" devrait exister
    Et    la commande "elasticsearch:index:auth:user" devrait exister
    Et    la commande "elasticsearch:index:contract" devrait exister
    Et    la commande "elasticsearch:index:contract:company" devrait exister
    Et    la commande "elasticsearch:index:contract:contract" devrait exister
