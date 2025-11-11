<?php
/**
 * Template: Dashboard Principal
 * Panel de control y estadísticas del usuario
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();
?>

<div class="cv-soc-dashboard">
    
    <!-- Header del Dashboard -->
    <div class="cv-soc-dashboard-header">
        <div class="cv-soc-dashboard-welcome">
            <?php if ($is_logged_in): ?>
                <h1>Bienvenido, <?php echo esc_html($current_user->display_name); ?>! 👋</h1>
                <p>Continúa tu entrenamiento en Cyber Valtorix SOC Training Platform</p>
            <?php else: ?>
                <h1>Bienvenido a Cyber Valtorix SOC Training Platform</h1>
                <p>Inicia sesión para comenzar tu entrenamiento</p>
            <?php endif; ?>
        </div>
        
        <?php if ($is_logged_in): ?>
        <div class="cv-soc-dashboard-actions">
            <button class="cv-soc-btn cv-soc-btn-primary" data-modal="cv-soc-new-workshop-modal">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                Nuevo Taller
            </button>
            <button class="cv-soc-btn cv-soc-btn-secondary cv-soc-ai-assist-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9.5 2c-1.82 0-3.53.5-5 1.35C7.99 5.08 10 7.81 10 11c0 .35-.03.69-.08 1.02C12.98 12.69 16 10.23 16 7c0-2.76-2.24-5-5-5s-5 2.24-5 5c0 1.12.37 2.16 1 3 .5-.65 1.11-1.19 1.82-1.59C8.03 12.07 8 11.54 8 11c0-2.21 1.79-4 4-4s4 1.79 4 4c0 2.21-1.79 4-4 4-.64 0-1.24-.15-1.79-.41C9.47 15.8 9 17.34 9 19c0 .19.01.37.02.56.65-.18 1.29-.43 1.91-.72.16-.96.58-1.85 1.21-2.57-.25-.09-.51-.16-.78-.21C11.08 15.84 11 15.43 11 15c0-1.66 1.34-3 3-3s3 1.34 3 3c0 1.66-1.34 3-3 3-.41 0-.8-.08-1.15-.24C12.23 19.06 12 20.48 12 22h2c0-2.21 1.79-4 4-4V16c-2.21 0-4-1.79-4-4 0-3.31 2.69-6 6-6s6 2.69 6 6c0 2.21-1.79 4-4 4v2c3.31 0 6-2.69 6-6 0-4.42-3.58-8-8-8z"/>
                </svg>
                Ayuda IA
            </button>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($is_logged_in): ?>
    <!-- Estadísticas del Usuario -->
    <div class="cv-soc-stats-grid">
        <div class="cv-soc-stat-card">
            <div class="cv-soc-stat-label">Talleres Completados</div>
            <div class="cv-soc-stat-value" data-stat="workshops-completed">0</div>
            <div class="cv-soc-stat-change positive">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 14l5-5 5 5z"/>
                </svg>
                +2 esta semana
            </div>
        </div>
        
        <div class="cv-soc-stat-card">
            <div class="cv-soc-stat-label">Tiempo Total</div>
            <div class="cv-soc-stat-value" data-stat="total-time">0</div>
            <div class="cv-soc-stat-change positive">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 14l5-5 5 5z"/>
                </svg>
                +5.2hrs esta semana
            </div>
        </div>
        
        <div class="cv-soc-stat-card">
            <div class="cv-soc-stat-label">Badges Obtenidos</div>
            <div class="cv-soc-stat-value" data-stat="badges-earned">0</div>
            <div class="cv-soc-stat-change">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
                3 disponibles
            </div>
        </div>
        
        <div class="cv-soc-stat-card">
            <div class="cv-soc-stat-label">Racha Actual</div>
            <div class="cv-soc-stat-value" data-stat="current-streak">0</div>
            <div class="cv-soc-stat-change">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z"/>
                </svg>
                ¡Sigue así!
            </div>
        </div>
    </div>
    
    <!-- Tabs de contenido -->
    <div class="cv-soc-dashboard-content">
        <div class="cv-soc-tabs">
            <button class="cv-soc-tab active" data-tab="tab-workshops">Talleres</button>
            <button class="cv-soc-tab" data-tab="tab-progress">Mi Progreso</button>
            <button class="cv-soc-tab" data-tab="tab-badges">Badges</button>
            <button class="cv-soc-tab" data-tab="tab-leaderboard">Ranking</button>
        </div>
        
        <!-- Tab: Talleres -->
        <div id="tab-workshops" class="cv-soc-tab-content active">
            <div class="cv-soc-section-header">
                <h2>Talleres Disponibles</h2>
                <div class="cv-soc-filters">
                    <select class="cv-soc-workshop-filter" data-filter="difficulty">
                        <option value="">Todas las dificultades</option>
                        <option value="beginner">Principiante</option>
                        <option value="intermediate">Intermedio</option>
                        <option value="advanced">Avanzado</option>
                        <option value="expert">Experto</option>
                    </select>
                    <select class="cv-soc-workshop-filter" data-filter="category">
                        <option value="">Todas las categorías</option>
                        <option value="linux-fundamentals">Fundamentos Linux</option>
                        <option value="log-analysis">Análisis de Logs</option>
                        <option value="intrusion-detection">Detección de Intrusiones</option>
                        <option value="red-team">Red Team</option>
                        <option value="blue-team">Blue Team</option>
                    </select>
                    <input type="text" class="cv-soc-workshop-search" placeholder="Buscar talleres...">
                </div>
            </div>
            
            <div class="cv-soc-workshops-grid">
                <!-- Los talleres se cargarán dinámicamente aquí -->
                <div class="cv-soc-loading-placeholder">
                    <div class="cv-soc-loading"></div>
                    <p>Cargando talleres...</p>
                </div>
            </div>
        </div>
        
        <!-- Tab: Mi Progreso -->
        <div id="tab-progress" class="cv-soc-tab-content">
            <div class="cv-soc-section-header">
                <h2>Mi Progreso</h2>
            </div>
            
            <div class="cv-soc-progress-overview">
                <div class="cv-soc-card">
                    <div class="cv-soc-card-header">
                        <h3 class="cv-soc-card-title">Progreso General</h3>
                    </div>
                    <div class="cv-soc-card-body">
                        <div class="cv-soc-progress-circle">
                            <svg viewBox="0 0 100 100" style="width: 200px; height: 200px;">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(184, 134, 11, 0.2)" stroke-width="10"/>
                                <circle cx="50" cy="50" r="45" fill="none" stroke="var(--cv-gold)" stroke-width="10" 
                                        stroke-dasharray="283" stroke-dashoffset="71" transform="rotate(-90 50 50)"/>
                                <text x="50" y="50" text-anchor="middle" dy=".3em" font-size="20" fill="var(--cv-gold)">75%</text>
                            </svg>
                        </div>
                        <p class="cv-soc-text-center cv-soc-mt-2">Completado 15 de 20 talleres</p>
                    </div>
                </div>
                
                <div class="cv-soc-card">
                    <div class="cv-soc-card-header">
                        <h3 class="cv-soc-card-title">Actividad Reciente</h3>
                    </div>
                    <div class="cv-soc-card-body">
                        <div class="cv-soc-activity-list">
                            <div class="cv-soc-activity-item">
                                <div class="cv-soc-activity-icon success">✓</div>
                                <div class="cv-soc-activity-details">
                                    <div class="cv-soc-activity-title">Completaste "Análisis de Logs"</div>
                                    <div class="cv-soc-activity-time">Hace 2 horas</div>
                                </div>
                            </div>
                            <div class="cv-soc-activity-item">
                                <div class="cv-soc-activity-icon badge">🏆</div>
                                <div class="cv-soc-activity-details">
                                    <div class="cv-soc-activity-title">Obtuviste "Log Master"</div>
                                    <div class="cv-soc-activity-time">Hace 2 horas</div>
                                </div>
                            </div>
                            <div class="cv-soc-activity-item">
                                <div class="cv-soc-activity-icon progress">📚</div>
                                <div class="cv-soc-activity-details">
                                    <div class="cv-soc-activity-title">Iniciaste "Red Team Operations"</div>
                                    <div class="cv-soc-activity-time">Hace 1 día</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="cv-soc-workshops-progress">
                <h3>Progreso por Taller</h3>
                <!-- Lista de talleres con progreso se cargará dinámicamente -->
            </div>
        </div>
        
        <!-- Tab: Badges -->
        <div id="tab-badges" class="cv-soc-tab-content">
            <div class="cv-soc-section-header">
                <h2>Badges y Logros</h2>
                <p>Colecciona badges completando talleres y desafíos</p>
            </div>
            
            <div class="cv-soc-badges-container">
                <!-- Badges de ejemplo -->
                <div class="cv-soc-badge-item earned">
                    <div class="cv-soc-badge-icon">🛡️</div>
                    <div class="cv-soc-badge-name">First Steps</div>
                    <div class="cv-soc-badge-description">Completa tu primer taller</div>
                </div>
                
                <div class="cv-soc-badge-item earned">
                    <div class="cv-soc-badge-icon">📊</div>
                    <div class="cv-soc-badge-name">Log Master</div>
                    <div class="cv-soc-badge-description">Domina el análisis de logs</div>
                </div>
                
                <div class="cv-soc-badge-item earned">
                    <div class="cv-soc-badge-icon">⚔️</div>
                    <div class="cv-soc-badge-name">Red Warrior</div>
                    <div class="cv-soc-badge-description">Completa 5 talleres Red Team</div>
                </div>
                
                <div class="cv-soc-badge-item locked">
                    <div class="cv-soc-badge-icon">🔥</div>
                    <div class="cv-soc-badge-name">Streak Master</div>
                    <div class="cv-soc-badge-description">Mantén una racha de 30 días</div>
                </div>
                
                <div class="cv-soc-badge-item locked">
                    <div class="cv-soc-badge-icon">👑</div>
                    <div class="cv-soc-badge-name">SOC Champion</div>
                    <div class="cv-soc-badge-description">Completa todos los talleres</div>
                </div>
                
                <div class="cv-soc-badge-item locked">
                    <div class="cv-soc-badge-icon">⚡</div>
                    <div class="cv-soc-badge-name">Speed Runner</div>
                    <div class="cv-soc-badge-description">Completa un taller en tiempo récord</div>
                </div>
            </div>
        </div>
        
        <!-- Tab: Ranking -->
        <div id="tab-leaderboard" class="cv-soc-tab-content">
            <div class="cv-soc-section-header">
                <h2>Tabla de Clasificación</h2>
                <p>Compite con otros estudiantes SOC</p>
            </div>
            
            <div class="cv-soc-leaderboard">
                <div class="cv-soc-card">
                    <table class="cv-soc-leaderboard-table">
                        <thead>
                            <tr>
                                <th>Posición</th>
                                <th>Usuario</th>
                                <th>Talleres</th>
                                <th>Badges</th>
                                <th>Puntos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="cv-soc-leaderboard-highlight">
                                <td><span class="rank-medal">🥇</span> 1</td>
                                <td><strong>usuario123</strong></td>
                                <td>20</td>
                                <td>15</td>
                                <td>2,500</td>
                            </tr>
                            <tr>
                                <td><span class="rank-medal">🥈</span> 2</td>
                                <td>analyst_pro</td>
                                <td>18</td>
                                <td>12</td>
                                <td>2,200</td>
                            </tr>
                            <tr>
                                <td><span class="rank-medal">🥉</span> 3</td>
                                <td>cyber_ninja</td>
                                <td>17</td>
                                <td>11</td>
                                <td>2,100</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>sec_expert</td>
                                <td>15</td>
                                <td>10</td>
                                <td>1,900</td>
                            </tr>
                            <tr class="cv-soc-leaderboard-current">
                                <td>5</td>
                                <td><strong>Tú</strong></td>
                                <td>15</td>
                                <td>8</td>
                                <td>1,850</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Vista para usuarios no autenticados -->
    <div class="cv-soc-guest-view">
        <div class="cv-soc-card cv-soc-text-center">
            <h2>Comienza tu entrenamiento en seguridad</h2>
            <p>Inicia sesión o regístrate para acceder a los talleres interactivos</p>
            <div class="cv-soc-mt-3">
                <a href="<?php echo wp_login_url(get_permalink()); ?>" class="cv-soc-btn cv-soc-btn-primary">
                    Iniciar Sesión
                </a>
                <a href="<?php echo wp_registration_url(); ?>" class="cv-soc-btn cv-soc-btn-secondary">
                    Registrarse
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
</div>

<style>
/* Estilos adicionales del dashboard */
.cv-soc-dashboard {
    padding: 2rem 0;
}

.cv-soc-dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 3rem;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.cv-soc-dashboard-welcome h1 {
    font-size: 2.5rem;
    color: var(--cv-gold);
    margin-bottom: 0.5rem;
}

.cv-soc-dashboard-welcome p {
    color: var(--text-secondary);
    font-size: 1.125rem;
}

.cv-soc-dashboard-actions {
    display: flex;
    gap: 1rem;
}

.cv-soc-section-header {
    margin-bottom: 2rem;
}

.cv-soc-section-header h2 {
    font-size: 1.75rem;
    color: var(--cv-gold);
    margin-bottom: 0.5rem;
}

.cv-soc-section-header p {
    color: var(--text-secondary);
}

.cv-soc-filters {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.cv-soc-filters select,
.cv-soc-filters input {
    padding: 0.75rem 1rem;
    background: var(--bg-tertiary);
    border: 1px solid rgba(184, 134, 11, 0.3);
    border-radius: 8px;
    color: var(--text-primary);
    font-family: inherit;
    font-size: 0.9375rem;
}

.cv-soc-filters select:focus,
.cv-soc-filters input:focus {
    outline: none;
    border-color: var(--cv-gold);
}

.cv-soc-progress-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.cv-soc-progress-circle {
    display: flex;
    justify-content: center;
    margin: 1rem 0;
}

.cv-soc-activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cv-soc-activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: rgba(184, 134, 11, 0.05);
    border-radius: 8px;
}

.cv-soc-activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.cv-soc-activity-icon.success {
    background: rgba(16, 185, 129, 0.2);
}

.cv-soc-activity-icon.badge {
    background: rgba(245, 158, 11, 0.2);
}

.cv-soc-activity-icon.progress {
    background: rgba(59, 130, 246, 0.2);
}

.cv-soc-activity-details {
    flex: 1;
}

.cv-soc-activity-title {
    color: var(--text-primary);
    font-weight: 500;
}

.cv-soc-activity-time {
    color: var(--text-muted);
    font-size: 0.875rem;
}

.cv-soc-leaderboard-table {
    width: 100%;
    border-collapse: collapse;
}

.cv-soc-leaderboard-table th,
.cv-soc-leaderboard-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid rgba(184, 134, 11, 0.1);
}

.cv-soc-leaderboard-table th {
    color: var(--cv-gold);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.875rem;
}

.cv-soc-leaderboard-table tbody tr:hover {
    background: rgba(184, 134, 11, 0.05);
}

.cv-soc-leaderboard-current {
    background: rgba(184, 134, 11, 0.1) !important;
    border-left: 3px solid var(--cv-gold);
}

.rank-medal {
    font-size: 1.25rem;
}

.cv-soc-guest-view {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
}

@media (max-width: 768px) {
    .cv-soc-dashboard-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .cv-soc-dashboard-welcome h1 {
        font-size: 2rem;
    }
    
    .cv-soc-filters {
        flex-direction: column;
    }
    
    .cv-soc-leaderboard-table {
        font-size: 0.875rem;
    }
    
    .cv-soc-leaderboard-table th,
    .cv-soc-leaderboard-table td {
        padding: 0.5rem;
    }
}
</style>
