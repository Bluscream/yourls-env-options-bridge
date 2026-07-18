# Environment Options Bridge for YOURLS

Dynamically overrides any YOURLS database option via environment variables (e.g. `YOURLS_OPTION_PS_TITLE` or `PS_TITLE` overrides `ps_title`).

## Installation

1. Copy this folder into your YOURLS `user/plugins` directory.
2. Activate the plugin in the YOURLS administration panel.

## Usage

Set environment variables in your container configuration:
- Prefix name: `YOURLS_OPTION_` + uppercase option name (e.g., `YOURLS_OPTION_PS_TITLE="My Shortener"`)
- Direct name: uppercase option name (e.g., `PS_TITLE="My Shortener"`)
