# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | ✅ Active Support  |
| < 1.0   | ❌ Not Supported   |

## Reporting a Vulnerability

The CosmicLib Engine team takes security seriously. We appreciate your efforts to responsibly disclose your findings.

### How to Report

**DO NOT** report security vulnerabilities through public GitHub issues.

Instead, please report security vulnerabilities by emailing:

📧 **security@cosmiclib.dev** *(placeholder — replace with actual email)*

### What to Include

Please include the following details in your report:

- Description of the vulnerability
- Steps to reproduce the issue
- Potential impact
- Suggested fix (if any)

### Response Timeline

- **Acknowledgment**: Within 48 hours
- **Initial Assessment**: Within 5 business days
- **Fix & Release**: Within 30 days for critical issues

### Disclosure Policy

- We follow **Coordinated Disclosure** — please allow us reasonable time to fix the issue before public disclosure.
- Credit will be given to reporters in the release notes (unless anonymity is requested).

## Security Best Practices

When contributing to CosmicLib Engine, please follow the security guidelines documented in [`docs/22_SECURITY_GUIDELINE.md`](../docs/22_SECURITY_GUIDELINE.md).

### Key Points

- Never commit secrets, API keys, or passwords to the repository
- Use `.env.example` for environment variable templates
- Always use parameterized queries (no raw SQL without binding)
- Validate and sanitize all user input
- Use CSRF protection on all forms
- Escape all output in Blade templates using `{{ }}`
