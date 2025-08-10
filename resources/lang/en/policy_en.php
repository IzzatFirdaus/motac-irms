<?php

return [
    // Title for the Privacy Policy page (used in Blade for translation)
    'title' => 'Privacy Policy',
    // Markdown content for the Privacy Policy
    'content' => <<< 'MD'
# Privacy Policy for the MOTAC Integrated Resource Management System

**Last Updated:** August 7, 2025

---

## 1. Introduction

This Privacy Policy explains how the Information Management Division (Bahagian Pengurusan Maklumat – BPM) at the Ministry of Tourism, Arts and Culture (MOTAC), Malaysia ("we", "us", or "our"), collects, uses, discloses, and protects your personal information when you use the MOTAC Integrated Resource Management System ("System"). The System facilitates ICT Equipment Loans and internal helpdesk support for official MOTAC staff.

By using the System, you agree to the collection and use of information as described in this policy.

---

## 2. Information We Collect

We collect different types of personal and application-related information to operate and continually improve our System.

### 2.1 Personal Data

We may request the following personally identifiable information, including but not limited to:

- Full name and title (e.g., "Encik", "Puan")
- Identification number (NRIC/MyKad) or passport number
- Official MOTAC and personal email addresses
- Assigned User ID (e.g., network ID)
- Position, grade, department, and level (Aras)
- Mobile telephone number
- Service status (e.g., Permanent, Contract, MySTEP), and appointment type (e.g., New, Promotion/Transfer)
- Previous department/email information (if applicable)
- Profile photo (optional)
- System login password (encrypted)

### 2.2 Application Data

Information specific to your applications, such as:

- ICT Equipment Loan Applications: Purpose, location, return location, loan and return dates, responsible officer details, equipment types and quantities requested, confirmation of terms and conditions, supporting officer details.
- Helpdesk Requests: Ticket content, communications, attachments, status, and workflow actions.

### 2.3 System Usage & Transaction Data

- Approval records (officer details, status, comments, timestamps)
- ICT equipment loan transactions (issuance/return records, checklists, notes)
- Helpdesk interactions (activity, responses, closure)
- Audit trails (created_by, updated_by, notification records)

---

## 3. How We Use Your Information

Your data will be used to:

- Provide, operate, and maintain the System
- Manage user accounts and role-based access
- Process ICT Equipment Loan applications and helpdesk requests
- Facilitate workflow approvals and notifications
- Track ICT equipment inventory and loan status
- Administer and secure the System, maintain audit trails
- Ensure compliance with MOTAC policies and Malaysian law
- Monitor performance and gather feedback for improvement

---

## 4. Data Sharing and Disclosure

Your information may be shared internally within MOTAC with authorized personnel for legitimate purposes, including but not limited to BPM staff, IT administrators, and management.

- No personal data is shared with external third parties for marketing purposes.
- Disclosure may occur only if required by law or to:
    - Comply with a legal obligation
    - Protect MOTAC’s rights or property
    - Prevent or investigate wrongdoing
    - Protect user/public safety

All helpdesk requests and ticket communications are logged and protected as per this policy.

---

## 5. Data Security

We employ robust security measures, including:

- Laravel authentication and security features
- Role-based access control (RBAC)
- Protection against web vulnerabilities (CSRF, input sanitization)
- Secure password hashing and session management
- Custom webhook signature validation

Regular audits and monitoring of system activity are performed. No method of online data transmission or storage is 100% secure, but we strive to protect your information using best practices.

---

## 6. Data Minimization & Purpose Limitation

We collect and process only the minimum data necessary to fulfill MOTAC’s official purposes as required by law and internal policy.

---

## 7. Audit Logging

All user activity within the System, including helpdesk and loan applications, is logged for security, accountability, and compliance purposes. Audit logs are retained in accordance with MOTAC and government ICT policies.

---

## 8. Data Retention

Personal and application data is retained only as long as necessary for the purposes described, for the duration of your employment/service with MOTAC, and as required by Malaysian law and government regulations. Requests for account or data deletion should be directed to BPM and will be handled as per official procedures.

---

## 9. Your Rights

You may access and update your personal information in your user profile, subject to system capabilities. You may request data correction by contacting BPM. Requests for access, correction, or deletion may be subject to approval in line with official records requirements.

---

## 10. Data Breach Notification

In the event of a data breach or suspected incident, BPM will promptly investigate and notify affected users in accordance with Malaysian government regulations and internal MOTAC procedures.

---

## 11. Third-Party Services

Where third-party processors (including government cloud or SaaS) are used, they are subject to equivalent security and legal obligations. No personal data is shared with external parties for marketing.

---

## 12. Legal Compliance

This policy is established in accordance with the Personal Data Protection Act 2010 (PDPA), Official Secrets Act 1972, and Malaysian Public Sector ICT Security Policy (MyMIS).

---

## 13. Enforcement

MOTAC staff who violate this policy are subject to disciplinary action under government service regulations and applicable law.

---

## 14. Changes to This Privacy Policy

This policy may be updated from time to time. Changes will be posted on this page and indicated by the "Last Updated" date. Please review periodically for updates.

---

## 15. Accessibility

This Privacy Policy is available in accessible formats (large print/PDF) upon request to BPM.

---

## 16. Contact Us

If you have questions about this Privacy Policy or your data, please contact:

**Bahagian Pengurusan Maklumat (BPM)**
Ministry of Tourism, Arts and Culture Malaysia
Level 5, No. 2, Tower 1, Jalan P5/6, Precinct 5, 62200 Putrajaya, Malaysia
Tel: 03-8000 8000 (MOTAC HQ main line)
Helpdesk BPM: 03-8891 7663
Fax: 03-8891 7545
Email: [info@motac.gov.my](mailto:info@motac.gov.my)

---
MD
];
