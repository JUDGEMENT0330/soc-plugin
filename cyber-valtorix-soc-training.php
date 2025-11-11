<?php
/**
 * Plugin Name: Cyber Valtorix SOC Training Platform
 * Plugin URI: https://cybervaltorix.com/plugins/soc-training
 * Description: Plataforma completa de capacitación SOC con terminales interactivas Red Team/Blue Team, tracking de progreso, integración IA y sistema de talleres escalable.
 * Version: 3.0.0
 * Author: Cyber Valtorix S.A. DE C.V.
 * Author URI: https://cybervaltorix.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cv-soc-training
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

// Constantes del plugin
define('CV_SOC_VERSION', '3.0.0');
define('CV_SOC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CV_SOC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CV_SOC_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('CV_SOC_ASSETS_URL', CV_SOC_PLUGIN_URL . 'assets/');
define('CV_SOC_INCLUDES_DIR', CV_SOC_PLUGIN_DIR . 'includes/');

// Autoloader mejorado para clases
spl_autoload_register(function ($class) {
    $prefix = 'CV_SOC_';
    $base_dir = CV_SOC_INCLUDES_DIR . 'classes/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . 'class-' . str_replace('_', '-', strtolower($relative_class)) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Clase principal del plugin
 */
class CV_SOC_Training_Platform {
    
    private static $instance = null;
    private $db_version = '3.0';
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        add_action('init', [$this, 'init_plugin']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }
    
    public function activate() {
        $this->create_tables();
        $this->create_roles();
        $this->add_default_workshops();
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    private function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Tabla de talleres
        $table_workshops = $wpdb->prefix . 'cv_soc_workshops';
        $sql_workshops = "CREATE TABLE IF NOT EXISTS $table_workshops (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description longtext,
            category varchar(100) DEFAULT 'general',
            difficulty enum('beginner','intermediate','advanced','expert') DEFAULT 'beginner',
            estimated_time int(11) DEFAULT 0,
            prerequisites longtext,
            learning_objectives longtext,
            content longtext,
            order_index int(11) DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY category (category),
            KEY difficulty (difficulty),
            KEY is_active (is_active)
        ) $charset_collate;";
        
        // Tabla de tareas
        $table_tasks = $wpdb->prefix . 'cv_soc_tasks';
        $sql_tasks = "CREATE TABLE IF NOT EXISTS $table_tasks (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            workshop_id bigint(20) NOT NULL,
            title varchar(255) NOT NULL,
            description longtext,
            task_type enum('command','analysis','investigation','defense','attack') DEFAULT 'command',
            terminal_type enum('redteam','blueteam','both') DEFAULT 'both',
            expected_command varchar(500),
            validation_type enum('exact','regex','ai','manual') DEFAULT 'exact',
            validation_pattern longtext,
            points int(11) DEFAULT 10,
            hints longtext,
            order_index int(11) DEFAULT 0,
            is_required tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY workshop_id (workshop_id),
            KEY terminal_type (terminal_type)
        ) $charset_collate;";
        
        // Tabla de progreso de usuarios
        $table_progress = $wpdb->prefix . 'cv_soc_user_progress';
        $sql_progress = "CREATE TABLE IF NOT EXISTS $table_progress (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            workshop_id bigint(20) NOT NULL,
            task_id bigint(20),
            status enum('not_started','in_progress','completed','validated') DEFAULT 'not_started',
            progress_percentage decimal(5,2) DEFAULT 0.00,
            attempts int(11) DEFAULT 0,
            score int(11) DEFAULT 0,
            time_spent int(11) DEFAULT 0,
            last_activity datetime,
            completed_at datetime,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_workshop_task (user_id, workshop_id, task_id),
            KEY user_id (user_id),
            KEY workshop_id (workshop_id),
            KEY status (status)
        ) $charset_collate;";
        
        // Tabla de comandos ejecutados (para tracking y análisis)
        $table_commands = $wpdb->prefix . 'cv_soc_command_history';
        $sql_commands = "CREATE TABLE IF NOT EXISTS $table_commands (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            workshop_id bigint(20),
            task_id bigint(20),
            terminal_type enum('redteam','blueteam') NOT NULL,
            command varchar(1000) NOT NULL,
            output longtext,
            success tinyint(1) DEFAULT 0,
            execution_time float DEFAULT 0,
            ip_address varchar(45),
            user_agent varchar(500),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY terminal_type (terminal_type),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Tabla de interacciones Red Team vs Blue Team
        $table_interactions = $wpdb->prefix . 'cv_soc_team_interactions';
        $sql_interactions = "CREATE TABLE IF NOT EXISTS $table_interactions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            workshop_id bigint(20) NOT NULL,
            redteam_user_id bigint(20),
            blueteam_user_id bigint(20),
            interaction_type enum('attack','defense','scan','block','exploit','patch') NOT NULL,
            redteam_action longtext,
            blueteam_response longtext,
            outcome enum('redteam_success','blueteam_success','ongoing','escalated') DEFAULT 'ongoing',
            severity enum('info','low','medium','high','critical') DEFAULT 'medium',
            details longtext,
            ai_analysis longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY workshop_id (workshop_id),
            KEY interaction_type (interaction_type),
            KEY outcome (outcome)
        ) $charset_collate;";
        
        // Tabla de configuración de AI
        $table_ai_config = $wpdb->prefix . 'cv_soc_ai_config';
        $sql_ai_config = "CREATE TABLE IF NOT EXISTS $table_ai_config (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            config_key varchar(255) NOT NULL UNIQUE,
            config_value longtext,
            config_type enum('string','json','int','bool') DEFAULT 'string',
            description text,
            is_active tinyint(1) DEFAULT 1,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY config_key (config_key)
        ) $charset_collate;";
        
        // Tabla de badges y logros
        $table_badges = $wpdb->prefix . 'cv_soc_badges';
        $sql_badges = "CREATE TABLE IF NOT EXISTS $table_badges (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text,
            icon_url varchar(500),
            criteria longtext,
            points int(11) DEFAULT 0,
            rarity enum('common','uncommon','rare','epic','legendary') DEFAULT 'common',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Tabla de badges de usuarios
        $table_user_badges = $wpdb->prefix . 'cv_soc_user_badges';
        $sql_user_badges = "CREATE TABLE IF NOT EXISTS $table_user_badges (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            badge_id bigint(20) NOT NULL,
            earned_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_badge (user_id, badge_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_workshops);
        dbDelta($sql_tasks);
        dbDelta($sql_progress);
        dbDelta($sql_commands);
        dbDelta($sql_interactions);
        dbDelta($sql_ai_config);
        dbDelta($sql_badges);
        dbDelta($sql_user_badges);
        
        update_option('cv_soc_db_version', $this->db_version);
    }
    
    private function create_roles() {
        // Rol de estudiante SOC
        add_role('soc_student', 'SOC Student', [
            'read' => true,
            'cv_soc_access_workshops' => true,
            'cv_soc_track_progress' => true,
            'cv_soc_use_terminals' => true,
        ]);
        
        // Rol de instructor SOC
        add_role('soc_instructor', 'SOC Instructor', [
            'read' => true,
            'cv_soc_access_workshops' => true,
            'cv_soc_manage_workshops' => true,
            'cv_soc_view_all_progress' => true,
            'cv_soc_use_terminals' => true,
            'cv_soc_manage_ai_config' => true,
        ]);
    }
    
    private function add_default_workshops() {
        global $wpdb;
        $table = $wpdb->prefix . 'cv_soc_workshops';
        
        // Verificar si ya existen talleres
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) {
            return;
        }
        
        $default_workshops = [
            [
                'title' => 'Fundamentos de Linux para SOC',
                'description' => 'Aprende los comandos esenciales de Linux necesarios para operar en un Security Operations Center.',
                'category' => 'linux-fundamentals',
                'difficulty' => 'beginner',
                'estimated_time' => 120,
                'order_index' => 1,
            ],
            [
                'title' => 'Análisis de Logs y SIEM',
                'description' => 'Domina el análisis de logs de seguridad y el uso de herramientas SIEM.',
                'category' => 'log-analysis',
                'difficulty' => 'intermediate',
                'estimated_time' => 180,
                'order_index' => 2,
            ],
            [
                'title' => 'Detección de Intrusiones',
                'description' => 'Aprende a detectar y analizar intentos de intrusión en sistemas.',
                'category' => 'intrusion-detection',
                'difficulty' => 'intermediate',
                'estimated_time' => 150,
                'order_index' => 3,
            ],
            [
                'title' => 'Red Team Operations',
                'description' => 'Técnicas ofensivas de seguridad y simulación de ataques.',
                'category' => 'red-team',
                'difficulty' => 'advanced',
                'estimated_time' => 240,
                'order_index' => 4,
            ],
            [
                'title' => 'Blue Team Defense',
                'description' => 'Estrategias defensivas y respuesta a incidentes de seguridad.',
                'category' => 'blue-team',
                'difficulty' => 'advanced',
                'estimated_time' => 240,
                'order_index' => 5,
            ],
        ];
        
        foreach ($default_workshops as $workshop) {
            $wpdb->insert($table, $workshop);
        }
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('cv-soc-training', false, dirname(CV_SOC_PLUGIN_BASENAME) . '/languages');
    }
    
    public function init_plugin() {
        // Registrar custom post types si es necesario
        $this->register_shortcodes();
    }
    
    private function register_shortcodes() {
        add_shortcode('cv_soc_dashboard', [$this, 'render_dashboard']);
        add_shortcode('cv_soc_workshops', [$this, 'render_workshops']);
        add_shortcode('cv_soc_terminals', [$this, 'render_terminals']);
        add_shortcode('cv_soc_progress', [$this, 'render_progress']);
    }
    
    public function render_dashboard($atts) {
        ob_start();
        include CV_SOC_PLUGIN_DIR . 'templates/dashboard.php';
        return ob_get_clean();
    }
    
    public function render_workshops($atts) {
        ob_start();
        include CV_SOC_PLUGIN_DIR . 'templates/workshops.php';
        return ob_get_clean();
    }
    
    public function render_terminals($atts) {
        $atts = shortcode_atts([
            'workshop_id' => 0,
            'mode' => 'both' // 'redteam', 'blueteam', 'both'
        ], $atts);
        
        ob_start();
        include CV_SOC_PLUGIN_DIR . 'templates/terminals.php';
        return ob_get_clean();
    }
    
    public function render_progress($atts) {
        ob_start();
        include CV_SOC_PLUGIN_DIR . 'templates/progress.php';
        return ob_get_clean();
    }
    
    public function enqueue_scripts() {
        // CSS principal
        wp_enqueue_style(
            'cv-soc-main',
            CV_SOC_ASSETS_URL . 'css/main.css',
            [],
            CV_SOC_VERSION
        );
        
        // Terminal CSS
        wp_enqueue_style(
            'cv-soc-terminal',
            CV_SOC_ASSETS_URL . 'css/terminal.css',
            [],
            CV_SOC_VERSION
        );
        
        // jQuery (ya incluido en WordPress)
        wp_enqueue_script('jquery');
        
        // Terminal JS
        wp_enqueue_script(
            'cv-soc-terminal',
            CV_SOC_ASSETS_URL . 'js/terminal.js',
            ['jquery'],
            CV_SOC_VERSION,
            true
        );
        
        // Main JS
        wp_enqueue_script(
            'cv-soc-main',
            CV_SOC_ASSETS_URL . 'js/main.js',
            ['jquery', 'cv-soc-terminal'],
            CV_SOC_VERSION,
            true
        );
        
        // Localización de variables para JS
        wp_localize_script('cv-soc-main', 'cvSocData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('cv-soc/v1/'),
            'nonce' => wp_create_nonce('cv_soc_nonce'),
            'currentUser' => get_current_user_id(),
            'isLoggedIn' => is_user_logged_in(),
            'translations' => [
                'loading' => __('Cargando...', 'cv-soc-training'),
                'error' => __('Error', 'cv-soc-training'),
                'success' => __('Éxito', 'cv-soc-training'),
                'commandExecuted' => __('Comando ejecutado', 'cv-soc-training'),
                'invalidCommand' => __('Comando inválido', 'cv-soc-training'),
            ]
        ]);
    }
    
    public function admin_enqueue_scripts($hook) {
        // Cargar scripts solo en páginas del plugin
        if (strpos($hook, 'cv-soc') === false) {
            return;
        }
        
        wp_enqueue_style('cv-soc-admin', CV_SOC_ASSETS_URL . 'css/admin.css', [], CV_SOC_VERSION);
        wp_enqueue_script('cv-soc-admin', CV_SOC_ASSETS_URL . 'js/admin.js', ['jquery'], CV_SOC_VERSION, true);
    }
    
    public function register_rest_routes() {
        // Registrar rutas REST API
        register_rest_route('cv-soc/v1', '/workshops', [
            'methods' => 'GET',
            'callback' => [$this, 'api_get_workshops'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route('cv-soc/v1', '/workshop/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'api_get_workshop'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route('cv-soc/v1', '/execute-command', [
            'methods' => 'POST',
            'callback' => [$this, 'api_execute_command'],
            'permission_callback' => 'is_user_logged_in',
        ]);
        
        register_rest_route('cv-soc/v1', '/progress', [
            'methods' => 'GET',
            'callback' => [$this, 'api_get_progress'],
            'permission_callback' => 'is_user_logged_in',
        ]);
        
        register_rest_route('cv-soc/v1', '/update-progress', [
            'methods' => 'POST',
            'callback' => [$this, 'api_update_progress'],
            'permission_callback' => 'is_user_logged_in',
        ]);
        
        register_rest_route('cv-soc/v1', '/team-interaction', [
            'methods' => 'POST',
            'callback' => [$this, 'api_team_interaction'],
            'permission_callback' => 'is_user_logged_in',
        ]);
        
        register_rest_route('cv-soc/v1', '/ai-assist', [
            'methods' => 'POST',
            'callback' => [$this, 'api_ai_assist'],
            'permission_callback' => 'is_user_logged_in',
        ]);
    }
    
    public function api_get_workshops($request) {
        global $wpdb;
        $table = $wpdb->prefix . 'cv_soc_workshops';
        
        $workshops = $wpdb->get_results(
            "SELECT * FROM $table WHERE is_active = 1 ORDER BY order_index ASC",
            ARRAY_A
        );
        
        return rest_ensure_response($workshops);
    }
    
    public function api_get_workshop($request) {
        global $wpdb;
        $id = $request['id'];
        $table = $wpdb->prefix . 'cv_soc_workshops';
        
        $workshop = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id),
            ARRAY_A
        );
        
        if (!$workshop) {
            return new WP_Error('not_found', 'Workshop not found', ['status' => 404]);
        }
        
        // Obtener tareas del taller
        $tasks_table = $wpdb->prefix . 'cv_soc_tasks';
        $tasks = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $tasks_table WHERE workshop_id = %d ORDER BY order_index ASC",
                $id
            ),
            ARRAY_A
        );
        
        $workshop['tasks'] = $tasks;
        
        return rest_ensure_response($workshop);
    }
    
    public function api_execute_command($request) {
        $user_id = get_current_user_id();
        $command = sanitize_text_field($request['command']);
        $terminal_type = sanitize_text_field($request['terminal_type']);
        $workshop_id = intval($request['workshop_id']);
        $task_id = isset($request['task_id']) ? intval($request['task_id']) : null;
        
        // Registrar comando en historial
        global $wpdb;
        $commands_table = $wpdb->prefix . 'cv_soc_command_history';
        
        $wpdb->insert($commands_table, [
            'user_id' => $user_id,
            'workshop_id' => $workshop_id,
            'task_id' => $task_id,
            'terminal_type' => $terminal_type,
            'command' => $command,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        ]);
        
        // Procesar comando
        $result = $this->process_terminal_command($command, $terminal_type, $workshop_id, $task_id);
        
        // Actualizar registro con resultado
        $wpdb->update(
            $commands_table,
            [
                'output' => maybe_serialize($result['output']),
                'success' => $result['success'] ? 1 : 0,
                'execution_time' => $result['execution_time'],
            ],
            ['id' => $wpdb->insert_id]
        );
        
        return rest_ensure_response($result);
    }
    
    private function process_terminal_command($command, $terminal_type, $workshop_id, $task_id) {
        $start_time = microtime(true);
        
        // Aquí iría la lógica de procesamiento de comandos
        // Por ahora, simulamos respuestas
        
        $response = [
            'success' => true,
            'output' => "Comando ejecutado: $command",
            'execution_time' => microtime(true) - $start_time,
            'terminal_type' => $terminal_type,
        ];
        
        // Validar contra tarea si existe
        if ($task_id) {
            global $wpdb;
            $tasks_table = $wpdb->prefix . 'cv_soc_tasks';
            $task = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM $tasks_table WHERE id = %d", $task_id),
                ARRAY_A
            );
            
            if ($task) {
                $response['task_validation'] = $this->validate_task_completion($command, $task);
            }
        }
        
        return $response;
    }
    
    private function validate_task_completion($command, $task) {
        $validation_type = $task['validation_type'];
        
        switch ($validation_type) {
            case 'exact':
                return strcasecmp(trim($command), trim($task['expected_command'])) === 0;
                
            case 'regex':
                return preg_match($task['validation_pattern'], $command) === 1;
                
            case 'ai':
                // Aquí se integraría con un servicio de IA
                return false;
                
            case 'manual':
                return null; // Requiere validación manual del instructor
                
            default:
                return false;
        }
    }
    
    public function api_get_progress($request) {
        $user_id = get_current_user_id();
        global $wpdb;
        $progress_table = $wpdb->prefix . 'cv_soc_user_progress';
        
        $progress = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $progress_table WHERE user_id = %d ORDER BY updated_at DESC",
                $user_id
            ),
            ARRAY_A
        );
        
        return rest_ensure_response($progress);
    }
    
    public function api_update_progress($request) {
        $user_id = get_current_user_id();
        $workshop_id = intval($request['workshop_id']);
        $task_id = isset($request['task_id']) ? intval($request['task_id']) : null;
        $status = sanitize_text_field($request['status']);
        
        global $wpdb;
        $progress_table = $wpdb->prefix . 'cv_soc_user_progress';
        
        // Verificar si ya existe un registro
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $progress_table WHERE user_id = %d AND workshop_id = %d AND task_id = %d",
                $user_id, $workshop_id, $task_id
            )
        );
        
        if ($existing) {
            $wpdb->update(
                $progress_table,
                [
                    'status' => $status,
                    'last_activity' => current_time('mysql'),
                ],
                [
                    'user_id' => $user_id,
                    'workshop_id' => $workshop_id,
                    'task_id' => $task_id,
                ]
            );
        } else {
            $wpdb->insert(
                $progress_table,
                [
                    'user_id' => $user_id,
                    'workshop_id' => $workshop_id,
                    'task_id' => $task_id,
                    'status' => $status,
                    'last_activity' => current_time('mysql'),
                ]
            );
        }
        
        // Calcular porcentaje de progreso
        $this->calculate_workshop_progress($user_id, $workshop_id);
        
        return rest_ensure_response(['success' => true]);
    }
    
    private function calculate_workshop_progress($user_id, $workshop_id) {
        global $wpdb;
        $tasks_table = $wpdb->prefix . 'cv_soc_tasks';
        $progress_table = $wpdb->prefix . 'cv_soc_user_progress';
        
        // Total de tareas
        $total_tasks = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $tasks_table WHERE workshop_id = %d",
                $workshop_id
            )
        );
        
        // Tareas completadas
        $completed_tasks = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $progress_table 
                WHERE user_id = %d AND workshop_id = %d AND status = 'completed'",
                $user_id, $workshop_id
            )
        );
        
        $percentage = $total_tasks > 0 ? ($completed_tasks / $total_tasks) * 100 : 0;
        
        // Actualizar porcentaje general del taller
        $wpdb->update(
            $progress_table,
            ['progress_percentage' => $percentage],
            [
                'user_id' => $user_id,
                'workshop_id' => $workshop_id,
                'task_id' => null,
            ]
        );
        
        return $percentage;
    }
    
    public function api_team_interaction($request) {
        // Registrar interacción entre Red Team y Blue Team
        global $wpdb;
        $table = $wpdb->prefix . 'cv_soc_team_interactions';
        
        $data = [
            'workshop_id' => intval($request['workshop_id']),
            'redteam_user_id' => isset($request['redteam_user_id']) ? intval($request['redteam_user_id']) : null,
            'blueteam_user_id' => isset($request['blueteam_user_id']) ? intval($request['blueteam_user_id']) : null,
            'interaction_type' => sanitize_text_field($request['interaction_type']),
            'redteam_action' => sanitize_textarea_field($request['redteam_action']),
            'blueteam_response' => isset($request['blueteam_response']) ? sanitize_textarea_field($request['blueteam_response']) : null,
            'severity' => sanitize_text_field($request['severity']),
            'details' => maybe_serialize($request['details']),
        ];
        
        $wpdb->insert($table, $data);
        
        return rest_ensure_response(['success' => true, 'interaction_id' => $wpdb->insert_id]);
    }
    
    public function api_ai_assist($request) {
        $query = sanitize_textarea_field($request['query']);
        $context = isset($request['context']) ? $request['context'] : [];
        
        // Aquí se integraría con un servicio de IA (OpenAI, Claude, etc.)
        // Por ahora devolvemos una respuesta simulada
        
        $response = [
            'success' => true,
            'response' => "Respuesta de IA para: $query",
            'suggestions' => [
                'Intenta usar el comando: ls -la',
                'Revisa los permisos del archivo',
                'Consulta los logs del sistema',
            ],
        ];
        
        return rest_ensure_response($response);
    }
}

// Inicializar el plugin
function cv_soc_training_platform() {
    return CV_SOC_Training_Platform::get_instance();
}

cv_soc_training_platform();
