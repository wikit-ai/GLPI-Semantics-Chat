# Wikit Semantics Chat Plugin for GLPI

## Overview

The **Wikit Semantics Chat** plugin brings an AI-powered conversational assistant directly into your GLPI interface. Accessible on every page, this intelligent chatbot provides instant support to help your teams answer questions and navigate their daily tasks efficiently.

## What Does It Do?

This plugin enables users to interact with an AI assistant that leverages your organization's knowledge base and business processes. Instead of searching through documentation or waiting for support, users can simply ask questions in natural language and get immediate, contextual responses.

### Key Benefits

- **24/7 Availability**: Always-on AI assistant accessible from any GLPI page
- **Instant Support**: Get immediate answers without leaving your workflow
- **Reduced Workload**: Decrease support requests by empowering users with self-service AI assistance
- **Accelerated Onboarding**: Help new users get up to speed faster with intelligent guidance
- **Seamless Integration**: Works directly within your GLPI interface with customizable appearance

## Features

- Direct integration with Wikit Semantics Chat platform
- Secure API authentication with organization-level controls
- Easy configuration through GLPI's admin interface
- Customizable widget appearance (color, position, icon, dimensions)
- Contextual intelligence leveraging your knowledge base and business processes
- Natural language conversation interface
- Support for GLPI 10.0+ 11.0+

## Requirements

### Version Compatibility

| Plugin Version | GLPI Version | PHP Version | Status |
|---------------|--------------|-------------|--------|
| 1.x.x | 10.0.0+ | 7.4+ | Maintenance only |
| 2.x.x | 11.0.0+ | 8.2+ | Active development |

### Prerequisites

- Active Wikit Semantics Chat subscription

## Installation

### Via GLPI Marketplace (Recommended)

1. Go to **Setup > Plugins** in GLPI
2. Search for "Wikit Semantics Chat"
3. Click **Install** then **Activate**
4. Enter your Wikit Semantics Chat credentials (Organization Slug, LLM App Slug)
5. Customize the widget appearance (color, position, icon)
6. Save and start using your AI assistant on all GLPI pages

### Manual Installation

1. Download release 1.0.0 from [GitHub Releases](https://github.com/wikitai/GLPI-Semantics-Chat/releases/tag/1.0.0)
2. Extract the archive to your GLPI plugins directory:
   ```bash
   cd /path/to/glpi/plugins
   tar -xjf wikitsemanticschat-2.0.0.tar.bz2
   ```
3. Go to **Setup > Plugins** in GLPI
4. Find "Wikit Semantics Chat" in the list
5. Click **Install** then **Activate**
6. Enter your Wikit Semantics Chat credentials (Organization Slug, LLM App Slug)
7. Customize the widget appearance (color, position, icon)
8. Save and start using your AI assistant on all GLPI pages

## Usage

Once configured and activated, the Wikit Semantics Chat widget will appear automatically on all GLPI pages for authenticated users.

### How It Works

1. **Access**: A floating chat button appears on every GLPI page
2. **Interact**: Click the button to open the chat interface
3. **Ask**: Type your questions in natural language
4. **Get Answers**: Receive instant, contextual responses based on your knowledge base
5. **Continue Working**: Get help without leaving your current page

### Features for End Users

- **Natural Language Interface**: Ask questions as you would to a colleague
- **Contextual Intelligence**: Leverages your organization's knowledge base and business processes
- **Instant Responses**: Get immediate answers without waiting for support
- **Always Available**: 24/7 assistance accessible from any GLPI page
- **Seamless Integration**: No context switching - help is always at your fingertips

### Permissions

The plugin uses GLPI's permission system:
- **Read**: View the plugin configuration
- **Update**: Modify the plugin configuration

Configure permissions in **Administration > Profiles > Wikit Semantics Chat** tab.

## About Wikit Semantics Chat

Wikit Semantics Chat provides enterprise-grade AI conversational assistants for intelligent support and knowledge management. Visit [Wikit](https://wikit.ai) for more information.

## Troubleshooting

### Chat widget doesn't appear

1. Check that the plugin is **activated** in Setup > Plugins
2. Verify that your user is **logged in**
3. Check that the **organization slug** is configured
4. Look for JavaScript errors in browser console
5. Clear browser cache and reload the page

### Configuration not saving

1. Verify you have **Update** permission for the plugin
2. Check GLPI error logs in `files/_log/`
3. Ensure database connection is working

### Widget loads but doesn't work

1. Verify your organization slug is correct
2. Check that `https://semantics-chat.wikit.ai` is accessible
3. Look for errors in browser console
4. Contact Wikit Semantics Chat support for organization issues

## Contributing

* Open a ticket for each bug/feature so it can be discussed
* Work on a new branch on your own fork
* Open a PR that will be reviewed by a developer

## License

This plugin is licensed under the **Apache License 2.0**.

See [LICENSE](LICENSE) file for details.

## Support

For issues, questions, or contributions, please visit our [GitHub repository](https://github.com/wikitai/GLPI-Semantics-Chat).

- 🐛 **Report issues**: [GitHub Issues](https://github.com/wikitai/GLPI-Semantics-Chat/issues)
- 💬 **Discussions**: [GitHub Discussions](https://github.com/wikitai/GLPI-Semantics-Chat/discussions)
- 🌐 **Wikit Website**: [wikit.ai](https://www.wikit.ai)

---

**Copyright**: © 2026 Wikit
