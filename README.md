# VulnHub — Healthcare Security Training Lab

<div align="center">

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat-square&logo=php&logoColor=white)
![Apache](https://img.shields.io/badge/Apache-XAMPP-D22128?style=flat-square&logo=apache)
![License](https://img.shields.io/badge/License-Educational-red?style=flat-square)

**Interactive Healthcare Cybersecurity Training with Real OWASP Vulnerabilities**

[Quick Start](#-quick-start) • [Modules](#-vulnerability-modules) • [Demo Users](#-demo-accounts) • [Safety](#-security-notice)

</div>

---

##  SECURITY NOTICE

```
╔═══════════════════════════════════════════════════════════════╗
║      INTENTIONALLY VULNERABLE — LOCALHOST ONLY                ║
╠═══════════════════════════════════════════════════════════════╣
║  Contains REAL exploitable vulnerabilities for education.    ║
║     NEVER deploy publicly    NEVER use real patient data     ║
║     Localhost only (127.0.0.1)     Educational use only       ║
╚═══════════════════════════════════════════════════════════════╝
```

---

##  What is VulnHub?

VulnHub is a **healthcare-themed cybersecurity training platform** built with pure procedural PHP (no frameworks, no database). It simulates a medical facility's web application with intentional security flaws, providing hands-on experience with **OWASP Top 10** vulnerabilities in realistic clinical workflows.

**Why Healthcare Context?**
- Patient records are 10-50x more valuable than credit cards on dark markets
- Real-world scenarios: doctors, lab techs, pharmacists, billing officers
- HIPAA compliance and privacy implications
- Critical infrastructure security awareness

**Key Features:**
-    **6 User Roles**: Doctor, Patient, Lab Tech, Pharmacist, Billing, Admin
-    **3 OWASP Modules**: A01 (Access Control), A03 (Injection), A05 (Misconfiguration)
-    **12 Total Flags**: 4 difficulty levels per module (LOW → IMPOSSIBLE)
-    **Zero Dependencies**: No database required, runs instantly on XAMPP
-    **Built-in Safety**: Localhost enforcement prevents accidental exposure

VulnHub is designed as a progressively expanding security training platform.
While the current release focuses on the most critical and commonly exploited vulnerabilities, the architecture already supports all OWASP Top 10 categories.

Several modules are actively being built and are intentionally marked as “Coming Soon” in the application sidebar to reflect real development status.

| OWASP ID | Vulnerability Category             | Status         | Notes                                  |
| -------- | ---------------------------------- | -------------- | -------------------------------------- |
| **A01**  | Broken Access Control              |   Implemented  | Fully functional (4 difficulty levels) |
| **A02**  | Cryptographic Failures             |   In Progress | Weak hashing, key misuse, token flaws  |
| **A03**  | Injection                          |   Implemented  | Includes advanced PHP deserialization  |
| **A04**  | Insecure Design                    |   In Progress | Business logic & workflow abuse        |
| **A05**  | Security Misconfiguration          |   Implemented  | Debug leaks, defaults, IDOR            |
| **A06**  | Vulnerable Components              |   Planned    | Dependency trust & version risks       |
| **A07**  | Authentication Failures            |   Planned    | Session, MFA, brute-force logic        |
| **A08**  | Software & Data Integrity          |   Planned    | Deserialization, update trust          |
| **A09**  | Logging & Monitoring Failures      |   Planned    | Silent attacks, alert bypass           |
| **A10**  | Server-Side Request Forgery (SSRF) |   Planned    | Internal service abuse                 |

---

##  Quick Start

### Installation (3 Steps)

```bash
# 1. Clone repository to XAMPP
cd C:\xampp\htdocs\  # Windows
# cd /Applications/XAMPP/htdocs/  # macOS
# cd /opt/lampp/htdocs/  # Linux

git clone https://github.com/PavithraaDeenadayalan/VulnHub.git

# 2. Start Apache in XAMPP Control Panel (no MySQL needed)

# 3. Open browser
http://localhost/VulnHub/
```

**Alternative**: Download ZIP from GitHub → Extract to `htdocs/VulnHub/` → Start Apache

### 🎮 Demo Accounts

| Role | Username | Password | Access |
|------|----------|----------|--------|
| Doctor | `dr_smith` | `password123` | Patients: pat001-003 |
| Patient | `patient_alice` | `password123` | Own records only |
| Lab Tech | `lab_tech` | `password123` | Upload lab results |
| Admin | `admin` | `password123` | Full system access |

**Recommended**: Start with `dr_smith` for the most complete experience.

---

##   Vulnerability Modules

###   A01 — Broken Access Control

**Healthcare Impact**: Unauthorized patient record access (HIPAA violations)

| Level | Vulnerability | Exploit |
|-------|--------------|---------|
|  **LOW** | Missing ownership checks | URL manipulation: `?patient_id=pat004` |
|  **MEDIUM** | Frontend-only auth | Modify cookies/POST data to escalate privileges |
|  **HARD** | Workflow bypass | Skip prescription approval steps |
|  **IMPOSSIBLE** | Secure RBAC | Proper server-side validation (unbreakable) |

**Example Flag**: `FLAG{a01_low_missing_relationship_check}`

---

### 💉 A03 — Injection

**Healthcare Impact**: Data exfiltration, prescription tampering, system compromise

| Level | Vulnerability | Exploit |
|-------|--------------|---------|
|  **LOW** | Array logic injection | Craft search queries to bypass filters |
|  **MEDIUM** | Path traversal (LFI) | `?file=../../../flag.txt` to read secrets |
|  **HARD** | PHP deserialization | Craft malicious objects for code execution |
|  **IMPOSSIBLE** | Whitelist validation | Safe file handling (unbreakable) |

**Example Flag**: `FLAG{a03_medium_path_traversal_lfi}`

---

### ⚙️ A05 — Security Misconfiguration

**Healthcare Impact**: Information disclosure, credential leaks, system takeover

| Level | Vulnerability | Exploit |
|-------|--------------|---------|
|  **LOW** | Debug mode enabled | Trigger errors to leak paths/config |
|  **MEDIUM** | Default credentials | Login as admin with `admin/admin123` |
|  **HARD** | Predictable IDs (IDOR) | Enumerate records: `?record_id=1,2,3...` |
|  **IMPOSSIBLE** | Hardened config | UUIDs, error suppression (unbreakable) |

**Example Flag**: `FLAG{a05_low_debug_mode_info_disclosure}`

---

##  Technical Architecture

```
Technology Stack:
├── Frontend: HTML5, CSS3 (Dark Medical Theme), Vanilla JS
├── Backend: Procedural PHP 7.4+ (No frameworks)
└── Data: Sessions + JSON files (NO database)

Data Storage:
├── $_SESSION → User state, authentication, progress
├── PHP Arrays → In-memory patient/user data
└── data/*.json → Persistent seed data (reports, configs)

Security Features:
├── Localhost enforcement (config/security.php)
├── Progressive difficulty (LOW → IMPOSSIBLE)
├── Flag tracking via sessions
└── Documented vulnerabilities with mitigation guides
```

---

##  Project Structure

```
VulnHub/
├── index.php                      # Landing page
├── dashboard.php                  # Main dashboard
├── EXPLOIT_GUIDE.md               # Detailed walkthroughs
│
├── config/
│   ├── security.php               # Localhost enforcement
│   └── seed_data.php              # Demo users/patients
│
├── auth/
│   ├── login.php                  # Authentication
│   └── session_manager.php        # Session handling
│
├── modules/
│   ├── A01_BrokenAccessControl/   # 4 difficulty levels
│   ├── A03_Injection/             # 4 difficulty levels
│   └── A05_SecurityMisconfiguration/  # 4 difficulty levels
│
├── data/
│   ├── patients.json              # Medical records
│   └── flag.txt                   # Hidden flag for LFI
│
└── assets/
    ├── css/dark-theme.css         # Medical dashboard UI
    └── js/flag_tracker.js         # Real-time flag counter
```

---

##  Learning Objectives

**Offensive Skills:**
-  Broken access control exploitation (IDOR, privilege escalation)
-  Injection attacks (array logic, path traversal, deserialization)
-  Security misconfiguration identification
-  Manual penetration testing methodology

**Defensive Skills:**
-  RBAC implementation patterns
-  Input validation strategies
-  Secure session management
-  Defense-in-depth architecture

**Healthcare Security:**
-  HIPAA privacy implications
-  Electronic Protected Health Information (ePHI) safeguards
-  Clinical workflow security considerations

---

##  Safety & Ethics

### Built-in Protections

```php
// config/security.php - Localhost enforcement
$allowed_hosts = ['localhost', '127.0.0.1', '::1'];
if (!in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
    die('VulnHub is for localhost training only.');
}
```

### Ethical Use Policy

 **Permitted:**
- Educational training on localhost
- Security research in isolated environments
- Course/workshop demonstrations

 **Prohibited:**
- Production deployment
- Public internet exposure
- Testing on systems you don't own
- Use with real patient data

**Legal**: Misuse may violate HIPAA, CFAA, and other statutes. User assumes full responsibility.

---

## 🔧 Troubleshooting

| Issue | Solution |
|-------|----------|
| Page not found | Ensure folder is `VulnHub` (case-sensitive) |
| Apache won't start | Check port 80 isn't used by another service |
| PHP errors | Verify XAMPP PHP version is 7.4+ |
| Blank page | Check Apache error logs in XAMPP |

**Need Help?** Open an issue: [github.com/PavithraaDeenadayalan/VulnHub/issues](https://github.com/PavithraaDeenadayalan/VulnHub/issues)

---

##  Author and Team Members

**Pavithraa Deenadayalan**  
**Mrinalini**  
**Pradeep**  

-  GitHub: [@PavithraaDeenadayalan](https://github.com/PavithraaDeenadayalan)
-  Email: pavithraadeenadayalan35@gmail.com
-  Project: [VulnHub Repository](https://github.com/PavithraaDeenadayalan/VulnHub)

---

##  License & Disclaimer

**License**: Educational use only. Not for production deployment.

**Disclaimer**: This application contains intentional security vulnerabilities for training purposes. The author assumes no liability for misuse. Users are responsible for ethical deployment and compliance with applicable laws.

**Acknowledgments:**
- OWASP Foundation — Security standards
- Healthcare IT Security Community
- PHP Security Documentation

---

##  Contributing

Contributions welcome! To add new vulnerability modules:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/A07-XSS`)
3. Follow the 4-tier difficulty pattern (LOW/MEDIUM/HARD/IMPOSSIBLE)
4. Include exploitation guide in `EXPLOIT_GUIDE.md`
5. Submit pull request

**Ideas for New Modules:**
- A02: Cryptographic Failures
- A04:  Insecure Design  
---

<div align="center">

**⭐ Star this repo if VulnHub helped you learn! ⭐**

Built with ❤️ for cybersecurity education

</div>
