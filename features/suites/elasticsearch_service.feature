# language: fr
Fonctionnalité: Tester les différentes fonctions du service

Scénario: Utiliser un indexer qui existe
    Etant donné que je veux récupérer l'indexer pour l'elasticsearch auth
    Alors       ca devrait s'être bien déroulé

Scénario: Utiliser un indexer qui n'existe pas
    Etant donné que je veux récupérer l'indexer pour l'elasticsearch unimplemented
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "There is no available indexer for unimplemented"

Scénario: Utiliser un client qui existe
    Etant donné que je veux récupérer le client pour l'elasticsearch auth
    Alors       ca devrait s'être bien déroulé

Scénario: Utiliser un client qui n'existe pas
    Etant donné que je veux récupérer le client pour l'elasticsearch unimplemented
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "There is no available client for unimplemented"

Scénario: Créer un index avec l'option reset sur un elasticsearch configuré
    Etant donné que je reset l'index sur l'elasticsearch auth
    Alors       ca devrait s'être bien déroulé
    Et          les settings de l'elasticsearch auth devraient être identique à "base_settings.json"

Scénario: Créer un index sur un elasticsearch non configuré
    Etant donné que je crée l'index sur l'elasticsearch pas_auth
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Application is not configured for index pas_auth"

Scénario: Lock un index configuré
    Etant donné que je lock l'index de l'elasticsearch auth
    Alors       ca devrait s'être bien déroulé
    Et          l'elasticsearch auth devrait être lock

Scénario: Lock un index non configuré
    Etant donné que je lock l'index de l'elasticsearch not_auth_again
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Application is not configured for index not_auth_again"

Scénario: Unlock un index configuré
    Etant donné que j'unlock l'index de l'elasticsearch auth
    Alors       ca devrait s'être bien déroulé
    Et          l'elasticsearch auth ne devrait pas être lock

Scénario: Unlock un index non configuré
    Etant donné que j'unlock l'index de l'elasticsearch not_auth_again
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Application is not configured for index not_auth_again"

Scénario: Reset l'index alors que l'alias n'existe pas
    Etant donné que je delete l'alias de l'index de l'elasticsearch auth
    Quand       je reset l'index sur l'elasticsearch auth
    Alors       ca devrait s'être bien déroulé
    Et          les settings de l'elasticsearch auth devraient être identique à "base_settings.json"

Scénario: Reset l'index alors que l'index n'existe pas
    Etant donné que je delete l'index de l'elasticsearch auth
    Quand       je reset l'index sur l'elasticsearch auth
    Alors       ca devrait s'être bien déroulé
    Et          les settings de l'elasticsearch auth devraient être identique à "base_settings.json"

Scénario: Créer un type sur un elasticsearch configuré
    Quand       je crée le type user sur l'elasticsearch auth
    Alors       ca devrait s'être bien déroulé
    Et          le mapping du type user de l'elasticsearch auth devrait être identique à "user_mapping.json"

Scénario: Créer un type sur un elasticsearch non configuré
    Etant donné que je crée le type user sur l'elasticsearch not_auth_again
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Application is not configured for index not_auth_again"

Scénario: Créer un type sur un elasticsearch configuré
    Etant donné que je crée le type not_a_user sur l'elasticsearch auth
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Application is not configured for type not_a_user"

Scénario: Créer un type avec l'option reset sur un elasticsearch configuré
    Etant donné que je delete le mapping du type user de l'elasticsearch auth
    Quand       je reset le type user sur l'elasticsearch auth
    Alors       ca devrait s'être bien déroulé
    Et          le mapping du type user de l'elasticsearch auth devrait être identique à "user_mapping.json"

Scénario: Créer un type avec l'option reset sur un elasticsearch non configuré
    Etant donné que je reset le type user sur l'elasticsearch not_auth_again
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Application is not configured for index not_auth_again"

Scénario: Créer un type non géré avec l'option reset sur un elasticsearch configuré
    Etant donné que je reset le type not_a_user sur l'elasticsearch auth
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Application is not configured for type not_a_user"

Scénario: Créer un type avec l'option reset sur un elasticsearch configuré sans avoir le mapping
    Etant donné que je reset le type company sur l'elasticsearch contract
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Mapping file for type company does not exist"
