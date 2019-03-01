# language: fr
Fonctionnalité: Tester la commande d'indexation globale

Scénario: Lancer la commande d'indexation globale pour un index à qui il manque des fonctions
    Etant donné que je lance la commande "elasticsearch:index"
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Implement the method indexContract as protected to index type contract"
    Et          la sortie de la commande devrait être identique à "outputs/index_contract.txt"

Scénario: Lancer la commande d'indexation globale pour un index
    Etant donné que je lance la commande "elasticsearch:index" avec les paramêtres contenus dans "params/index_auth.json"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/index_auth.txt"

Scénario: Lancer la commande d'indexation globale avec le reset pour un index à qui il manque des fonctions
    Etant donné que je lance la commande "elasticsearch:index" avec les paramêtres contenus dans "params/reset.json"
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Mapping file for type contract does not exist"
    Et          la sortie de la commande devrait être identique à "outputs/reindex_contract.txt"

Scénario: Lancer la commande d'indexation globale avec le reset pour un index
    Etant donné que je lance la commande "elasticsearch:index" avec les paramêtres contenus dans "params/auth_reset.json"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/reindex_auth.txt"
