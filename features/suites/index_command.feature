# language: fr
Fonctionnalité: Tester la commande d'indexation globale

Scénario: Lancer la commande d'indexation globale pour un index à qui il manque des fonctions
    Etant donné que je lance la commande "elasticsearch:index"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/index_all.txt"

Scénario: Lancer la commande d'indexation globale pour un index
    Etant donné que je lance la commande "elasticsearch:index" avec les paramêtres contenus dans "params/index_auth.json"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/index_auth.txt"

Scénario: Lancer la commande d'indexation globale avec le reset pour un index à qui il manque des fonctions
    Etant donné que je lance la commande "elasticsearch:index" avec les paramêtres contenus dans "params/reset.json"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/reindex_all.txt"

Scénario: Lancer la commande d'indexation globale avec le reset pour un index
    Etant donné que je lance la commande "elasticsearch:index" avec les paramêtres contenus dans "params/auth_reset.json"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/reindex_auth.txt"
