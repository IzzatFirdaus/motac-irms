# MOTAC IRMS SRS: Extensive Diagram Planning

This section details all diagrams (rajah) to be included in the SRS for the **Sistem Pengurusan Sumber Terintegrasi MOTAC (IRMS)**. Each diagram type is mapped to its SRS section, purpose, notation, source, and recommended tools. The goal is to visually model requirements, data, processes, actors, and compliance for robust system specification and future traceability.

---

## 1. **System Overview & Context Diagrams**

### **Rajah SRS-1: System Context Diagram**
- **Purpose:** Shows IRMS boundaries, external actors (users, government systems, email servers, reporting tools), and main data flows.
- **Notation:** Central system box, external actors, arrows for data flow.
- **Tool:** Draw.io, Mermaid, Visio.
- **Source:** SRS Introduction/System Overview.

### **Rajah SRS-2: Functional/Business Architecture Diagram**
- **Purpose:** Top-level modules (Pinjaman ICT, Helpdesk, Admin, Notification, Reporting) and their interconnections.
- **Notation:** Tree/hierarchy, boxes for modules, arrows for relationships.
- **Tool:** Draw.io, Mermaid.
- **Source:** SRS System Goals, Module List.

---

## 2. **Actors, Roles & Use Case Diagrams**

### **Rajah SRS-3: Actor Diagram**
- **Purpose:** Visualizes system actors, roles (Pemohon, Pegawai Penyokong, BPM, IT Admin, IT Agent, Admin), and their relationships.
- **Notation:** UML actor notation, stick figures, role hierarchy.
- **Tool:** Lucidchart, Mermaid, PlantUML.
- **Source:** SRS Actors & Roles section.

### **Rajah SRS-4: Use Case Diagram – Pinjaman ICT**
- **Purpose:** Shows interactions for equipment loan (application, approval, issuance, return).
- **Notation:** UML use case, actors, use case ovals, associations.
- **Tool:** Mermaid, Lucidchart.
- **Source:** SRS Functional Req. > Equipment Loan.

### **Rajah SRS-5: Use Case Diagram – Helpdesk**
- **Purpose:** Ticket lifecycle (create, assign, resolve, close), actors, and system boundaries.
- **Notation:** UML use case, actors, ovals.
- **Tool:** Mermaid, Lucidchart.
- **Source:** SRS Functional Req. > Helpdesk.

### **Rajah SRS-6: Use Case Diagram – Notification & Reporting**
- **Purpose:** Notification triggers, recipients, report generation, viewing.
- **Notation:** UML use case, actors, ovals.
- **Tool:** Lucidchart, Mermaid.
- **Source:** SRS Notification, Reporting.

---

## 3. **Data & Entity Relationship Diagrams**

### **Rajah SRS-7: ERD (Entity Relationship Diagram)**
- **Purpose:** Database entities (users, equipment, loan applications, transactions, helpdesk tickets, approvals, notifications, settings) and relationships.
- **Notation:** Boxes for entities, lines for relationships, cardinality.
- **Tool:** Draw.io, ERD tools, Mermaid.
- **Source:** SRS Data Architecture & Models.

### **Rajah SRS-8: Data Dictionary Table Reference**
- **Purpose:** Visual reference for all entity attributes and their relationships (can be combined with ERD or as a large table).
- **Notation:** Markdown table or diagram.
- **Tool:** Markdown, Excel.
- **Source:** SRS Data Dictionary.

---

## 4. **Workflow & Process Flow Diagrams**

### **Rajah SRS-9: Workflow – Pinjaman ICT**
- **Purpose:** Stepwise process from application to return; decision points, notifications.
- **Notation:** Flowchart, process boxes, decision diamonds, arrows.
- **Tool:** Mermaid, Draw.io.
- **Source:** SRS Process Modeling > Equipment Loan.

### **Rajah SRS-10: Workflow – Helpdesk Ticket**
- **Purpose:** End-to-end ticket process: creation, validation, assignment, resolution, closure.
- **Notation:** Flowchart.
- **Tool:** Mermaid, Draw.io.
- **Source:** SRS Process Modeling > Helpdesk.

### **Rajah SRS-11: Approval Routing Diagram**
- **Purpose:** Decision flow for hierarchical approval (grade validation, escalation, approval/rejection).
- **Notation:** Flowchart with decision nodes.
- **Tool:** Draw.io, Mermaid.
- **Source:** SRS Functional Req. > Approval Logic.

---

## 5. **Data Flow Diagrams (DFD)**

### **Rajah SRS-12: DFD – Pinjaman ICT Module**
- **Purpose:** Data movement: input, validation, storage, notification, reporting.
- **Notation:** Circles for processes, arrows for data flow, rectangles for actors, open rectangles for data stores.
- **Tool:** Draw.io, Mermaid.
- **Source:** SRS Data Flow Modeling > Equipment Loan.

### **Rajah SRS-13: DFD – Helpdesk Module**
- **Purpose:** Data flow for ticket creation, assignment, update, closure, notification.
- **Notation:** DFD symbols.
- **Tool:** Mermaid, Draw.io.
- **Source:** SRS Data Flow Modeling > Helpdesk.

---

## 6. **Integration & Interaction Diagrams**

### **Rajah SRS-14: Integration Points Diagram**
- **Purpose:** IRMS connections to external services (SMTP/email, analytics, other government APIs).
- **Notation:** Boxes for modules, arrows for integration/data exchange.
- **Tool:** Draw.io.
- **Source:** SRS Integration & Interfaces.

### **Rajah SRS-15: Notification Trigger Flow Diagram**
- **Purpose:** Sequence of notification events: action → service → dashboard/email → recipient.
- **Notation:** Sequence diagram, boxes/arrows.
- **Tool:** Mermaid (sequenceDiagram), Draw.io.
- **Source:** SRS Notification Logic.

---

## 7. **UI/UX Reference & Compliance**

### **Rajah SRS-16: MYDS Component Inventory Reference**
- **Purpose:** Reference or wireframe showing key MYDS components (buttons, forms, badges, navigation) adopted in IRMS.
- **Notation:** Wireframe or component inventory diagram.
- **Tool:** Figma, Draw.io, screenshots.
- **Source:** SRS UI/UX Compliance.

---

## 8. **Quality Assurance & Compliance Mapping**

### **Rajah SRS-17: MyGovEA Principles Compliance Matrix**
- **Purpose:** Matrix mapping SRS modules/processes to MyGovEA principles.
- **Notation:** Table/chart.
- **Tool:** Markdown, Draw.io.
- **Source:** SRS Compliance Checklist.

---

## **Diagram Table: SRS Inclusion Reference**

| No. | Diagram Name                   | Section                        | Purpose/Notes                                    | Tool/Format           |
|-----|------------------------------- |------------------------------- |--------------------------------------------------|-----------------------|
| 1   | System Context                 | System Overview                | Boundaries, actors, integrations                 | Draw.io, Mermaid      |
| 2   | Functional/Business Architecture| System Overview/Goals         | Module structure, relationships                  | Draw.io, Mermaid      |
| 3   | Actor Diagram                  | Actors & Roles                 | Actor-role mapping                               | Lucidchart, Mermaid   |
| 4   | Use Case Pinjaman ICT          | Use Case Modeling              | Loan process actor interactions                  | Mermaid, Lucidchart   |
| 5   | Use Case Helpdesk              | Use Case Modeling              | Helpdesk process actor interactions              | Mermaid, Lucidchart   |
| 6   | Use Case Notification/Reporting| Use Case Modeling              | Notification & reporting actor interactions      | Lucidchart, Mermaid   |
| 7   | ERD                            | Data Architecture              | Entity structure and relationships               | Draw.io, ERD tools    |
| 8   | Data Dictionary Table Reference| Data Architecture              | Entity attributes and relationships              | Markdown, Excel       |
| 9   | Workflow Pinjaman ICT          | Process Modeling               | Stepwise loan workflow                           | Mermaid, Draw.io      |
| 10  | Workflow Helpdesk Ticket       | Process Modeling               | Ticket workflow                                  | Mermaid, Draw.io      |
| 11  | Approval Routing               | Approval Logic                 | Hierarchical approval process                    | Draw.io, Mermaid      |
| 12  | DFD Pinjaman ICT               | Data Flow Modeling             | Data movement in loan module                     | Mermaid, Draw.io      |
| 13  | DFD Helpdesk                   | Data Flow Modeling             | Data movement in helpdesk                        | Mermaid, Draw.io      |
| 14  | Integration Points             | Integration & Interfaces       | External service integrations                    | Draw.io               |
| 15  | Notification Trigger Flow      | Notification Logic             | Notification event pipeline                      | Mermaid, Draw.io      |
| 16  | MYDS Component Inventory       | UI/UX Compliance               | UI component reference/wireframe                 | Figma, Draw.io        |
| 17  | MyGovEA Principles Matrix      | Compliance Checklist           | Compliance mapping to principles                 | Markdown, Draw.io     |

---

## **Methodology Notes**

- **Draft diagrams for each section:** Start with high-level sketches, refine based on requirements, data, and process details.
- **Consistent notation and numbering:** Use Rajah SRS-1, SRS-2, etc. for traceability and reference in document text.
- **Embed diagrams in relevant SRS sections:** E.g., context diagram in System Overview, ERD in Data Architecture.
- **Maintain a notation legend:** Include a table explaining diagram symbols for reader clarity.
- **Wireframe/Component diagrams:** Use MYDS standards for UI/UX compliance.
- **Compliance matrix:** Map every functional/non-functional requirement to MyGovEA principles in a dedicated diagram/table.
- **Store all diagram source files in /docs/diagrams or /img for versioning and updates.**

---

## **Next Steps**

1. Create diagram skeletons (mermaid/Draw.io/Figma) for each listed item.
2. Reference and caption each diagram in the SRS main document.
3. Ensure diagrams are reviewed for completeness, accuracy, and compliance before finalization.

---

> **Tip:** Always cross-reference diagrams with SRS tables for actors, entities, processes, and compliance to ensure clarity and completeness.
