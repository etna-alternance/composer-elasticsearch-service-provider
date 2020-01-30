# language: fr
Fonctionnalité: Tester d'indexer des documents avec l'objet dédié

Scénario: Appeler la fonction d'indexation unique quand elle est bien implémentée
    Etant donné que j'utilise l'indexer auth pour indexer le document avec l'id 42
    Alors       ca devrait s'être bien déroulé

Scénario: Appeler la fonction d'indexation globale quand elle est bien implémentée
    Etant donné que j'utilise l'indexer auth pour indexer les documents
    Alors       ca devrait s'être bien déroulé

Scénario: Appeler la fonction d'indexation unique quand elle n'est pas bien implémentée
    Etant donné que j'utilise l'indexer contract pour indexer le document avec l'id 42
    Alors       ca devrait s'être bien déroulé

Scénario: Appeler la fonction d'indexation globale quand elle n'est pas bien implémentée
    Etant donné que j'utilise l'indexer contract pour indexer les documents
    Alors       ca devrait s'être bien déroulé
