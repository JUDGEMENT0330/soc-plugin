/**
 * Main.js - Funcionalidades principales del plugin
 * Cyber Valtorix SOC Training Platform
 * Version: 3.0.0
 */

(function($) {
    'use strict';

    const CVSOCPlatform = {
        init() {
            this.setupWorkshops();
            this.setupProgress();
            this.setupTabs();
            this.setupModals();
            this.setupAIAssist();
            this.setupNotifications();
            this.loadUserData();
        },

        /**
         * Configuración de talleres
         */
        setupWorkshops() {
            // Cargar talleres disponibles
            this.loadWorkshops();
            
            // Click en card de taller
            $(document).on('click', '.cv-soc-workshop-card', function() {
                const workshopId = $(this).data('workshop-id');
                CVSOCPlatform.openWorkshop(workshopId);
            });
            
            // Filtros de talleres
            $('.cv-soc-workshop-filter').on('change', function() {
                CVSOCPlatform.filterWorkshops();
            });
            
            // Búsqueda de talleres
            $('.cv-soc-workshop-search').on('input', function() {
                CVSOCPlatform.searchWorkshops($(this).val());
            });
        },

        loadWorkshops() {
            $.ajax({
                url: cvSocData.restUrl + 'workshops',
                method: 'GET',
                success: (workshops) => {
                    this.renderWorkshops(workshops);
                },
                error: (xhr, status, error) => {
                    console.error('Error loading workshops:', error);
                    this.showNotification('Error al cargar talleres', 'error');
                }
            });
        },

        renderWorkshops(workshops) {
            const container = $('.cv-soc-workshops-grid');
            container.empty();
            
            workshops.forEach(workshop => {
                const card = this.createWorkshopCard(workshop);
                container.append(card);
            });
        },

        createWorkshopCard(workshop) {
            const difficultyClass = `badge-${workshop.difficulty}`;
            const difficultyText = {
                beginner: 'Principiante',
                intermediate: 'Intermedio',
                advanced: 'Avanzado',
                expert: 'Experto'
            }[workshop.difficulty] || workshop.difficulty;
            
            return $(`
                <div class="cv-soc-card cv-soc-workshop-card" data-workshop-id="${workshop.id}">
                    <span class="cv-soc-workshop-badge ${difficultyClass}">${difficultyText}</span>
                    
                    <div class="cv-soc-card-header">
                        <h3 class="cv-soc-card-title">${workshop.title}</h3>
                    </div>
                    
                    <div class="cv-soc-card-body">
                        <p>${workshop.description || 'Sin descripción'}</p>
                        
                        <div class="cv-soc-workshop-meta">
                            <div class="cv-soc-workshop-meta-item">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                </svg>
                                <span>${workshop.estimated_time || 60} min</span>
                            </div>
                        </div>
                        
                        <div class="cv-soc-progress-bar">
                            <div class="cv-soc-progress-fill" style="width: ${this.getWorkshopProgress(workshop.id)}%"></div>
                        </div>
                        <div class="cv-soc-progress-text">
                            <span>Progreso</span>
                            <span>${this.getWorkshopProgress(workshop.id)}%</span>
                        </div>
                        
                        <button class="cv-soc-btn cv-soc-btn-primary cv-soc-mt-2" style="width: 100%;">
                            ${this.getWorkshopProgress(workshop.id) > 0 ? 'Continuar' : 'Comenzar'}
                        </button>
                    </div>
                </div>
            `);
        },

        getWorkshopProgress(workshopId) {
            // Obtener progreso del localStorage o desde el servidor
            const progress = localStorage.getItem(`cv_soc_progress_${workshopId}`);
            return progress ? parseInt(progress) : 0;
        },

        filterWorkshops() {
            const difficulty = $('.cv-soc-workshop-filter[data-filter="difficulty"]').val();
            const category = $('.cv-soc-workshop-filter[data-filter="category"]').val();
            
            $('.cv-soc-workshop-card').each(function() {
                const $card = $(this);
                let show = true;
                
                if (difficulty && !$card.find(`.badge-${difficulty}`).length) {
                    show = false;
                }
                
                if (category && $card.data('category') !== category) {
                    show = false;
                }
                
                $card.toggle(show);
            });
        },

        searchWorkshops(query) {
            query = query.toLowerCase();
            
            $('.cv-soc-workshop-card').each(function() {
                const $card = $(this);
                const title = $card.find('.cv-soc-card-title').text().toLowerCase();
                const description = $card.find('.cv-soc-card-body p').text().toLowerCase();
                
                const matches = title.includes(query) || description.includes(query);
                $card.toggle(matches);
            });
        },

        openWorkshop(workshopId) {
            // Cargar detalles del taller
            $.ajax({
                url: cvSocData.restUrl + 'workshop/' + workshopId,
                method: 'GET',
                success: (workshop) => {
                    this.displayWorkshopDetails(workshop);
                },
                error: (xhr, status, error) => {
                    console.error('Error loading workshop:', error);
                    this.showNotification('Error al cargar el taller', 'error');
                }
            });
        },

        displayWorkshopDetails(workshop) {
            // Aquí se mostraría la página completa del taller
            // Por ahora, redirigimos o abrimos un modal
            window.location.href = `?workshop=${workshop.id}`;
        },

        /**
         * Configuración de progreso
         */
        setupProgress() {
            this.loadUserProgress();
            
            // Actualizar progreso periódicamente
            setInterval(() => {
                this.updateProgressDisplay();
            }, 30000); // Cada 30 segundos
        },

        loadUserProgress() {
            if (!cvSocData.isLoggedIn) {
                return;
            }
            
            $.ajax({
                url: cvSocData.restUrl + 'progress',
                method: 'GET',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', cvSocData.nonce);
                },
                success: (progress) => {
                    this.displayProgress(progress);
                },
                error: (xhr, status, error) => {
                    console.error('Error loading progress:', error);
                }
            });
        },

        displayProgress(progress) {
            // Actualizar estadísticas del dashboard
            const stats = this.calculateStats(progress);
            
            $('.cv-soc-stat-value[data-stat="workshops-completed"]').text(stats.workshopsCompleted);
            $('.cv-soc-stat-value[data-stat="total-time"]').text(stats.totalTime + ' hrs');
            $('.cv-soc-stat-value[data-stat="badges-earned"]').text(stats.badgesEarned);
            $('.cv-soc-stat-value[data-stat="current-streak"]').text(stats.currentStreak + ' días');
        },

        calculateStats(progress) {
            // Calcular estadísticas desde los datos de progreso
            const workshopsCompleted = progress.filter(p => p.status === 'completed').length;
            const totalTime = progress.reduce((sum, p) => sum + (p.time_spent || 0), 0) / 3600; // Convertir a horas
            
            return {
                workshopsCompleted,
                totalTime: Math.round(totalTime * 10) / 10,
                badgesEarned: 0, // Implementar lógica de badges
                currentStreak: this.calculateStreak(progress),
            };
        },

        calculateStreak(progress) {
            // Calcular racha de días consecutivos
            // Implementación simplificada
            return 0;
        },

        updateProgressDisplay() {
            // Actualizar barras de progreso visibles
            $('.cv-soc-workshop-card').each(function() {
                const workshopId = $(this).data('workshop-id');
                const progress = CVSOCPlatform.getWorkshopProgress(workshopId);
                
                $(this).find('.cv-soc-progress-fill').css('width', progress + '%');
                $(this).find('.cv-soc-progress-text span:last').text(progress + '%');
            });
        },

        /**
         * Configuración de tabs
         */
        setupTabs() {
            $('.cv-soc-tab').on('click', function() {
                const $tab = $(this);
                const targetId = $tab.data('tab');
                
                // Desactivar todos los tabs
                $tab.siblings().removeClass('active');
                $('.cv-soc-tab-content').removeClass('active');
                
                // Activar tab seleccionado
                $tab.addClass('active');
                $(`#${targetId}`).addClass('active');
            });
        },

        /**
         * Configuración de modales
         */
        setupModals() {
            // Abrir modal
            $(document).on('click', '[data-modal]', function() {
                const modalId = $(this).data('modal');
                $(`#${modalId}`).addClass('active');
            });
            
            // Cerrar modal
            $(document).on('click', '.cv-soc-modal-close', function() {
                $(this).closest('.cv-soc-modal').removeClass('active');
            });
            
            // Cerrar modal al hacer click fuera
            $(document).on('click', '.cv-soc-modal', function(e) {
                if ($(e.target).hasClass('cv-soc-modal')) {
                    $(this).removeClass('active');
                }
            });
            
            // Cerrar modal con ESC
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.cv-soc-modal.active').removeClass('active');
                }
            });
        },

        /**
         * Asistencia de IA
         */
        setupAIAssist() {
            // Botón de ayuda IA
            $(document).on('click', '.cv-soc-ai-assist-btn', function() {
                CVSOCPlatform.openAIAssist();
            });
            
            // Procesar consulta de IA
            $(document).on('submit', '.cv-soc-ai-form', function(e) {
                e.preventDefault();
                const query = $(this).find('textarea').val();
                CVSOCPlatform.askAI(query);
            });
        },

        openAIAssist() {
            $('#cv-soc-ai-modal').addClass('active');
            $('#cv-soc-ai-input').focus();
        },

        askAI(query) {
            if (!query.trim()) {
                return;
            }
            
            const $output = $('#cv-soc-ai-output');
            $output.html('<div class="cv-soc-loading"></div> Consultando IA...');
            
            $.ajax({
                url: cvSocData.restUrl + 'ai-assist',
                method: 'POST',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', cvSocData.nonce);
                },
                data: JSON.stringify({
                    query: query,
                    context: {
                        current_workshop: this.getCurrentWorkshop(),
                        user_progress: this.getUserProgress(),
                    }
                }),
                contentType: 'application/json',
                success: (response) => {
                    this.displayAIResponse(response);
                },
                error: (xhr, status, error) => {
                    $output.html(`<div class="cv-soc-alert cv-soc-alert-error">Error: ${error}</div>`);
                }
            });
        },

        displayAIResponse(response) {
            const $output = $('#cv-soc-ai-output');
            
            let html = `<div class="cv-soc-ai-response">${response.response}</div>`;
            
            if (response.suggestions && response.suggestions.length > 0) {
                html += '<div class="cv-soc-ai-suggestions"><h4>Sugerencias:</h4><ul>';
                response.suggestions.forEach(suggestion => {
                    html += `<li>${suggestion}</li>`;
                });
                html += '</ul></div>';
            }
            
            $output.html(html);
        },

        getCurrentWorkshop() {
            // Obtener ID del taller actual
            return $('.cv-soc-terminal').first().data('workshop-id') || null;
        },

        getUserProgress() {
            // Obtener progreso del usuario actual
            return {}; // Implementar
        },

        /**
         * Sistema de notificaciones
         */
        setupNotifications() {
            // Contenedor de notificaciones
            if (!$('#cv-soc-notifications').length) {
                $('body').append('<div id="cv-soc-notifications"></div>');
            }
        },

        showNotification(message, type = 'info', duration = 5000) {
            const notification = $(`
                <div class="cv-soc-notification cv-soc-notification-${type}">
                    <span class="cv-soc-notification-message">${message}</span>
                    <button class="cv-soc-notification-close">&times;</button>
                </div>
            `);
            
            $('#cv-soc-notifications').append(notification);
            
            // Animar entrada
            setTimeout(() => {
                notification.addClass('active');
            }, 10);
            
            // Auto-cerrar
            if (duration > 0) {
                setTimeout(() => {
                    this.closeNotification(notification);
                }, duration);
            }
            
            // Cerrar manualmente
            notification.find('.cv-soc-notification-close').on('click', () => {
                this.closeNotification(notification);
            });
        },

        closeNotification($notification) {
            $notification.removeClass('active');
            setTimeout(() => {
                $notification.remove();
            }, 300);
        },

        /**
         * Cargar datos del usuario
         */
        loadUserData() {
            if (!cvSocData.isLoggedIn) {
                return;
            }
            
            // Cargar perfil del usuario
            // Cargar badges
            // Cargar estadísticas
            this.loadUserProfile();
            this.loadUserBadges();
        },

        loadUserProfile() {
            // Implementar carga de perfil
        },

        loadUserBadges() {
            // Implementar carga de badges
        },

        /**
         * Utilidades
         */
        formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            
            if (hours > 0) {
                return `${hours}h ${minutes}m`;
            }
            return `${minutes}m`;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
    };

    /**
     * Inicializar cuando el DOM esté listo
     */
    $(document).ready(function() {
        CVSOCPlatform.init();
    });

    // Exponer objeto global
    window.CVSOCPlatform = CVSOCPlatform;

})(jQuery);
