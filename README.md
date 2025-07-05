# SHB – Security Headers Boss ![GPL v2](https://img.shields.io/badge/license-GPLv2-blue.svg) ![WP 5.2+](https://img.shields.io/badge/WP-5.2%2B-brightgreen.svg) ![PHP 7.4+](https://img.shields.io/badge/PHP-7.4%2B-orange.svg)

**One‑click HTTP security headers with beginner‑friendly presets and a 60‑second onboarding wizard – built & maintained by [Arun Ammisetty](https://arunammisetty.dev).**

![SHB banner](assets/banner-772x250.png)

---

## Table of Contents

1. [Why SHB?](#why-shb)
2. [Features](#features)
3. [Screenshots](#screenshots)
4. [Quick Start](#quick-start)
5. [Protection Modes](#protection-modes)
6. [FAQ](#faq)
7. [Contributing](#contributing)
8. [Changelog](#changelog)
9. [License](#license)

---

## Why SHB?

> **Non‑technical site owners deserve bullet‑proof headers without reading an RFC.**

* **Instant setup** – Activate → pick a mode → you’re protected.
* **Traffic‑light dashboard** – Know your header health at a glance.
* **Safety nets** – Undo link & “Safe Mode” constant prevent lock‑outs.
* **Clean code** – PSR‑12, namespaced, documented, unit‑test ready.

---

## Features

| Category             | Details                                                                 |
|----------------------|-------------------------------------------------------------------------|
| **Presets**          | **Standard**, **Strict**, **Paranoid** radio buttons hide the jargon.   |
| **Headers Sent**     | HSTS, CSP, Referrer‑Policy, COOP, COEP, Permissions‑Policy (`interest‑cohort=()` by default). |
| **On‑boarding Wizard** | 60‑second modal guides users through first‑time setup.               |
| **Visual Status Tile** | Green / Yellow / Red tile in Admin shows missing headers count.      |
| **Zero Config**      | No API keys, no htaccess edits, no database schema changes.             |
| **Developer Friendly** | Extensible `SHB_Core` singleton, DocBlocks, WP hooks, POT file for i18n. |

---

## Screenshots

| Wizard                                  | Dashboard                                 |
|----------------------------------------|-------------------------------------------|
| ![Wizard screenshot](docs/img/wizard.png) | ![Tile screenshot](docs/img/dashboard.png) |

---

## Quick Start

```bash
# Inside your WordPress installation
cd wp-content/plugins/
git clone https://github.com/arunammisetty/SHB.git 
# or download & unzip the release

# Activate
wp plugin activate shb        # WP‑CLI
# …or via WP Admin ‑> Plugins
```

1. Navigate to **Settings → Security Headers**.
2. Click **Run 60‑second Wizard**.
3. Choose a **Protection Mode** and press **Apply & Test**. ✔️

---

## Protection Modes

| Mode         | Sent Headers (⊕ = in addition to previous)             | Typical Use‑Case               |
|--------------|--------------------------------------------------------|--------------------------------|
| **Standard** | HSTS, Referrer‑Policy, Permissions‑Policy (cohort off) | Brochure sites, blogs          |
| **Strict**   | ⊕ CSP `default-src 'self'; frame-ancestors 'none'`     | Sites with minimal inline JS   |
| **Paranoid** | ⊕ COOP, COEP, tighter CSP                              | High‑security dashboards, SaaS |

Switch modes any time – SHB updates headers on the next request.

---

## FAQ

<details>
<summary><strong>Will this break my page‑builder / ads / inline scripts?</strong></summary>

*Standard* mode is 100 % compatible.  
*Strict* and *Paranoid* modes add CSP which may block inline assets; whitelist hashes/nonces if needed.

</details>

<details>
<summary><strong>Where are settings stored?</strong></summary>

Single row in `wp_options` → key `shb_settings`.

</details>

<details>
<summary><strong>How do I reset to safe defaults?</strong></summary>

Add `define('SHB_SAFE_MODE', true);` to `wp-config.php` – SHB will revert to **Standard** headers.

</details>

---

## Contributing

1. Fork → feature branch → pull request.
2. Follow **PSR‑12** & **WordPress‑Coding‑Standards** (`composer install && composer run lint`).
3. Add PHPUnit tests in `tests/` when touching core logic.
4. Update `languages/shb.pot` with `wp i18n make-pot` for new strings.

---

## Changelog

See [`CHANGELOG.md`](CHANGELOG.md) for full history.

**1.0.0 – 2025‑07‑05**

* Initial release 🎉

---

## License

GPL‑2.0‑or‑later – see [`LICENSE`](LICENSE).

> © 2025 Arun Ammisetty. SHB is free software: you may redistribute it and/or modify it under the terms of the GPL. Logo & screenshots are released under CC‑BY‑4.0 unless noted otherwise.
