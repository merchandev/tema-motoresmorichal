# Tema Motores Morichal - Concesionario Toyota

Tema personalizado de WordPress desarrollado exclusivamente para **Motores Morichal, C.A.**, concesionario oficial de Toyota en Maturín, Venezuela.

## Características Principales

*   **Diseño Optimizado:** UI/UX moderna enfocada en el sector automotriz (Dark Theme premium) siguiendo lineamientos visuales de Toyota.
*   **Hero Slider Dual (Desktop/Mobile):** Slider principal con soporte nativo desde el Personalizador de WordPress para cargar versiones independientes de videos e imágenes (apaisado y vertical) mejorando drásticamente el LCP y rendimiento en móviles.
*   **Catálogo de Vehículos:** Tipos de entrada personalizados (`vehiculo` y `vehiculo_usado`) con campos avanzados (ACF-like integrados en el código) para características técnicas, galería de imágenes, ficha PDF, colores disponibles y enlace a reserva.
*   **Generación SEO Multi-Idioma:** Arquitectura preparada para indexación dinámica.
*   **Formularios Nativos (AJAX):** Integración con sistema de mensajería asíncrona sin depender de plugins de terceros pesados.
*   **Performance First:** Carga condicional de librerías, sin bloqueo de renderizado, y limpieza automática de caracteres invisibles en el DOM.

## Requisitos Técnicos

*   **WordPress:** 6.0 o superior.
*   **PHP:** 7.4 o superior (Recomendado 8.0+).
*   **Plugins Recomendados:** No requiere constructores visuales pesados (Elementor/Divi). Todo está construido nativamente para máxima velocidad.

## Instalación

1. Clona o descarga este repositorio.
2. Sube la carpeta del tema a la ruta `/wp-content/themes/tema-motoresmorichal` de tu instalación de WordPress.
3. Entra al panel de administración de WordPress > **Apariencia > Temas**.
4. Activa el tema **Motores Morichal**.
5. Ve a **Apariencia > Personalizar** para configurar el Hero Slider y otros detalles de la portada.

## Estructura del Tema

*   `/assets/` - Recursos estáticos (Imágenes base, videos, y dependencias locales de CSS/JS).
*   `/inc/` - Lógica modular de PHP (Custom Post Types, metaboxes, AJAX, Customizer).
*   `/dist/` - Archivos finales compilados (si se utiliza un bundler como Vite).
*   `front-page.php` - Plantilla altamente personalizada para la página de inicio.
*   `functions.php` - Orquestador principal de las funcionalidades del tema.
*   `style.css` - Hoja de estilos principal y declaración del tema.

---

### Desarrollo y Créditos

Diseñado y desarrollado por **[Merchan.Dev](https://github.com/merchandev)**.
*Transformando ideas en experiencias digitales de alto impacto.*
