# ProcurePath: Documentation & Planification

## 1. Use Case Diagram (PlantUML)

```plantuml
@startuml
left to right direction
actor "Demandeur" as D
actor "Manager" as M
actor "Directeur" as DR
actor "Acheteur" as A

package "ProcurePath System" {
  usecase "Créer une demande d'achat" as UC1
  usecase "Approuver la demande (Step 1)" as UC2
  usecase "Approuver la demande (Step 2)" as UC3
  usecase "Gérer les fournisseurs & Devis" as UC4
  usecase "Sélectionner le meilleur devis" as UC5
  usecase "Livraison & Clôture" as UC6
  usecase "Chat en temps réel" as UC7
  usecase "Recevoir Notifications" as UC8
}

D --> UC1
D --> UC7
D --> UC8

M --> UC2
M --> UC7
M --> UC8

DR --> UC3
DR --> UC5
DR --> UC7
DR --> UC8

A --> UC4
A --> UC6
A --> UC7
A --> UC8

UC1 <.. UC2 : <<include>>
UC2 <.. UC3 : <<include>>
UC3 <.. UC4 : <<include>>
@enduml
```

---

## 2. Class Diagram (PlantUML)

```plantuml
@startuml
package "Models" {
  class User {
    +id: int
    +name: string
    +email: string
    +role: string
  }

  class Department {
    +id: int
    +name: string
    +manager_id: int
  }

  class PurchaseRequest {
    +id: int
    +title: string
    +description: string
    +status: string
    +priority: string
    +user_id: int
  }

  class RequestItem {
    +id: int
    +productName: string
    +quantity: int
  }

  class Approval {
    +id: int
    +step: int
    +decision: string
    +comment: text
  }

  class Message {
    +id: int
    +content: text
    +user_id: int
  }

  class Devis {
    +id: int
    +price: float
    +deliveryTime: string
    +status: string
  }

  class Supplier {
    +id: int
    +name: string
    +contact: string
  }
}

User "1" -- "0..*" PurchaseRequest : creates
Department "1" -- "0..*" PurchaseRequest : belongs to
PurchaseRequest "1" -- "1..*" RequestItem : contains
PurchaseRequest "1" -- "0..*" Approval : undergoes
PurchaseRequest "1" -- "0..*" Message : has discussion
PurchaseRequest "1" -- "0..*" Devis : manages quotes
Devis "1" -- "1" Supplier : provided by
User "1" -- "0..*" Message : sends
@enduml
```

---

## 3. Logique Métier (Enums & Acteurs)

### **États de la Demande (Enums)**
- `draft` : Brouillon (créateur uniquement).
- `pending_manager` : En attente validation Manager.
- `pending_directeur` : En attente validation Directeur.
- `approved` : Validé, prêt pour traitement.
- `in_progress` : Pris en charge par l'Acheteur.
- `consultation` : Recherche de fournisseurs/devis.
- `ordered` : Commande passée.
- `delivered` : Livraison effectuée (Clôturé).
- `rejected` : Demande refusée.

### **Actions par Acteur**
- **Demandeur** : Créer, enregistrer brouillon, soumettre, discuter.
- **Manager** : Approuver/Rejeter étape 1 (Département).
- **Directeur** : Approuver/Rejeter étape 2, Sélectionner Devis, Administration.
- **Acheteur** : Gérer Fournisseurs, Saisir Devis, Mettre à jour les étapes logistiques.

---

## 4. Simple Planification (KISS)

### **Phase 1: Backend Foundation**
- Auth (Sanctum) & RoleMiddleware.
- Workflow API (CRUD + Approvals).
- Real-time (Reverb + Events).

### **Phase 2: Frontend Core**
- Dashboard filtré par rôle.
- Formulaires de création et validation.
- Gestion des Devis (Interface Acheteur/Directeur).

### **Phase 3: Polish & Real-time**
- Chat discussion & Pièces jointes.
- Système de notifications temps réel.
- Thème Glassmorphism final.
