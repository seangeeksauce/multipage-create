<div class="wrap">
    <h2>Multipage Creation Form</h2>

    <form name="form" method="post" action="">
        <input type="hidden" name="<?php echo self::$hidden_field_name; ?>" value="1">

        <p><?php _e("Enter comma sepperated list of pages to create:", ''); ?> </p>
        <p><?php _e("Pages:", ''); ?> <input type="text" name="<?php echo self::$array_field_name; ?>" value="" size="20"></p>
        </p><hr />

        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Insert Pages') ?>" />
        </p>

        <p><?php echo self::$match_msg; ?></p>
    </form>

    <?php echo self::display_creation_info(); ?>

</div>