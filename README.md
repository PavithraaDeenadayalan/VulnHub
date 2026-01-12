# MedVulnLab — Interactive Healthcare Security Lab (Intentional Vulns)

**CRITICAL SECURITY NOTICE — LOCALHOST ONLY. DO NOT DEPLOY PUBLICLY.**  
This app is intentionally vulnerable for ethical training. Every flaw is documented and isolated. Use only in a controlled environment.

---

## Snapshot
- Healthcare theme (patients, doctors, lab, billing) with realistic workflows.  
- Multiple roles (Patient, Doctor, Lab Tech, Pharmacist, Billing, Admin).  
- Procedural PHP, Apache/XAMPP, no database (arrays/sessions/JSON only).  
- Dark mode, left sidebar for OWASP Top 10, flag counter, level switcher.  
- Flags per module/level; see `EXPLOIT_GUIDE.md` for step-by-step captures.  
- Localhost enforcement—hard stop if host ≠ `localhost/127.0.0.1`.  

---

## Quickstart (Windows + XAMPP)
1. Copy `MedVulnLab` → `C:\xampp\htdocs\`  
2. Start Apache (PHP 7.4+; no DB needed)  
3. Open `http://localhost/MedVulnLab/`  
4. Login with a demo user:  
   - Doctor: `dr_smith` / `password123` (pat001, pat002, pat003)  
   - Doctor: `dr_jones` / `password123` (pat004, pat005)  
   - Patient: `patient_alice` / `password123`  
   - Lab Tech: `lab_tech` / `password123`  
   - Billing: `billing_officer` / `password123`  
   - Admin: `admin` / `password123`

---

## Modules & Levels (Active: A01, A03, A05)
**A01 Broken Access Control**  
- LOW: Missing relationship/context check (doctor views unassigned patients).  
- MEDIUM: Frontend-only auth; backend trusts UI.  
- HARD: Workflow state bypass (out-of-order actions).  
- IMPOSSIBLE: Token reuse; no session/ownership binding.  

**A03 Injection**  
- LOW: Broad search injection (logic/array “SQLi”).  
- MEDIUM: Path traversal/file access from `data/` via `../`.  
- HARD: PHP object deserialization (user-controlled payload).  
- IMPOSSIBLE: Second-order injection (stored payload executed when viewed).  

**A05 Security Misconfiguration**  
- LOW: Debug mode + verbose errors leaking paths/config.  
- MEDIUM: Default/weak credentials (e.g., `admin/admin123`).  
- HARD: Predictable IDs + missing auth (IDOR on patient records).  
- IMPOSSIBLE: Insecure file permissions & directory listing (backup/secret files).  

Flags are issued per level; total shown in the sidebar.

---

## How to Use
1. Sign in with any role.  
2. Use the sidebar to pick a module and level.  
3. Read the vulnerability and the “design mistake” note.  
4. Exploit the flaw; a flag banner confirms success and updates the counter.  
5. Need payloads? Open `EXPLOIT_GUIDE.md`.  

---

## Project Layout
```
MedVulnLab/
├── index.php                     # Dashboard, sidebar, flag counter
├── config/security.php           # Localhost enforcement, settings
├── auth/                         # Login/logout/session helpers
├── data/                         # Seed data, reports, JSON stores
├── assets/                       # Layout, sidebar, warning banner, flags
├── modules/
│   ├── A01_BrokenAccessControl/          # 4 levels
│   ├── A03_Injection/                    # 4 levels
│   └── A05_SecurityMisconfiguration/     # 4 levels
├── EXPLOIT_GUIDE.md              # Step-by-step flag walkthroughs
└── README.md
```

---

## Safety & Ethics
- Localhost-only by design; blocked elsewhere.  
- Intentional vulnerabilities for education; never use real data.  
- Do not expose to the internet. Use responsibly.  

---

## Troubleshooting
- Not loading? Ensure Apache is running and path is `C:\xampp\htdocs\MedVulnLab\`.  
- Session quirks? Clear cookies or restart Apache.  
- 404s or missing assets? Confirm folder name matches URL.  
- Still stuck? Check Apache/PHP error logs in XAMPP.  

---

## Contributing / Coursework Tips
- Document each vuln: impact, root cause, and a proposed fix.  
- Use flags as proof of exploitation.  
- Keep it framework-free and localhost-only.  
- Add new OWASP categories by reusing the four-level pattern.  

---

## License
Educational use only. Not for production deployment.  
Stay safe, hack responsibly, and enjoy the lab. 🩺🛡️
