# language: fr
Fonctionnalité: Tester la commande d'indexation pour le type précis d'un index précis

Scénario: Lancer la commande d'indexation pour un type précis
    Etant donné que je lance la commande "elasticsearch:index:auth:user"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/index_auth_user.txt"

Scénario: Lancer la commande d'indexation pour un type précis avec l'option reset
    Etant donné que je lance la commande "elasticsearch:index:auth:user" avec les paramêtres contenus dans "params/reset.json"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/reindex_auth_user.txt"

Scénario: Lancer la commande d'indexation pour un document précis
    Etant donné que je lance la commande "elasticsearch:index:auth:user" avec les paramêtres contenus dans "params/id.json"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/index_auth_doc.txt"

Scénario: Lancer la commande d'indexation pour un document précis avec l'option reset (qui devrait être ignorée)
    Etant donné que je lance la commande "elasticsearch:index:auth:user" avec les paramêtres contenus dans "params/reset_id.json"
    Alors       ca devrait s'être bien déroulé
    Et          la sortie de la commande devrait être identique à "outputs/index_auth_doc.txt"

Scénario: Lancer la commande d'indexation pour un document précis qui à la fonction d'indexation non implémentée
    Etant donné que je lance la commande "elasticsearch:index:contract:company"
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Implement the method indexCompany as protected to index type company"
    Et          la sortie de la commande devrait être identique à "outputs/index_company.txt"

Scénario: Lancer la commande d'indexation pour un document précis qui à la fonction d'indexation non implémentée
    Etant donné que je lance la commande "elasticsearch:index:contract:company" avec les paramêtres contenus dans "params/id.json"
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Implement the method indexOneCompany as protected to index one type company"
    Et          la sortie de la commande devrait être identique à "outputs/index_company_doc.txt"

