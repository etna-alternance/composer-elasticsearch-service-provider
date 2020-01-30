# language: fr
Fonctionnalité: Tester la commande d'indexation pour un index précis

Scénario: Lancer la commande d'indexation pour un index
    Etant donné que je lance la commande "elasticsearch:index:auth"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/index_auth.txt"

Scénario: Lancer la commande d'indexation pour un index avec l'option reset
    Etant donné que je lance la commande "elasticsearch:index:auth" avec les paramêtres contenus dans "params/reset.json"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/reindex_auth.txt"

Scénario: Lancer la commande d'indexation pour un document précis
    Etant donné que je lance la commande "elasticsearch:index:auth" avec les paramêtres contenus dans "params/id.json"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/index_auth_doc.txt"
