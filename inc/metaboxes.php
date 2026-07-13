<?php
/**
 * Metaboxes for Vehicle Data
 */

// ---------------------------------------------
// Metabox: Categorías de vehículo (Single Select)
// ---------------------------------------------
add_action('add_meta_boxes', function(){
    add_meta_box('vehiculo_categoria_select', 'Categorías de vehículo', 'toyota_m_categoria_select', 'vehiculo', 'side', 'default');
    add_meta_box('vehiculo_categoria_select', 'Categorías de vehículo usado', 'toyota_m_categoria_select', 'vehiculo_usado', 'side', 'default');
});

// Category select metabox (single select for vehiculo_categoria)
function toyota_m_categoria_select($post){
  wp_nonce_field('vehiculo_categoria_save','vehiculo_categoria_nonce');
  $terms = wp_get_post_terms($post->ID, 'vehiculo_categoria', array('fields'=>'ids'));
  $selected = (!empty($terms) && is_array($terms)) ? intval($terms[0]) : 0;
  $args = array(
    'taxonomy'        => 'vehiculo_categoria',
    'hide_empty'      => false,
    'name'            => 'veh_categoria_select',
    'id'              => 'veh_categoria_select',
    'orderby'         => 'name',
    'selected'        => $selected,
    'show_option_none'=> '-- Seleccionar categoría --',
  );
  echo '<div style="padding:6px;">';
  wp_dropdown_categories($args);
  echo '<p class="howto">Selecciona la categoría a la que pertenece este vehículo.</p>';
  echo '</div>';
}

// Save category meta
add_action('save_post', function($post_id){
    // Save selected category from our dropdown (single-select)
    if (isset($_POST['vehiculo_categoria_nonce']) && wp_verify_nonce($_POST['vehiculo_categoria_nonce'], 'vehiculo_categoria_save')){
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        if (isset($_POST['veh_categoria_select']) && $_POST['veh_categoria_select'] !== ''){
            $term_id = intval($_POST['veh_categoria_select']);
            if ($term_id > 0) {
                wp_set_object_terms($post_id, array($term_id), 'vehiculo_categoria');
            }
        } else {
            // Clear terms if none selected
            wp_set_object_terms($post_id, array(), 'vehiculo_categoria');
        }
    }
});


// ---------------------------------------------
// ADMIN: Custom Metabox for Vehicle Data (Colors, Images, etc.)
// ---------------------------------------------
add_action('add_meta_boxes', function() {
    add_meta_box('toyota_vehiculo_data', 'Datos del Vehículo (Configuración)', 'toyota_render_vehiculo_data', array('vehiculo', 'vehiculo_usado'), 'normal', 'high');
});

function toyota_render_vehiculo_data($post) {
    // Retrieve existing data
    $sub      = get_post_meta($post->ID, 'veh_subtitulo', true);
    $ver      = get_post_meta($post->ID, 'veh_version', true);
    $pr       = get_post_meta($post->ID, 'veh_precio', true);
    $fich_id  = get_post_meta($post->ID, 'veh_ficha_id', true);
    $fich_url = $fich_id ? wp_get_attachment_url((int)$fich_id) : get_post_meta($post->ID, 'veh_ficha_url', true);
    $legal    = get_post_meta($post->ID, 'veh_legal_url', true);
    
    // Colors is a JSON array
    $cols = get_post_meta($post->ID, 'veh_colores', true);
    if (!is_array($cols)) { 
        $decoded = json_decode((string)$cols, true);
        $cols = is_array($decoded) ? $decoded : array();
    }
    $gallery = get_post_meta($post->ID, 'veh_galeria', true);
    if (!is_array($gallery)) { 
        $decoded_gallery = json_decode((string)$gallery, true);
        $gallery = is_array($decoded_gallery) ? $decoded_gallery : array();
    }
    
    wp_nonce_field('toyota_veh_data_save', 'toyota_veh_data_nonce');
    ?>
    <style>
        .tvd-row { margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .tvd-row label { width: 120px; font-weight: 600; }
        .tvd-row input[type="text"] { width: 100%; max-width: 400px; }
        .tvd-colors-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .tvd-colors-table th, .tvd-colors-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .tvd-colors-table th { background: #f9f9f9; }
        .color-preview { display: inline-block; width: 24px; height: 24px; border: 1px solid #ccc; vertical-align: middle; margin-right: 5px; border-radius: 50%; }
        .img-preview { max-width: 50px; max-height: 50px; display: block; margin-top: 5px; }
        .button-remove-color { color: #a00; cursor: pointer; }
        .veh-gallery-list { display: flex; flex-direction: column; gap: 12px; margin-top: 12px; }
        .veh-gallery-item { display: flex; gap: 12px; align-items: center; border: 1px solid #ddd; padding: 10px; border-radius: 6px; background: #fafafa; }
        .veh-gallery-item .vg-preview { width: 90px; height: 90px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #fff; border-radius: 4px; }
        .veh-gallery-item .vg-preview img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .vg-placeholder { font-size: 12px; color: #777; text-align: center; padding: 6px; }
        .veh-gallery-item .vg-fields { flex: 1; display: flex; gap: 10px; align-items: center; }
        .veh-gallery-item .vg-fields input[type="text"] { width: 100%; }
    </style>
    
    <div class="tvd-wrap">
      <div class="tvd-row">
        <label>Subtitulo / Ano:</label>
        <input type="text" name="veh_subtitulo" value="<?php echo esc_attr($sub); ?>" placeholder="Ej: 2025 Toyota Yaris">
      </div>
      <div class="tvd-row">
        <label>Version:</label>
        <input type="text" name="veh_version" value="<?php echo esc_attr($ver); ?>" placeholder="Ej: GLS Automatico">
      </div>
      <div class="tvd-row">
        <label>Precio ($):</label>
        <input type="text" name="veh_precio" value="<?php echo esc_attr($pr); ?>" placeholder="Ej: 25.000">
      </div>
      <div class="tvd-row">
        <label>Ficha tecnica (PDF):</label>
        <input type="hidden" name="veh_ficha_id" id="veh_ficha_id" value="<?php echo esc_attr($fich_id); ?>">
        <input type="hidden" name="veh_ficha_url" id="veh_ficha_url" value="<?php echo esc_url($fich_url); ?>">
        <button type="button" class="button" id="veh_ficha_btn">Seleccionar PDF</button>
        <span id="veh_ficha_name" style="font-size:12px;color:#555;">
          <?php echo $fich_url ? esc_html(basename($fich_url)) : 'Ningun archivo seleccionado'; ?>
        </span>
        <button type="button" class="button button-link-delete" id="veh_ficha_clear" style="display:<?php echo $fich_url ? 'inline-block' : 'none'; ?>;">Quitar</button>
      </div>
      <div class="tvd-row">
        <label>URL Legal:</label>
        <input type="text" name="veh_legal_url" value="<?php echo esc_attr($legal); ?>" placeholder="https://...">
      </div>

      <hr>
      <h3>Colores e Imagenes</h3>
      <p>Agrega los colores disponibles. La primera imagen será la predeterminada.</p><table class="tvd-colors-table" id="tvd-colors-list">
                <thead>
                    <tr>
                        <th style="width: 20px;">#</th>
                        <th>Nombre Color</th>
                        <th>Hex Color</th>
                        <th>Imagen del Auto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(empty($cols)) { $cols = array(array('nombre'=>'','hex'=>'','img'=>'','img_id'=>'')); }
                    foreach($cols as $i => $c): 
                        $c_nombre = isset($c['nombre']) ? $c['nombre'] : '';
                        $c_hex    = isset($c['hex']) ? $c['hex'] : '#ffffff';
                        $c_img    = isset($c['img']) ? $c['img'] : '';
                        $c_img_id = isset($c['img_id']) ? $c['img_id'] : '';
                    ?>
                    <tr class="tvd-color-row">
                        <td class="row-index"><?php echo $i+1; ?></td>
                        <td><input type="text" name="veh_colores[<?php echo $i; ?>][nombre]" value="<?php echo esc_attr($c_nombre); ?>" placeholder="Ej: Blanco Perlado" /></td>
                        <td>
                            <input type="text" class="color-field" name="veh_colores[<?php echo $i; ?>][hex]" value="<?php echo esc_attr($c_hex); ?>" data-default-color="#ffffff" />
                        </td>
                        <td>
                            <input type="hidden" class="img-id" name="veh_colores[<?php echo $i; ?>][img_id]" value="<?php echo esc_attr($c_img_id); ?>" />
                            <input type="hidden" class="img-url" name="veh_colores[<?php echo $i; ?>][img]" value="<?php echo esc_attr($c_img); ?>" />
                            <button type="button" class="button upload-img-btn">Elegir Imagen</button>
                            <div class="preview-area">
                                <?php if($c_img): ?><img src="<?php echo esc_url($c_img); ?>" class="img-preview" /><?php endif; ?>
                            </div>
                        </td>
                        <td><button type="button" class="button button-small button-remove-color">Eliminar</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><button type="button" class="button button-primary" id="add-color-row">Agregar Color</button></p>

            <hr>
            <h3>Galería de fotos</h3>
            <p>Agrega fotos extra que se mostrarán en la página del vehículo y se abrirán en lightbox.</p>
            <div id="veh-gallery-list" class="veh-gallery-list">
                <?php 
                if(empty($gallery)) { $gallery = array(); }
                foreach($gallery as $i => $g): 
                    $gid  = isset($g['id']) ? intval($g['id']) : 0;
                    $gurl = $gid ? wp_get_attachment_image_url($gid, 'medium') : '';
                    if (!$gurl && !empty($g['url'])) { $gurl = esc_url($g['url']); }
                    $galt = isset($g['alt']) ? $g['alt'] : '';
                ?>
                <div class="veh-gallery-item">
                    <div class="vg-preview">
                        <?php if($gurl): ?><img src="<?php echo esc_url($gurl); ?>" alt=""><?php else: ?><div class="vg-placeholder">Sin imagen</div><?php endif; ?>
                        <input type="hidden" name="veh_galeria[<?php echo $i; ?>][id]" value="<?php echo esc_attr($gid); ?>">
                        <input type="hidden" name="veh_galeria[<?php echo $i; ?>][url]" value="<?php echo esc_url($gurl); ?>">
                    </div>
                    <div class="vg-fields">
                        <input type="text" name="veh_galeria[<?php echo $i; ?>][alt]" value="<?php echo esc_attr($galt); ?>" placeholder="Alt / descripcion (opcional)">
                        <button type="button" class="button-link-delete veh-gallery-remove">Quitar</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <p><button type="button" class="button" id="veh-gallery-add">Agregar imágenes</button></p>
        </div>

    <script>
    jQuery(document).ready(function($){
        // Init color picker
        function initColorPicker(scope) {
            scope.find('.color-field').wpColorPicker();
        }
        initColorPicker($('#tvd-colors-list'));

        // Sortable (optional, requires jquery-ui-sortable)
        // $('#tvd-colors-list tbody').sortable();

        // Add Row
        $('#add-color-row').on('click', function(){
            var rowCount = $('#tvd-colors-list tbody tr').length;
            var newRow = `
                <tr class="tvd-color-row">
                    <td class="row-index">${rowCount + 1}</td>
                    <td><input type="text" name="veh_colores[${rowCount}][nombre]" placeholder="Ej: Nuevo Color" /></td>
                    <td><input type="text" class="color-field" name="veh_colores[${rowCount}][hex]" value="#ffffff" /></td>
                    <td>
                        <input type="hidden" class="img-id" name="veh_colores[${rowCount}][img_id]" />
                        <input type="hidden" class="img-url" name="veh_colores[${rowCount}][img]" />
                        <button type="button" class="button upload-img-btn">Elegir Imagen</button>
                        <div class="preview-area"></div>
                    </td>
                    <td><button type="button" class="button button-small button-remove-color">Eliminar</button></td>
                </tr>
            `;
            var $row = $(newRow);
            $('#tvd-colors-list tbody').append($row);
            initColorPicker($row);
        });

        // Remove Row
        $(document).on('click', '.button-remove-color', function(){
            $(this).closest('tr').remove();
            // Re-index could be done here but WP handles array keys on save usually fine if we don't mind gaps, 
            // but strictly we should re-index names. For simplicity, we rely on PHP array_values on save.
        });

        // Media Uploader
        $(document).on('click', '.upload-img-btn', function(e){
            e.preventDefault();
            var btn = $(this);
            var row = btn.closest('td');
            var frame = wp.media({
                title: 'Seleccionar Imagen del Vehículo',
                button: { text: 'Usar esta imagen' },
                multiple: false
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                row.find('.img-id').val(attachment.id);
                row.find('.img-url').val(attachment.url);
                row.find('.preview-area').html('<img src="'+attachment.url+'" class="img-preview"/>');
            });
            frame.open();
        });

        // PDF uploader for ficha tecnica
        $('#veh_ficha_btn').on('click', function(e){
            e.preventDefault();
            var frame = wp.media({
                title: 'Seleccionar ficha técnica (PDF)',
                button: { text: 'Usar este PDF' },
                multiple: false,
                library: { type: 'application/pdf' }
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#veh_ficha_id').val(attachment.id);
                $('#veh_ficha_url').val(attachment.url);
                $('#veh_ficha_name').text(attachment.filename);
                $('#veh_ficha_clear').show();
            });
            frame.open();
        });

        $('#veh_ficha_clear').on('click', function(e){
            e.preventDefault();
            $('#veh_ficha_id').val('');
            $('#veh_ficha_url').val('');
            $('#veh_ficha_name').text('Ningun archivo seleccionado');
            $(this).hide();
        });

        // Galería de vehículo
        var $galleryList = $('#veh-gallery-list');
        function renumberGallery(){
            $galleryList.find('.veh-gallery-item').each(function(idx){
                $(this).find('input[name^="veh_galeria["]').each(function(){
                    var name = $(this).attr('name');
                    var newName = name.replace(/veh_galeria\[\d+\]/, 'veh_galeria['+idx+']');
                    $(this).attr('name', newName);
                });
            });
        }
        function addGalleryRow(data){
            var idx = $galleryList.find('.veh-gallery-item').length;
            var url = data.url || '';
            var alt = data.alt || '';
            var id  = data.id  || '';
            var html = `
                <div class="veh-gallery-item">
                    <div class="vg-preview">
                        ${url ? `<img src="${url}" alt=""/>` : '<div class="vg-placeholder">Sin imagen</div>'}
                        <input type="hidden" name="veh_galeria[${idx}][id]" value="${id}">
                        <input type="hidden" name="veh_galeria[${idx}][url]" value="${url}">
                    </div>
                    <div class="vg-fields">
                        <input type="text" name="veh_galeria[${idx}][alt]" value="${alt}" placeholder="Alt / descripcion (opcional)">
                        <button type="button" class="button-link-delete veh-gallery-remove">Quitar</button>
                    </div>
                </div>
            `;
            $galleryList.append(html);
        }

        $('#veh-gallery-add').on('click', function(e){
            e.preventDefault();
            var frame = wp.media({
                title: 'Seleccionar imágenes',
                button: { text: 'Usar estas imágenes' },
                multiple: true
            });
            frame.on('select', function(){
                var selection = frame.state().get('selection');
                selection.each(function(att){
                    var data = att.toJSON();
                    addGalleryRow({
                        id: data.id || '',
                        url: data.url || '',
                        alt: data.alt || data.title || ''
                    });
                });
            });
            frame.open();
        });

        $(document).on('click', '.veh-gallery-remove', function(e){
            e.preventDefault();
            $(this).closest('.veh-gallery-item').remove();
            renumberGallery();
        });
    });
    </script>
    <?php
}

// Save Meta
add_action('save_post', function($post_id){
    if (!isset($_POST['toyota_veh_data_nonce']) || !wp_verify_nonce($_POST['toyota_veh_data_nonce'], 'toyota_veh_data_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = array('veh_subtitulo', 'veh_version', 'veh_precio', 'veh_ficha_url', 'veh_legal_url', 'veh_ficha_id');
    foreach($fields as $f){
        if (isset($_POST[$f])) {
            if ($f === 'veh_ficha_id') {
                update_post_meta($post_id, $f, absint($_POST[$f]));
            } elseif ($f === 'veh_ficha_url') {
                update_post_meta($post_id, $f, esc_url_raw($_POST[$f]));
            } else {
                update_post_meta($post_id, $f, sanitize_text_field($_POST[$f]));
            }
        }
    }

    // Sync URL with selected PDF if an attachment ID is present
    if (!empty($_POST['veh_ficha_id'])) {
        $fid = absint($_POST['veh_ficha_id']);
        $furl = $fid ? wp_get_attachment_url($fid) : '';
        if ($furl) {
            update_post_meta($post_id, 'veh_ficha_url', esc_url_raw($furl));
        }
    }

    if (isset($_POST['veh_colores']) && is_array($_POST['veh_colores'])) {
        // Sanitize and re-index
        $clean = array();
        foreach($_POST['veh_colores'] as $c) {
            if (empty($c['nombre']) && empty($c['img'])) continue; // skip empty rows
            $clean[] = array(
                'nombre' => sanitize_text_field($c['nombre']),
                'hex'    => sanitize_hex_color($c['hex']),
                'img'    => esc_url_raw($c['img']),
                'img_id' => intval($c['img_id'])
            );
        }
        update_post_meta($post_id, 'veh_colores', $clean); // Save as array, WP serializes it
    }

    if (isset($_POST['veh_galeria']) && is_array($_POST['veh_galeria'])) {
        $gallery_clean = array();
        foreach($_POST['veh_galeria'] as $g) {
            $gid  = isset($g['id']) ? absint($g['id']) : 0;
            $gurl = !empty($g['url']) ? esc_url_raw($g['url']) : '';
            $galt = isset($g['alt']) ? sanitize_text_field($g['alt']) : '';
            if (!$gid && !$gurl) continue;
            if ($gid && !$gurl) {
                $maybe = wp_get_attachment_url($gid);
                if ($maybe) { $gurl = $maybe; }
            }
            if ($gurl) {
                $gallery_clean[] = array(
                    'id'  => $gid,
                    'url' => $gurl,
                    'alt' => $galt
                );
            }
        }
        update_post_meta($post_id, 'veh_galeria', $gallery_clean);
    } else {
        delete_post_meta($post_id, 'veh_galeria');
    }
});

// Enqueue Admin Scripts for Color Picker and Media
add_action('admin_enqueue_scripts', function($hook){
    if ('post.php' != $hook && 'post-new.php' != $hook) return;
    global $post_type;
    if ('vehiculo' == $post_type || 'vehiculo_usado' == $post_type) {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media();
    }
});
