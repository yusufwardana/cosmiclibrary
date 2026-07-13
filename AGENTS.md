# CosmicLib Engine - Agent Instructions

This file contains persistent instruction overrides and rules for AI Coding Agents (such as Gemini, Claude, Cline, or Codex) working on this repository.

---

## 🛑 STRICT COMMANDMENTS

1. **DO NOT WRITE LARAVEL CODE YET**: Do not write, scaffold, or generate Laravel controllers, routes, migrations, views, or models. This is currently a **blueprint and documentation phase**.
2. **DO NOT EXPOSE SECRETS**: Never commit database passwords, API keys, or security salt tokens to git. Use `.env.example` to declare variables.
3. **RESPECT DIRECTORY BOUNDARIES**: Keep documentation in `docs/`, blueprints in `blueprint/`, examples in `examples/`, and coding templates/prompts in `prompts/`.
4. **UI LANGUAGE**: All user interface elements must use **Bahasa Indonesia**. All underlying code (variable names, tables, classes, database schemas) must be written in **English**.

---

## 🎨 CODING & VISUAL IDENTITY RULES

When building or updating the repository viewer React dashboard:
- **Theme**: Use a high-contrast dark visual theme inspired by deep space or modern cyber-minimalism (sleek dark slates, ambient deep-space purples/indigos, and pristine neon teal accents).
- **Typography**: Inter for standard body text, JetBrains Mono for system specs/code, and Space Grotesk/Outfit for Display headings.
- **Animations**: Use clean, physics-based fluid animations (`framer-motion`) to ease transitions between documentation tabs. Keep them responsive and purposeful.
- **Component Design**: Ensure all visual elements are responsive, polished, and have robust interactive hover/active feedback.

---

## 🛠️ BLUEPRINT SYNTAX STANDARDS

When adding or editing schema blueprints in `blueprint/`:
- Use standardized JSON or YAML representations of the MySQL schemas.
- Ensure all primary keys are `bigint unsigned AUTO_INCREMENT` or `uuid` where applicable.
- Specify foreign key relations explicitly.
- Include indices for query speed optimization on high-volume tables (e.g., `borrow_records`, `books`).
