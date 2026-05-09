/**
 * Wikit Semantics Chat - AI Answer Generation
 * Copyright (C) 2026 by the Wikit Development Team.
 */

/**
 * Wikit Semantics Chat Widget Manager
 * Handles the injection and configuration of the Wikit Semantics Chat widget
 */
class WikitSemanticsChatWidget {
    constructor() {
        this.chatApi = null;
        this.config = null;
        this.scriptLoaded = false;
        this.initialized = false;
    }

    /**
     * Initialize the widget with configuration from GLPI
     */
    async init() {
        if (this.initialized) {
            return;
        }

        this.initialized = true;

        try {
            // Fetch configuration from GLPI backend
            const response = await fetch(
                CFG_GLPI.root_doc + '/plugins/wikitsemanticschat/ajax/config.php',
                {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                }
            );

            if (!response.ok) {
                console.warn('Wikit Semantics Chat: Failed to fetch configuration');
                return;
            }

            const data = await response.json();

            if (!data.success || !data.config) {
                console.warn('Wikit Semantics Chat: Plugin not configured');
                return;
            }

            this.config = data.config;
            this.userInfo = data.userInfo || {};

            // Load the Semantics Chat script
            await this.loadScript(data.scriptUrl);

            // Initialize the chat widget
            this.initWidget();
        } catch (error) {
            console.error('Wikit Semantics Chat: Initialization error', error);
        }
    }

    /**
     * Load the Semantics Chat embed script
     * @param {string} scriptUrl - URL of the chat embed script
     * @returns {Promise}
     */
    loadScript(scriptUrl) {
        return new Promise((resolve, reject) => {
            if (this.scriptLoaded) {
                resolve();
                return;
            }

            const existingScript = document.querySelector('script[data-wikit-semantics-chat="true"]');
            if (existingScript) {
                existingScript.addEventListener('load', () => {
                    this.scriptLoaded = true;
                    resolve();
                }, { once: true });
                existingScript.addEventListener('error', () => {
                    reject(new Error('Failed to load Wikit Semantics Chat script'));
                }, { once: true });

                if (typeof wrapSemanticsChat === 'function') {
                    this.scriptLoaded = true;
                    resolve();
                }

                return;
            }

            const script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = scriptUrl;
            script.async = true;
            script.dataset.wikitSemanticsChat = 'true';

            script.onload = () => {
                this.scriptLoaded = true;
                resolve();
            };

            script.onerror = () => {
                reject(new Error('Failed to load Wikit Semantics Chat script'));
            };

            document.head.appendChild(script);
        });
    }

    /**
     * Initialize the chat widget with configuration
     */
    initWidget() {
        if (typeof wrapSemanticsChat !== 'function') {
            console.error('Wikit Semantics Chat: wrapSemanticsChat function not found');
            return;
        }

        // Build widget configuration
        const widgetConfig = {
            ...this.config,
            // Add custom params with user information
            customParams: {
                userLogin: this.userInfo.login || '',
                firstName: this.userInfo.firstname || '',
                lastName: this.userInfo.realname || '',
                userId: this.userInfo.id || '',
                language: this.userInfo.language || 'fr_FR',
                currentURL: window.location.href,
            },
        };

        // Initialize the chat widget
        this.chatApi = wrapSemanticsChat(widgetConfig);

        console.log('Wikit Semantics Chat: Widget initialized successfully');
    }

    /**
     * Update custom params dynamically
     * @param {Object} params - New custom parameters
     */
    setCustomParams(params) {
        if (this.chatApi && typeof this.chatApi.setCustomParams === 'function') {
            this.chatApi.setCustomParams(params);
        }
    }

    /**
     * Open the chat widget
     */
    open() {
        if (this.chatApi && typeof this.chatApi.open === 'function') {
            this.chatApi.open();
        }
    }

    /**
     * Close the chat widget
     */
    close() {
        if (this.chatApi && typeof this.chatApi.close === 'function') {
            this.chatApi.close();
        }
    }
}

function isWikitSemanticsChatMainFrame() {
    try {
        return window.self === window.top;
    } catch (error) {
        return false;
    }
}

function initWikitSemanticsChatWidget() {
    // Check if we're in GLPI context and user is logged in
    if (typeof CFG_GLPI === 'undefined') {
        console.warn('Wikit Semantics Chat: Not in GLPI context');
        return;
    }

    if (!isWikitSemanticsChatMainFrame()) {
        return;
    }

    if (window.__WikitSemanticsChatWidgetInstance) {
        window.WikitSemanticsChatWidget = window.__WikitSemanticsChatWidgetInstance;
        window.WikitSemanticsChatWidget.init();
        return;
    }

    // Create global instance
    window.WikitSemanticsChatWidget = new WikitSemanticsChatWidget();
    window.__WikitSemanticsChatWidgetInstance = window.WikitSemanticsChatWidget;

    // Initialize
    window.WikitSemanticsChatWidget.init();
}

// Initialize the widget when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWikitSemanticsChatWidget, { once: true });
} else {
    initWikitSemanticsChatWidget();
}
