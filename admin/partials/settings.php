<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/brayan2rincon
 * @since      1.0.0
 *
 * @package    Wc_Payme_Whatsapp
 * @subpackage Wc_Payme_Whatsapp/admin/partials
 */
?>

<?php if (! current_user_can ('administrator')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.')); ?>

<?php

    if(isset($_POST['woows_phone'])){
        update_option('woows_phone', $_POST['woows_phone']);
    }

?>

<div class="wrap">
    <h2><?php _e( 'Settings', 'wc-payme-whatsapp' ) ?></h2> 
    <br/>
    <form method="post">

        <div>
            <label for="woows_phone"><?=__('Phone')?></label>
            <input 
                type="tel" 
                id="woows_phone" 
                name="woows_phone" 
                value="<?=get_option('woows_phone')?>" 
            /> <br/> <br/>
        </div>

    </form>
</div>