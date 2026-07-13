# Documentación del Proyecto: Toyota Monagas

**Versión:** 1.1.6  
**Autor:** Merchan.Dev & Espressivo Venezuela  
**Tipo:** Tema de WordPress (Custom Theme)

## 📌 Descripción General
Este proyecto es un tema personalizado de WordPress desarrollado para **Toyota Monagas**. Utiliza un stack moderno híbrido que combina la arquitectura tradicional de PHP de WordPress con herramientas de desarrollo frontend modernas como **Vite** y **Tailwind CSS**.

El tema está diseñado para ser rápido, modular y fácil de mantener, con una estructura clara de archivos y funciones encapsuladas.

---

## 🛠 Requisitos del Sistema

### Entorno de Desarrollo
- **PHP**: 7.4 o superior (Compatible con 8.x).
- **Node.js**: v16+ (Recomendado v18+).
- **WordPress**: 6.0 o superior.
- **Composer** (Opcional, si el proyecto maneja dependencias PHP).

---

## 🚀 Instalación y Configuración

1. **Clonar el Repositorio / Copiar Archivos**:
   Coloca la carpeta del tema `TEMA OFICIAL - MOTORES MORICHAL` dentro del directorio de temas de WordPress:
   `/wp-content/themes/`

2. **Instalar Dependencias Frontend**:
   Navega a la raíz del tema desde la terminal y ejecuta:
   ```bash
   npm install
   ```
   Esto instalará Vite, Tailwind CSS, PostCSS y otras herramientas de desarrollo definidas en `package.json`.

3. **Iniciar Servidor de Desarrollo**:
   Para trabajar en los estilos y scripts con recarga en caliente (HMR):
   ```bash
   npm run dev
   ```

4. **Compilar para Producción**:
   Cuando e desarrollo esté listo para producción, genera los archivos optimizados:
   ```bash
   npm run build
   ```

---

## 📂 Estructura del Proyecto

### Directorios Principales
- **`/inc`**: El "corazón" lógico del tema. Aquí se encuentran todas las funcionalidades PHP separadas por módulos.
  - `setup.php`: Configuración inicial del tema (scripts, soportes).
  - `cpt.php`: Definición de Custom Post Types (Coches, Repuestos, etc.).
  - `metaboxes.php`: Campos personalizados para los posts.
  - `ajax.php`: Manejadores de peticiones AJAX.
  - `contact-system.php`: Lógica de formularios y envío de correos (SMTP).
  - `shortcodes.php`: Shortcodes personalizados.
  - `admin-tools.php` / `permissions.php`: Herramientas y permisos de administrador.

- **`/assets`**: Archivos estáticos públicos (imágenes, fuentes, librerías compiladas).
- **`/src`**: Código fuente frontend (TypeScript/JavaScript, SCSS/CSS) que Vite procesa.
- **`/template-parts`**: Fragmentos de plantillas reutilizables (headers, loops de posts, cards).

### Archivos Clave en la Raíz
- **`functions.php`**: Punto de entrada principal. Se encarga de cargar (require) todos los módulos de la carpeta `/inc`.
- **`style.css`**: Hoja de estilos principal declarativa del tema (meta-información de WordPress y estilos base).
- **`tailwind.config.js`**: Configuración del sistema de diseño (colores corporativos, fuentes, rutas de purga).
- **`vite.config.js`**: Configuración del empaquetador Vite.
- **`front-page.php`**: Plantilla de la página de inicio.
- **`single*.php`**: Plantillas para visualización individual de posts y vehículos.

---

## 🎨 Desarrollo Frontend (Tailwind + Vite)

El estilo visual se maneja principalmente a través de **Tailwind CSS**.
- **Colores Personalizados**:
  - `toyota-red` (#EB0A1E)
  - `toyota-black` (#0A0A0A)
- **Fuentes**: Inter (definida en las variables CSS).

Los estilos se compilan y minifican automáticamente al ejecutar `npm run build`.

---

## 🔧 Funcionalidades Destacadas

### 1. Sistema de Vehículos (Custom Post Types)
El tema maneja catálogos de vehículos (nuevos y usados) mediante CPTs definidos en `inc/cpt.php`. Cada vehículo tiene campos personalizados (metaboxes) gestionados en `inc/metaboxes.php` para especificaciones técnicas, precios, galería, etc.

### 2. Sistema de Contacto Integrado
En lugar de depender de plugins pesados, el tema incluye su propio manejador de contactos en `inc/contact-system.php`. Gestiona la validación, el guardado de "Leads" en base de datos y el envío de notificaciones por correo.

### 3. Permisos de Administrador
El archivo `inc/permissions.php` asegura que los administradores mantengan acceso total, previniendo bloqueos accidentales de capacidades.

---

## 📝 Notas de Mantenimiento

- **Actualizar Estilos**: Siempre modifica los estilos a través de las clases de Tailwind o en los archivos CSS fuente, luego ejecuta `npm run build`.
- **Depuración**: El archivo `functions.php` suprime errores en el frontend por defecto (`display_errors`, 0) para producción. Para depurar, cambia esto temporalmente o revisa el `debug.log` de WordPress.
