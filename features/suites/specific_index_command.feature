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

Scénario: Lancer la commande d'indexation pour un index en spécifiant le type
    Etant donné que je lance la commande "elasticsearch:index:auth" avec les paramêtres contenus dans "params/user.json"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/index_auth_user.txt"

Scénario: Lancer la commande d'indexation pour un index avec l'option reset en spécifiant le type
    Etant donné que je lance la commande "elasticsearch:index:auth" avec les paramêtres contenus dans "params/reset_user.json"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/reindex_auth_user.txt"

Scénario: Lancer la commande d'indexation pour un index qui à la fonction d'indexation non implémentée
    Etant donné que je lance la commande "elasticsearch:index:contract"
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Implement the method indexContract as protected to index type contract"
    Et          la sortie de la commande devrait être identique à "outputs/index_contract.txt"
