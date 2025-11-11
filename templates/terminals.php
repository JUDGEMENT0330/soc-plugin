<?php
/**
 * Template: Terminales Interactivas
 * Red Team & Blue Team Terminals
 */

if (!defined('ABSPATH')) {
    exit;
}

$workshop_id = isset($atts['workshop_id']) ? intval($atts['workshop_id']) : 0;
$mode = isset($atts['mode']) ? sanitize_text_field($atts['mode']) : 'both';
?>

<div class="cv-soc-terminals-section">
    <div class="cv-soc-terminals-header">
        <h2>Terminales Interactivas</h2>
        <p>Practica comandos y técnicas en un entorno seguro</p>
    </div>
    
    <div class="cv-soc-terminals-wrapper <?php echo $mode === 'both' ? '' : 'single'; ?>">
        
        <?php if ($mode === 'redteam' || $mode === 'both'): ?>
        <!-- Terminal Red Team -->
        <div class="cv-soc-terminal redteam" 
             data-terminal-type="redteam" 
             data-workshop-id="<?php echo esc_attr($workshop_id); ?>">
            
            <!-- Header -->
            <div class="cv-soc-terminal-header">
                <div class="cv-soc-terminal-title">
                    <svg class="cv-soc-terminal-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L1 12h3v9h16v-9h3L12 2zm0 2.5L19 11v8h-3v-6H8v6H5v-8l7-6.5z"/>
                    </svg>
                    <div>
                        <h3>Red Team Terminal</h3>
                        <span class="terminal-team-badge">Ofensivo</span>
                    </div>
                </div>
                <div class="cv-soc-terminal-controls">
                    <button class="cv-soc-terminal-btn terminal-help-btn" title="Ayuda">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11 18h2v-2h-2v2zm1-16C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5 0-2.21-1.79-4-4-4z"/>
                        </svg>
                        Ayuda
                    </button>
                    <button class="cv-soc-terminal-btn terminal-clear-btn" title="Limpiar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                        Limpiar
                    </button>
                    <button class="cv-soc-terminal-btn terminal-reset-btn" title="Reiniciar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/>
                        </svg>
                        Reiniciar
                    </button>
                </div>
            </div>
            
            <!-- Tabs (opcional) -->
            <div class="cv-soc-terminal-tabs">
                <button class="cv-soc-terminal-tab active" data-tab="redteam-console">
                    Console
                </button>
                <button class="cv-soc-terminal-tab" data-tab="redteam-history">
                    Historial
                </button>
                <button class="cv-soc-terminal-tab" data-tab="redteam-tasks">
                    Tareas
                </button>
            </div>
            
            <!-- Body -->
            <div class="cv-soc-terminal-body">
                <div class="cv-soc-terminal-screen"></div>
                
                <div class="cv-soc-terminal-input-wrapper">
                    <div class="cv-soc-terminal-prompt">
                        <span class="prompt-user">redteam</span>
                        <span class="prompt-at">@</span>
                        <span class="prompt-host">valtorix</span>
                        <span class="prompt-separator">:</span>
                        <span class="prompt-path">~</span>
                        <span class="prompt-symbol">$</span>
                    </div>
                    <input type="text" 
                           class="cv-soc-terminal-input" 
                           placeholder="Escribe un comando..." 
                           autocomplete="off" 
                           spellcheck="false">
                </div>
            </div>
            
            <!-- Footer -->
            <div class="cv-soc-terminal-footer">
                <div class="cv-soc-terminal-stats">
                    <div class="cv-soc-terminal-stat">
                        <span>Comandos:</span>
                        <span class="cv-soc-terminal-stat-value">0</span>
                    </div>
                    <div class="cv-soc-terminal-stat">
                        <span>Éxitos:</span>
                        <span class="cv-soc-terminal-stat-value">0</span>
                    </div>
                </div>
                <div class="cv-soc-terminal-info">
                    <span>Presiona Tab para autocompletar</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($mode === 'blueteam' || $mode === 'both'): ?>
        <!-- Terminal Blue Team -->
        <div class="cv-soc-terminal blueteam" 
             data-terminal-type="blueteam" 
             data-workshop-id="<?php echo esc_attr($workshop_id); ?>">
            
            <!-- Header -->
            <div class="cv-soc-terminal-header">
                <div class="cv-soc-terminal-title">
                    <svg class="cv-soc-terminal-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                    </svg>
                    <div>
                        <h3>Blue Team Terminal</h3>
                        <span class="terminal-team-badge">Defensivo</span>
                    </div>
                </div>
                <div class="cv-soc-terminal-controls">
                    <button class="cv-soc-terminal-btn terminal-help-btn" title="Ayuda">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11 18h2v-2h-2v2zm1-16C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5 0-2.21-1.79-4-4-4z"/>
                        </svg>
                        Ayuda
                    </button>
                    <button class="cv-soc-terminal-btn terminal-clear-btn" title="Limpiar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                        Limpiar
                    </button>
                    <button class="cv-soc-terminal-btn terminal-reset-btn" title="Reiniciar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/>
                        </svg>
                        Reiniciar
                    </button>
                </div>
            </div>
            
            <!-- Tabs (opcional) -->
            <div class="cv-soc-terminal-tabs">
                <button class="cv-soc-terminal-tab active" data-tab="blueteam-console">
                    Console
                </button>
                <button class="cv-soc-terminal-tab" data-tab="blueteam-history">
                    Historial
                </button>
                <button class="cv-soc-terminal-tab" data-tab="blueteam-tasks">
                    Tareas
                </button>
            </div>
            
            <!-- Body -->
            <div class="cv-soc-terminal-body">
                <div class="cv-soc-terminal-screen"></div>
                
                <div class="cv-soc-terminal-input-wrapper">
                    <div class="cv-soc-terminal-prompt">
                        <span class="prompt-user">blueteam</span>
                        <span class="prompt-at">@</span>
                        <span class="prompt-host">valtorix</span>
                        <span class="prompt-separator">:</span>
                        <span class="prompt-path">~</span>
                        <span class="prompt-symbol">$</span>
                    </div>
                    <input type="text" 
                           class="cv-soc-terminal-input" 
                           placeholder="Escribe un comando..." 
                           autocomplete="off" 
                           spellcheck="false">
                </div>
            </div>
            
            <!-- Footer -->
            <div class="cv-soc-terminal-footer">
                <div class="cv-soc-terminal-stats">
                    <div class="cv-soc-terminal-stat">
                        <span>Comandos:</span>
                        <span class="cv-soc-terminal-stat-value">0</span>
                    </div>
                    <div class="cv-soc-terminal-stat">
                        <span>Bloqueos:</span>
                        <span class="cv-soc-terminal-stat-value">0</span>
                    </div>
                </div>
                <div class="cv-soc-terminal-info">
                    <span>Presiona Tab para autocompletar</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
    
    <!-- Panel de interacciones -->
    <?php if ($mode === 'both'): ?>
    <div class="cv-soc-interactions-panel">
        <h3>Log de Interacciones Red Team ↔️ Blue Team</h3>
        <div id="cv-soc-interactions-log" class="cv-soc-interactions-log">
            <p class="cv-soc-text-muted">Las interacciones entre equipos aparecerán aquí...</p>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
/* Estilos adicionales específicos del template */
.cv-soc-terminals-section {
    padding: 2rem 0;
}

.cv-soc-terminals-header {
    text-align: center;
    margin-bottom: 2rem;
}

.cv-soc-terminals-header h2 {
    font-size: 2rem;
    color: var(--cv-gold);
    margin-bottom: 0.5rem;
}

.cv-soc-terminals-header p {
    color: var(--text-secondary);
    font-size: 1.125rem;
}

.cv-soc-interactions-panel {
    margin-top: 2rem;
    background: var(--bg-card);
    border: 1px solid rgba(184, 134, 11, 0.2);
    border-radius: 16px;
    padding: 1.5rem;
}

.cv-soc-interactions-panel h3 {
    font-size: 1.25rem;
    color: var(--cv-gold);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.cv-soc-interactions-log {
    max-height: 300px;
    overflow-y: auto;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 8px;
}

.cv-soc-interaction-entry {
    padding: 0.75rem;
    margin: 0.5rem 0;
    background: rgba(184, 134, 11, 0.1);
    border-left: 3px solid var(--cv-gold);
    border-radius: 4px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.cv-soc-interaction-timestamp {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-bottom: 0.25rem;
}

.cv-soc-interaction-message {
    color: var(--text-primary);
}

.terminal-success-pulse {
    animation: successPulse 1s ease;
}

@keyframes successPulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    50% {
        box-shadow: 0 0 0 15px rgba(16, 185, 129, 0);
    }
}

/* Notificaciones */
#cv-soc-notifications {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    max-width: 400px;
}

.cv-soc-notification {
    background: var(--bg-tertiary);
    border: 1px solid var(--cv-gold);
    border-radius: 8px;
    padding: 1rem 1.5rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
    box-shadow: var(--shadow-lg);
}

.cv-soc-notification.active {
    opacity: 1;
    transform: translateX(0);
}

.cv-soc-notification-info {
    border-color: var(--info);
}

.cv-soc-notification-success {
    border-color: var(--success);
}

.cv-soc-notification-warning {
    border-color: var(--warning);
}

.cv-soc-notification-error {
    border-color: var(--error);
}

.cv-soc-notification-message {
    flex: 1;
    color: var(--text-primary);
}

.cv-soc-notification-close {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    margin-left: 1rem;
    transition: var(--transition-fast);
}

.cv-soc-notification-close:hover {
    color: var(--text-primary);
}
</style>
