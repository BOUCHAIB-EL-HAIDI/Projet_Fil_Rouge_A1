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

## 3. Simple Planification (KISS)

### **Phase 1: Backend Foundation (The Engine)**
1.  **Auth & Roles**: Setup Sanctum and the `RoleMiddleware` you just created.
2.  **Workflow API**: Create the standard CRUD for Purchase Requests and the Approval logic.
3.  **Broadcasting**: Configure Reverb and Events (`MessageSent`, `StatusUpdated`) for real-time.
4.  **Module Fournisseurs**: Basic API to manage Suppliers and their Quotes (Devis).

### **Phase 2: Frontend Core (The Cockpit)**
1.  **Auth Pages**: Login/Register with instant redirection.
2.  **Dashboard**: A clear list of requests filtered by the user's role.
3.  **Request Flow**:
    - **Step 1**: Creation form (Demandeur).
    - **Step 2**: Approval UI with comments (Manager/Directeur).
    - **Step 3**: Quote management (Acheteur).
    - **Step 4**: Quote selection (Directeur).

### **Phase 3: Real-time & Polish (The Turbo)**
1.  **Chat Integration**: Connect Echo to the discussion sub-module.
2.  **Notifications**: Real-time bell alerts for status changes.
3.  **UI Refinement**: Ensure the dark "Glassmorphism" theme is consistent across all pages.

### **Phase 4: Validation (The Crash Test)**
1.  **End-to-End Test**: Simulate a full request from Demandeur -> Manager -> Directeur -> Acheteur -> Directeur (Quote) -> Delivered.
2.  **Bug Squashing**: Fix minor UI or WebSocket glitches.
