# Cyber Valtorix SOC Training Platform - Plugin WordPress

**Versión:** 3.0.0  
**Autor:** Cyber Valtorix S.A. DE C.V.  
**Licencia:** GPL v2 o posterior  
**Requiere WordPress:** 6.0+  
**Requiere PHP:** 7.4+

## 📋 Descripción

Plugin completo de capacitación SOC con terminales interactivas Red Team/Blue Team, tracking de progreso, integración IA y sistema de talleres escalable. Diseñado para entrenar profesionales de seguridad en un entorno práctico y realista.

## ✨ Características Principales

### 🖥️ **Terminales Interactivas**
- **Red Team Terminal**: Terminal ofensiva para practicar técnicas de penetration testing
- **Blue Team Terminal**: Terminal defensiva para practicar detección y respuesta
- **Interacción en Tiempo Real**: Las acciones de un equipo son visibles para el otro
- **Historial de Comandos**: Navegación completa con flechas y autocompletado
- **Múltiples Pestañas**: Console, Historial y Tareas en cada terminal

### 📚 **Sistema de Talleres Escalable**
- Talleres organizados por dificultad (Principiante, Intermedio, Avanzado, Experto)
- Categorías personalizables
- Sistema de tareas con validación automática
- Sin restricciones de orden - los estudiantes eligen su ruta
- Barra de progreso basada en tareas completadas

### 📊 **Tracking de Progreso**
- Dashboard personalizado con estadísticas
- Progreso por taller y global
- Historial de actividad
- Sistema de puntos y badges
- Ranking de estudiantes

### 🤖 **Integración con IA**
- Asistencia inteligente para estudiantes
- Validación de comandos por IA
- Sugerencias personalizadas
- Análisis de patrones de aprendizaje

### 🏆 **Gamificación**
- Sistema de badges y logros
- Rachas de días consecutivos
- Tabla de clasificación
- Puntos por tareas completadas

### 👥 **Gestión de Usuarios**
- Roles personalizados (SOC Student, SOC Instructor)
- Sistema de autenticación integrado
- Permisos granulares
- Tracking individual

## 🚀 Instalación

### Método 1: Instalación Manual

1. **Descarga el plugin**
   ```bash
   # Descargar y descomprimir los archivos
   ```

2. **Sube a WordPress**
   - Sube la carpeta `cyber-valtorix-soc-training` a `/wp-content/plugins/`
   - O usa el uploader de WordPress: Panel de WordPress → Plugins → Añadir Nuevo → Subir Plugin

3. **Activa el plugin**
   - Ve a Panel de WordPress → Plugins
   - Busca "Cyber Valtorix SOC Training Platform"
   - Haz clic en "Activar"

4. **Configuración inicial**
   - El plugin creará automáticamente las tablas necesarias
   - Se agregarán talleres de ejemplo
   - Se crearán los roles de usuario necesarios

### Método 2: Instalación desde ZIP

1. Ve a WordPress Admin → Plugins → Añadir Nuevo
2. Haz clic en "Subir Plugin"
3. Selecciona el archivo ZIP del plugin
4. Haz clic en "Instalar Ahora"
5. Activa el plugin

## 📖 Uso

### Shortcodes Disponibles

#### 1. Dashboard Principal
```php
[cv_soc_dashboard]
```
Muestra el panel de control con estadísticas y progreso del usuario.

#### 2. Lista de Talleres
```php
[cv_soc_workshops]
```
Muestra todos los talleres disponibles con filtros y búsqueda.

#### 3. Terminales Interactivas
```php
// Ambas terminales (Red Team y Blue Team)
[cv_soc_terminals workshop_id="1" mode="both"]

// Solo Red Team
[cv_soc_terminals workshop_id="1" mode="redteam"]

// Solo Blue Team
[cv_soc_terminals workshop_id="1" mode="blueteam"]
```

#### 4. Progreso del Usuario
```php
[cv_soc_progress]
```
Muestra el progreso detallado del usuario actual.

### Ejemplo de Página Completa

```php
<!-- Página Principal -->
[cv_soc_dashboard]

<!-- Página de Talleres -->
<h1>Talleres Disponibles</h1>
[cv_soc_workshops]

<!-- Página de Taller Individual -->
<h1>Taller: Fundamentos de Linux</h1>
<p>Descripción del taller...</p>
[cv_soc_terminals workshop_id="1" mode="both"]
```

## 🛠️ Configuración

### API REST Endpoints

El plugin expone los siguientes endpoints:

- `GET /wp-json/cv-soc/v1/workshops` - Lista todos los talleres
- `GET /wp-json/cv-soc/v1/workshop/{id}` - Obtiene un taller específico
- `POST /wp-json/cv-soc/v1/execute-command` - Ejecuta un comando en terminal
- `GET /wp-json/cv-soc/v1/progress` - Obtiene progreso del usuario
- `POST /wp-json/cv-soc/v1/update-progress` - Actualiza progreso
- `POST /wp-json/cv-soc/v1/team-interaction` - Registra interacción entre equipos
- `POST /wp-json/cv-soc/v1/ai-assist` - Obtiene ayuda de IA

### Roles y Capacidades

#### SOC Student (soc_student)
- `cv_soc_access_workshops` - Acceso a talleres
- `cv_soc_track_progress` - Tracking de progreso
- `cv_soc_use_terminals` - Uso de terminales

#### SOC Instructor (soc_instructor)
- Todas las capacidades de Student, más:
- `cv_soc_manage_workshops` - Gestión de talleres
- `cv_soc_view_all_progress` - Ver progreso de todos
- `cv_soc_manage_ai_config` - Configurar IA

### Configuración de IA

Para habilitar la asistencia de IA, configura la API key en la base de datos:

```sql
INSERT INTO wp_cv_soc_ai_config (config_key, config_value, config_type) 
VALUES ('openai_api_key', 'tu-api-key-aquí', 'string');
```

O usa el panel de administración del plugin.

## 🎨 Personalización

### Estilos CSS

El plugin usa variables CSS para facilitar la personalización:

```css
:root {
    --cv-gold: #B8860B;
    --cv-gold-light: #DAA520;
    --cv-gold-dark: #8B6914;
    --bg-primary: #0a0f1c;
    --bg-secondary: #111827;
    --text-primary: #f9fafb;
    --redteam: #dc2626;
    --blueteam: #2563eb;
}
```

### Agregar Nuevos Talleres

#### Método 1: Base de Datos Directa

```sql
INSERT INTO wp_cv_soc_workshops 
(title, description, category, difficulty, estimated_time, order_index)
VALUES 
('Mi Nuevo Taller', 'Descripción...', 'categoria', 'intermediate', 120, 6);
```

#### Método 2: Programáticamente

```php
global $wpdb;
$table = $wpdb->prefix . 'cv_soc_workshops';

$wpdb->insert($table, [
    'title' => 'Mi Nuevo Taller',
    'description' => 'Descripción del taller',
    'category' => 'categoria',
    'difficulty' => 'intermediate',
    'estimated_time' => 120,
    'order_index' => 6,
]);
```

### Agregar Nuevas Tareas

```php
global $wpdb;
$tasks_table = $wpdb->prefix . 'cv_soc_tasks';

$wpdb->insert($tasks_table, [
    'workshop_id' => 1,
    'title' => 'Listar archivos',
    'description' => 'Usa el comando ls para listar archivos',
    'task_type' => 'command',
    'terminal_type' => 'both',
    'expected_command' => 'ls -la',
    'validation_type' => 'regex',
    'validation_pattern' => '/^ls(\s+-[a-z]+)*$/i',
    'points' => 10,
    'order_index' => 1,
]);
```

## 📊 Base de Datos

### Tablas Creadas

- `wp_cv_soc_workshops` - Talleres
- `wp_cv_soc_tasks` - Tareas de talleres
- `wp_cv_soc_user_progress` - Progreso de usuarios
- `wp_cv_soc_command_history` - Historial de comandos
- `wp_cv_soc_team_interactions` - Interacciones Red Team vs Blue Team
- `wp_cv_soc_ai_config` - Configuración de IA
- `wp_cv_soc_badges` - Badges disponibles
- `wp_cv_soc_user_badges` - Badges obtenidos por usuarios

## 🔧 Desarrollo

### Estructura del Plugin

```
cyber-valtorix-soc-training/
├── assets/
│   ├── css/
│   │   ├── main.css
│   │   ├── terminal.css
│   │   └── admin.css
│   ├── js/
│   │   ├── main.js
│   │   ├── terminal.js
│   │   └── admin.js
│   └── images/
├── includes/
│   ├── classes/
│   ├── admin/
│   └── api/
├── templates/
│   ├── dashboard.php
│   ├── workshops.php
│   ├── terminals.php
│   └── progress.php
├── languages/
└── cyber-valtorix-soc-training.php
```

### Agregar Comandos Personalizados

Edita `assets/js/terminal.js`:

```javascript
handleLocalCommand(command) {
    const parts = command.toLowerCase().trim().split(' ');
    const cmd = parts[0];
    
    switch(cmd) {
        case 'micomando':
            this.addLine('Salida de mi comando');
            return true;
        // ... otros comandos
    }
    return false;
}
```

### Hooks Disponibles

```php
// Antes de crear un taller
do_action('cv_soc_before_create_workshop', $workshop_data);

// Después de completar una tarea
do_action('cv_soc_task_completed', $user_id, $task_id, $workshop_id);

// Cuando se obtiene un badge
do_action('cv_soc_badge_earned', $user_id, $badge_id);
```

## 🐛 Debugging

### Modo Debug

Habilita el modo debug en `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Logs

Los logs se guardan en:
- WordPress: `wp-content/debug.log`
- Comandos: `wp_cv_soc_command_history`

## 🚧 Solución de Problemas

### Las terminales no cargan

1. Verifica que jQuery esté cargado
2. Revisa la consola del navegador para errores
3. Verifica permisos de archivos JS/CSS

### Los comandos no se ejecutan

1. Verifica que el usuario esté autenticado
2. Revisa los permisos del rol
3. Verifica la configuración de REST API
4. Revisa el nonce en la consola

### El progreso no se guarda

1. Verifica permisos de escritura en la base de datos
2. Revisa que las tablas se hayan creado correctamente
3. Verifica que el usuario tenga el capability `cv_soc_track_progress`

## 📝 Actualización

Para actualizar el plugin:

1. **Respaldo**: Haz backup de la base de datos
2. **Desactivar**: Desactiva el plugin (no eliminar)
3. **Reemplazar**: Reemplaza los archivos del plugin
4. **Activar**: Reactiva el plugin
5. **Verificar**: Las actualizaciones de base de datos se ejecutarán automáticamente

## 🤝 Soporte

Para soporte técnico:
- **Email**: soporte@cybervaltorix.com
- **Documentación**: https://cybervaltorix.com/docs
- **GitHub**: https://github.com/cybervaltorix/soc-training-plugin

## 📄 Licencia

Este plugin está licenciado bajo GPL v2 o posterior.

```
Copyright (C) 2025 Cyber Valtorix S.A. DE C.V.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

## 🔄 Changelog

### Version 3.0.0 (2025-01-15)
- ✨ Arquitectura completamente rediseñada
- ✨ Terminales interactivas Red Team y Blue Team
- ✨ Sistema de interacciones en tiempo real
- ✨ Integración con IA para asistencia
- ✨ Sistema de badges y gamificación
- ✨ Dashboard mejorado con estadísticas
- ✨ API REST completa
- ✨ Sistema de progreso sin restricciones
- ✨ Mejoras de rendimiento y escalabilidad

### Version 2.5.0
- Primera versión pública

## 👥 Créditos

Desarrollado por el equipo de Cyber Valtorix S.A. DE C.V.

## 🎯 Roadmap

- [ ] Integración con LMS externos
- [ ] Modo multijugador en tiempo real
- [ ] Certificaciones automatizadas
- [ ] App móvil
- [ ] Integración con herramientas SOC reales
- [ ] Simulaciones de incidentes en vivo
- [ ] Analytics avanzado con ML

---

**¿Te gusta el plugin? ¡Déjanos una reseña! ⭐⭐⭐⭐⭐**
