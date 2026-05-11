// ============================================
// MODE SOMBRE/CLAIR - GESTION COMPLÈTE
// ============================================

(function() {
    // Éléments DOM
    const themeToggle = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;
    
    // Clé pour localStorage
    const THEME_KEY = 'innogov_theme';
    
    /**
     * Sauvegarde le thème choisi
     * @param {string} theme - 'light' ou 'dark'
     */
    function saveTheme(theme) {
        localStorage.setItem(THEME_KEY, theme);
        
        // Sauvegarder en base de données si utilisateur connecté
        saveThemeToDatabase(theme);
    }
    
    /**
     * Sauvegarde le thème en BDD via AJAX
     * @param {string} theme 
     */
    function saveThemeToDatabase(theme) {
        // Vérifier si l'utilisateur est connecté
        const userId = getUserIdFromSession();
        
        if (userId) {
            fetch('../CONTROLLER/UtilisateurController.php?action=saveTheme', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `theme=${theme}&user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.warn('Erreur lors de la sauvegarde du thème en BDD');
                }
            })
            .catch(err => console.log('Impossible de sauvegarder le thème:', err));
        }
    }
    
    /**
     * Récupère l'ID utilisateur depuis la session
     * (via un cookie ou une variable JS)
     */
    function getUserIdFromSession() {
        // Option 1: via un cookie
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'user_id') {
                return value;
            }
        }
        
        // Option 2: via un meta tag dans le HTML
        const metaUserId = document.querySelector('meta[name="user-id"]');
        if (metaUserId) {
            return metaUserId.getAttribute('content');
        }
        
        return null;
    }
    
    /**
     * Applique le thème au document
     * @param {string} theme 
     */
    function applyTheme(theme) {
        htmlElement.setAttribute('data-theme', theme);
        
        // Mettre à jour le toggle si présent
        if (themeToggle) {
            themeToggle.checked = (theme === 'dark');
        }
        
        // Changer la classe body pour compatibilité
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
            document.body.classList.remove('light-mode');
        } else {
            document.body.classList.add('light-mode');
            document.body.classList.remove('dark-mode');
        }
        
        console.log(`Thème appliqué : ${theme}`);
    }
    
    /**
     * Initialise le thème au chargement
     */
    function initTheme() {
        // 1. Vérifier si un thème est sauvegardé dans localStorage
        let savedTheme = localStorage.getItem(THEME_KEY);
        
        if (savedTheme) {
            applyTheme(savedTheme);
        } 
        // 2. Sinon, vérifier les préférences système
        else {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const defaultTheme = prefersDark ? 'dark' : 'light';
            applyTheme(defaultTheme);
            saveTheme(defaultTheme);
        }
    }
    
    /**
     * Change le thème
     * @param {string} newTheme 
     */
    function setTheme(newTheme) {
        applyTheme(newTheme);
        saveTheme(newTheme);
        
        // Déclencher un événement personnalisé pour informer les autres scripts
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));
    }
    
    /**
     * Bascule entre les thèmes
     */
    function toggleTheme() {
        const currentTheme = htmlElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
        
        // Animation de transition
        animateThemeTransition();
    }
    
    /**
     * Animation lors du changement de thème
     */
    function animateThemeTransition() {
        // Créer un overlay de transition
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, transparent, var(--bg-primary));
            pointer-events: none;
            z-index: 99999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        document.body.appendChild(overlay);
        
        setTimeout(() => {
            overlay.style.opacity = '0.3';
            setTimeout(() => {
                overlay.style.opacity = '0';
                setTimeout(() => {
                    overlay.remove();
                }, 300);
            }, 100);
        }, 10);
    }
    
    /**
     * Écoute les changements de préférence système
     */
    function watchSystemTheme() {
        const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        
        darkModeMediaQuery.addEventListener('change', (e) => {
            // Ne changer que si l'utilisateur n'a pas de préférence sauvegardée
            const savedTheme = localStorage.getItem(THEME_KEY);
            if (!savedTheme) {
                const newTheme = e.matches ? 'dark' : 'light';
                setTheme(newTheme);
            }
        });
    }
    
    /**
     * Ajoute un bouton flottant si le toggle n'existe pas
     */
    function createFloatingThemeButton() {
        if (!document.getElementById('theme-toggle')) {
            const floatingBtn = document.createElement('div');
            floatingBtn.innerHTML = `
                <div class="theme-switch-wrapper floating">
                    <span class="theme-icon light-icon">☀️</span>
                    <label class="theme-switch">
                        <input type="checkbox" id="theme-toggle-floating">
                        <span class="slider"></span>
                    </label>
                    <span class="theme-icon dark-icon">🌙</span>
                </div>
            `;
            floatingBtn.style.position = 'fixed';
            floatingBtn.style.bottom = '20px';
            floatingBtn.style.right = '20px';
            floatingBtn.style.zIndex = '9999';
            document.body.appendChild(floatingBtn);
            
            const floatingToggle = document.getElementById('theme-toggle-floating');
            if (floatingToggle) {
                floatingToggle.addEventListener('change', toggleTheme);
                floatingToggle.checked = htmlElement.getAttribute('data-theme') === 'dark';
            }
        }
    }
    
    /**
     * Précharge les deux thèmes pour éviter le FOUC
     */
    function preloadThemes() {
        // Cette fonction force le chargement des deux thèmes en mémoire
        const tempDiv = document.createElement('div');
        tempDiv.style.cssText = 'position: absolute; visibility: hidden;';
        tempDiv.setAttribute('data-theme', 'light');
        tempDiv.setAttribute('data-theme', 'dark');
        document.body.appendChild(tempDiv);
        setTimeout(() => tempDiv.remove(), 100);
    }
    
    // ============================================
    // INITIALISATION
    // ============================================
    
    // Attendre que le DOM soit chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initTheme();
            watchSystemTheme();
            preloadThemes();
            
            // Attacher l'événement au toggle existant
            if (themeToggle) {
                themeToggle.addEventListener('change', toggleTheme);
            } else {
                createFloatingThemeButton();
            }
        });
    } else {
        initTheme();
        watchSystemTheme();
        preloadThemes();
        
        if (themeToggle) {
            themeToggle.addEventListener('change', toggleTheme);
        } else {
            createFloatingThemeButton();
        }
    }
    
    // Exposition publique pour debugging (optionnel)
    window.themeManager = {
        setTheme,
        getTheme: () => htmlElement.getAttribute('data-theme'),
        toggleTheme
    };
    
})();