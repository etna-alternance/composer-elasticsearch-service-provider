# language: fr
Fonctionnalité: Tester d'indexer des documents avec l'objet dédié

Scénario: Appeler la fonction d'indexation unique quand elle est bien implémentée
    Etant donné que j'utilise l'indexer auth pour indexer le document de type user avec l'id 42
    Alors       ca devrait s'être bien déroulé

Scénario: Appeler la fonction d'indexation globale quand elle est bien implémentée
    Etant donné que j'utilise l'indexer auth pour indexer les documents de type user
    Alors       ca devrait s'être bien déroulé

Scénario: Appeler la fonction d'indexation unique quand elle n'est pas bien implémentée
    Etant donné que j'utilise l'indexer contract pour indexer le document de type company avec l'id 42
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Implement the method indexOneCompany as protected to index one type company"

Scénario: Appeler la fonction d'indexation globale quand elle n'est pas bien implémentée
    Etant donné que j'utilise l'indexer contract pour indexer les documents de type company
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Implement the method indexCompany as protected to index type company"

Scénario: Appeler la fonction d'indexation unique pour un type non géré
    Etant donné que j'utilise l'indexer auth pour indexer le document de type not_a_user avec l'id 42
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Invalid type not_a_user for index auth"

Scénario: Appeler la fonction d'indexation globale pour un type non géré
    Etant donné que j'utilise l'indexer auth pour indexer les documents de type not_a_user
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Invalid type(s) not_a_user for index auth"

Scénario: Appeler la fonction d'indexation globale sans spécifier de type
    Etant donné j'utilise l'indexer contract pour indexer les documents de tout les types
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Implement the method indexContract as protected to index type contract"
