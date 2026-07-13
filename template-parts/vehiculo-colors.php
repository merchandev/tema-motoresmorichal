<?php
/**
 * Partial: vehicle colors list
 * Usage: include locate_template('template-parts/vehiculo-colors.php');
 *
 * This file was extracted from style.css to keep CSS and PHP separate.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( have_rows( 'colores' ) ) : ?>
  <ul class="list">
    <?php while ( have_rows( 'colores' ) ) : the_row();
      $hex    = get_sub_field( 'hex' );
      $nombre = get_sub_field( 'nombre_color' );
    ?>
      <li>
        <button class="dot" style="--veh-color:<?php echo esc_attr( $hex ); ?>;" aria-label="<?php echo esc_attr( $nombre ); ?>"></button>
        <span class="color-name"><?php echo esc_html( $nombre ); ?></span>
      </li>
    <?php endwhile; ?>
  </ul>
<?php endif;

/**
 * Example usage to render interactive color dots within a vehicle block.
 * Copy the markup below into the appropriate template (e.g. single-vehiculo.php)
 * and include the partial where needed.
 *
 * <div class="vs-colors veh-dots">
 *   <div class="color-header">
 *     <span class="color-label">Cambia el color</span>
 *   </div>
 *   <?php include locate_template('template-parts/vehiculo-colors.php'); ?>
 * </div>
 */
