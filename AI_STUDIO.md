# AI Studio Developer Guidelines: CosmicLib Engine

This file defines the development behavior for developers and agents working inside **Google AI Studio**.

## Workspace Capabilities
- **Platform Ingress**: Our development server is locked to port `3000` with host `0.0.0.0` inside a Cloud Run container.
- **Environment Handling**: Secret keys (such as `GEMINI_API_KEY`) must never be hardcoded. They should be loaded server-side or referenced dynamically.
- **Iframe Sandboxing**: Avoid APIs that break in iframes (e.g., `window.alert`, `window.open`). Render modern web overlays/dialogs instead.

## How to Iterate on Documentation
- Do not make random changes to files. Always verify that they exist and review their headings.
- Create beautiful, descriptive interactive UI layers in React (in `src/`) to let users browse and view these documents directly in their browser preview.
