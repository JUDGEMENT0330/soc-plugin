/**
 * Terminal.js - Sistema de terminales interactivas
 * Red Team & Blue Team Terminal System
 * Version: 3.0.0
 */

(function($) {
    'use strict';

    /**
     * Clase principal de Terminal
     */
    class CVSOCTerminal {
        constructor(element, options = {}) {
            this.element = $(element);
            this.options = $.extend({
                type: 'general', // 'redteam', 'blueteam', 'general'
                workshopId: null,
                enableAI: true,
                enableHistory: true,
                maxHistorySize: 100,
                enableAutocomplete: true,
                enableTeamInteraction: true,
                otherTerminal: null, // Referencia a la otra terminal para interacciones
            }, options);

            this.history = [];
            this.historyIndex = -1;
            this.commandQueue = [];
            this.isProcessing = false;
            this.currentPath = '~';
            this.environment = {};
            
            this.init();
        }

        init() {
            this.screen = this.element.find('.cv-soc-terminal-screen');
            this.input = this.element.find('.cv-soc-terminal-input');
            this.prompt = this.element.find('.cv-soc-terminal-prompt');
            
            this.setupEventListeners();
            this.loadHistory();
            this.displayWelcome();
            this.focus();
        }

        setupEventListeners() {
            // Manejo de entrada de comandos
            this.input.on('keydown', this.handleKeyDown.bind(this));
            this.input.on('keyup', this.handleKeyUp.bind(this));
            
            // Focus automático al hacer click en la terminal
            this.element.on('click', () => this.focus());
            
            // Botones de control
            this.element.find('.terminal-clear-btn').on('click', () => this.clear());
            this.element.find('.terminal-reset-btn').on('click', () => this.reset());
            this.element.find('.terminal-help-btn').on('click', () => this.showHelp());
        }

        handleKeyDown(e) {
            switch(e.key) {
                case 'Enter':
                    e.preventDefault();
                    this.executeCommand();
                    break;
                    
                case 'ArrowUp':
                    e.preventDefault();
                    this.navigateHistory('up');
                    break;
                    
                case 'ArrowDown':
                    e.preventDefault();
                    this.navigateHistory('down');
                    break;
                    
                case 'Tab':
                    e.preventDefault();
                    if (this.options.enableAutocomplete) {
                        this.autocomplete();
                    }
                    break;
                    
                case 'l':
                    if (e.ctrlKey) {
                        e.preventDefault();
                        this.clear();
                    }
                    break;
            }
        }

        handleKeyUp(e) {
            // Aquí se podría implementar autocompletado en tiempo real
        }

        displayWelcome() {
            const teamName = this.options.type === 'redteam' ? 'Red Team' :
                           this.options.type === 'blueteam' ? 'Blue Team' : 'SOC';
            
            const welcomeMessage = `
╔═══════════════════════════════════════════════════════════╗
║   Cyber Valtorix SOC Training Platform - ${teamName}   ║
╚═══════════════════════════════════════════════════════════╝

Bienvenido al entorno de entrenamiento ${teamName}.
Escribe 'help' para ver los comandos disponibles.
Escribe 'tutorial' para comenzar un tutorial guiado.
Escribe 'ai ayuda' para obtener asistencia de IA.

${ this.options.type === 'redteam' ? '⚔️  Modo Ofensivo Activado' : '🛡️  Modo Defensivo Activado' }
`;
            
            this.addLine(welcomeMessage, 'cv-soc-terminal-welcome');
            this.addLine('');
        }

        executeCommand() {
            const command = this.input.val().trim();
            
            if (!command) {
                return;
            }
            
            // Mostrar comando en pantalla
            this.displayCommand(command);
            
            // Limpiar input
            this.input.val('');
            
            // Agregar al historial
            this.addToHistory(command);
            
            // Procesar comando
            this.processCommand(command);
        }

        displayCommand(command) {
            const promptLine = $('<div class="cv-soc-terminal-line cv-soc-terminal-prompt-line"></div>');
            const promptClone = this.prompt.clone();
            const commandSpan = $('<span class="cv-soc-terminal-command"></span>').text(command);
            
            promptLine.append(promptClone).append(commandSpan);
            this.screen.append(promptLine);
            this.scrollToBottom();
        }

        processCommand(command) {
            this.isProcessing = true;
            
            // Comandos locales (no requieren servidor)
            if (this.handleLocalCommand(command)) {
                this.isProcessing = false;
                return;
            }
            
            // Comandos que requieren procesamiento en servidor
            this.executeRemoteCommand(command);
        }

        handleLocalCommand(command) {
            const parts = command.toLowerCase().trim().split(' ');
            const cmd = parts[0];
            
            switch(cmd) {
                case 'clear':
                case 'cls':
                    this.clear();
                    return true;
                    
                case 'help':
                    this.showHelp();
                    return true;
                    
                case 'history':
                    this.showHistory();
                    return true;
                    
                case 'tutorial':
                    this.startTutorial();
                    return true;
                    
                case 'reset':
                    this.reset();
                    return true;
                    
                case 'whoami':
                    this.addLine(cvSocData.currentUser ? `User ID: ${cvSocData.currentUser}` : 'guest');
                    return true;
                    
                case 'pwd':
                    this.addLine(this.currentPath);
                    return true;
                    
                case 'echo':
                    this.addLine(parts.slice(1).join(' '));
                    return true;
                    
                default:
                    return false;
            }
        }

        executeRemoteCommand(command) {
            this.showLoading();
            
            $.ajax({
                url: cvSocData.restUrl + 'execute-command',
                method: 'POST',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', cvSocData.nonce);
                },
                data: JSON.stringify({
                    command: command,
                    terminal_type: this.options.type,
                    workshop_id: this.options.workshopId || 0,
                    task_id: this.getCurrentTaskId(),
                }),
                contentType: 'application/json',
                success: (response) => {
                    this.handleCommandResponse(command, response);
                },
                error: (xhr, status, error) => {
                    this.addLine(`Error ejecutando comando: ${error}`, 'cv-soc-terminal-error');
                },
                complete: () => {
                    this.hideLoading();
                    this.isProcessing = false;
                }
            });
        }

        handleCommandResponse(command, response) {
            if (response.output) {
                this.addLine(response.output, response.success ? '' : 'cv-soc-terminal-error');
            }
            
            if (response.task_validation !== undefined) {
                if (response.task_validation) {
                    this.addLine('✓ Tarea completada correctamente!', 'cv-soc-terminal-success');
                    this.updateProgress();
                    this.playSuccessAnimation();
                } else {
                    this.addLine('✗ Tarea no completada. Intenta de nuevo.', 'cv-soc-terminal-warning');
                    this.requestHint();
                }
            }
            
            // Notificar a la otra terminal si hay interacción
            if (this.options.enableTeamInteraction && this.options.otherTerminal) {
                this.notifyOtherTerminal(command, response);
            }
        }

        notifyOtherTerminal(command, response) {
            // Determinar si el comando genera una interacción visible para el otro equipo
            const interactionCommands = ['nmap', 'scan', 'exploit', 'attack', 'block', 'defend', 'firewall'];
            const commandLower = command.toLowerCase();
            
            const hasInteraction = interactionCommands.some(cmd => commandLower.includes(cmd));
            
            if (hasInteraction && this.options.otherTerminal) {
                const interactionType = this.options.type === 'redteam' ? 'attack' : 'defense';
                const message = this.generateInteractionMessage(command, interactionType);
                
                this.options.otherTerminal.receiveInteraction({
                    type: interactionType,
                    source: this.options.type,
                    command: command,
                    message: message,
                    severity: this.assessSeverity(command)
                });
                
                // Registrar la interacción en el servidor
                this.logTeamInteraction(command, response);
            }
        }

        receiveInteraction(interaction) {
            const interactionDiv = $('<div class="cv-soc-team-interaction"></div>');
            
            const header = $(`
                <div class="cv-soc-team-interaction-header">
                    <span class="interaction-${interaction.source}">
                        ${interaction.source === 'redteam' ? '⚔️ Red Team' : '🛡️ Blue Team'} 
                        - ${interaction.type.toUpperCase()}
                    </span>
                    <span class="interaction-severity severity-${interaction.severity}">
                        ${interaction.severity.toUpperCase()}
                    </span>
                </div>
            `);
            
            const message = $('<div class="cv-soc-team-interaction-message"></div>').text(interaction.message);
            
            interactionDiv.append(header).append(message);
            this.screen.append(interactionDiv);
            this.scrollToBottom();
            
            // Notificación visual
            this.element.addClass('terminal-active');
            setTimeout(() => this.element.removeClass('terminal-active'), 2000);
            
            // Sonido opcional
            this.playNotificationSound();
        }

        generateInteractionMessage(command, type) {
            const messages = {
                attack: {
                    nmap: 'Se detectó un escaneo de puertos en la red',
                    scan: 'Actividad de reconocimiento detectada',
                    exploit: 'Intento de explotación detectado',
                    attack: 'Ataque en progreso',
                },
                defense: {
                    block: 'Tráfico bloqueado por firewall',
                    defend: 'Medidas defensivas activadas',
                    firewall: 'Reglas de firewall actualizadas',
                    monitor: 'Monitoreo activo en curso',
                }
            };
            
            for (const [keyword, message] of Object.entries(messages[type])) {
                if (command.toLowerCase().includes(keyword)) {
                    return message;
                }
            }
            
            return `Actividad ${type} detectada`;
        }

        assessSeverity(command) {
            const commandLower = command.toLowerCase();
            
            if (commandLower.includes('exploit') || commandLower.includes('attack') || commandLower.includes('shell')) {
                return 'high';
            } else if (commandLower.includes('scan') || commandLower.includes('enum')) {
                return 'medium';
            } else {
                return 'low';
            }
        }

        logTeamInteraction(command, response) {
            $.ajax({
                url: cvSocData.restUrl + 'team-interaction',
                method: 'POST',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', cvSocData.nonce);
                },
                data: JSON.stringify({
                    workshop_id: this.options.workshopId || 0,
                    interaction_type: this.options.type === 'redteam' ? 'attack' : 'defense',
                    redteam_user_id: this.options.type === 'redteam' ? cvSocData.currentUser : null,
                    blueteam_user_id: this.options.type === 'blueteam' ? cvSocData.currentUser : null,
                    redteam_action: this.options.type === 'redteam' ? command : null,
                    blueteam_response: this.options.type === 'blueteam' ? command : null,
                    severity: this.assessSeverity(command),
                    details: response,
                }),
                contentType: 'application/json'
            });
        }

        showHelp() {
            const helpText = `
Comandos disponibles:

COMANDOS BÁSICOS:
  help              - Muestra esta ayuda
  clear / cls       - Limpia la pantalla
  history           - Muestra historial de comandos
  tutorial          - Inicia tutorial guiado
  reset             - Reinicia la terminal
  whoami            - Muestra tu usuario
  pwd               - Muestra directorio actual
  echo [texto]      - Imprime texto

${this.options.type === 'redteam' ? `
COMANDOS RED TEAM:
  nmap [target]     - Escaneo de puertos
  exploit [target]  - Ejecuta exploit
  crack [file]      - Crackeo de contraseñas
  backdoor [target] - Instala backdoor
  phishing [email]  - Campaña de phishing
` : ''}

${this.options.type === 'blueteam' ? `
COMANDOS BLUE TEAM:
  monitor           - Monitorea actividad
  block [ip]        - Bloquea dirección IP
  firewall [rule]   - Configura firewall
  analyze [log]     - Analiza logs
  patch [system]    - Aplica parches
` : ''}

COMANDOS IA:
  ai ayuda          - Obtiene ayuda de IA
  ai hint           - Solicita una pista
  ai explain [cmd]  - Explica un comando

ATAJOS:
  Ctrl+L            - Limpia la pantalla
  Flecha Arriba     - Comando anterior
  Tab               - Autocompletar
`;
            
            this.addLine(helpText, 'cv-soc-terminal-info');
        }

        showHistory() {
            if (this.history.length === 0) {
                this.addLine('No hay comandos en el historial');
                return;
            }
            
            this.addLine('Historial de comandos:', 'cv-soc-terminal-info');
            this.history.forEach((cmd, index) => {
                this.addLine(`  ${index + 1}. ${cmd}`);
            });
        }

        startTutorial() {
            this.addLine('Iniciando tutorial...', 'cv-soc-terminal-info');
            // Aquí se implementaría la lógica del tutorial
            // Por ahora, solo mostramos un mensaje
            setTimeout(() => {
                this.addLine('Tutorial próximamente disponible.');
            }, 1000);
        }

        requestHint() {
            if (!this.options.enableAI) {
                return;
            }
            
            this.addLine('💡 ¿Necesitas ayuda? Escribe "ai hint" para obtener una pista.', 'cv-soc-terminal-hint');
        }

        autocomplete() {
            const currentInput = this.input.val();
            // Implementar lógica de autocompletado
            // Por ahora, un placeholder
            console.log('Autocompletando:', currentInput);
        }

        addToHistory(command) {
            if (!this.options.enableHistory) {
                return;
            }
            
            this.history.push(command);
            
            if (this.history.length > this.options.maxHistorySize) {
                this.history.shift();
            }
            
            this.historyIndex = this.history.length;
            this.saveHistory();
        }

        navigateHistory(direction) {
            if (this.history.length === 0) {
                return;
            }
            
            if (direction === 'up') {
                if (this.historyIndex > 0) {
                    this.historyIndex--;
                    this.input.val(this.history[this.historyIndex]);
                }
            } else {
                if (this.historyIndex < this.history.length - 1) {
                    this.historyIndex++;
                    this.input.val(this.history[this.historyIndex]);
                } else {
                    this.historyIndex = this.history.length;
                    this.input.val('');
                }
            }
        }

        saveHistory() {
            if (typeof localStorage !== 'undefined') {
                const key = `cv_soc_terminal_history_${this.options.type}`;
                localStorage.setItem(key, JSON.stringify(this.history));
            }
        }

        loadHistory() {
            if (typeof localStorage !== 'undefined') {
                const key = `cv_soc_terminal_history_${this.options.type}`;
                const saved = localStorage.getItem(key);
                
                if (saved) {
                    try {
                        this.history = JSON.parse(saved);
                        this.historyIndex = this.history.length;
                    } catch (e) {
                        console.error('Error loading history:', e);
                    }
                }
            }
        }

        clear() {
            this.screen.empty();
        }

        reset() {
            this.clear();
            this.history = [];
            this.historyIndex = -1;
            this.currentPath = '~';
            this.saveHistory();
            this.displayWelcome();
        }

        addLine(text, className = '') {
            const line = $('<div class="cv-soc-terminal-line"></div>');
            if (className) {
                line.addClass(className);
            }
            line.text(text);
            this.screen.append(line);
            this.scrollToBottom();
        }

        showLoading() {
            const loadingLine = $('<div class="cv-soc-terminal-line cv-soc-terminal-loading"></div>')
                .html('⏳ Procesando comando...');
            this.screen.append(loadingLine);
            this.scrollToBottom();
        }

        hideLoading() {
            this.screen.find('.cv-soc-terminal-loading').remove();
        }

        scrollToBottom() {
            this.screen.scrollTop(this.screen[0].scrollHeight);
        }

        focus() {
            this.input.focus();
        }

        updateProgress() {
            // Actualizar progreso del taller
            $.ajax({
                url: cvSocData.restUrl + 'update-progress',
                method: 'POST',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', cvSocData.nonce);
                },
                data: JSON.stringify({
                    workshop_id: this.options.workshopId || 0,
                    task_id: this.getCurrentTaskId(),
                    status: 'completed',
                }),
                contentType: 'application/json'
            });
        }

        getCurrentTaskId() {
            // Obtener ID de tarea actual desde el contexto
            return this.element.data('current-task-id') || null;
        }

        playSuccessAnimation() {
            this.element.addClass('terminal-success-pulse');
            setTimeout(() => {
                this.element.removeClass('terminal-success-pulse');
            }, 1000);
        }

        playNotificationSound() {
            // Aquí se podría agregar un sonido de notificación
            // Por ahora solo logging
            console.log('Notification received');
        }
    }

    /**
     * jQuery Plugin
     */
    $.fn.cvSocTerminal = function(options) {
        return this.each(function() {
            if (!$.data(this, 'cvSocTerminal')) {
                $.data(this, 'cvSocTerminal', new CVSOCTerminal(this, options));
            }
        });
    };

    /**
     * Inicialización automática
     */
    $(document).ready(function() {
        // Inicializar todas las terminales en la página
        $('.cv-soc-terminal').each(function() {
            const $terminal = $(this);
            const type = $terminal.data('terminal-type') || 'general';
            const workshopId = $terminal.data('workshop-id') || null;
            
            $terminal.cvSocTerminal({
                type: type,
                workshopId: workshopId,
            });
        });
        
        // Conectar terminales Red Team y Blue Team para interacciones
        const redTerminal = $('.cv-soc-terminal.redteam').data('cvSocTerminal');
        const blueTerminal = $('.cv-soc-terminal.blueteam').data('cvSocTerminal');
        
        if (redTerminal && blueTerminal) {
            redTerminal.options.otherTerminal = blueTerminal;
            blueTerminal.options.otherTerminal = redTerminal;
        }
    });

})(jQuery);
