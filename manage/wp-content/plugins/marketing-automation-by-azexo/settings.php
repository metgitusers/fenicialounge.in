<?php
if (!function_exists('azh_settings_sanitize_callback')) {

    function azh_settings_sanitize_callback($input) {
        $input = apply_filters('azh_settings_sanitize_callback', $input);
        return $input;
    }

}

if (!function_exists('azh_textfield')) {

    function azh_textfield($args) {
        extract($args);
        $option = 'azh-settings';
        if (isset($_GET['page'])) {
            $option = sanitize_text_field($_GET['page']);
        }
        $settings = get_option($option);
        if (isset($default) && !isset($settings[$id])) {
            $settings[$id] = $default;
        }
        if (!isset($type)) {
            $type = 'text';
        }
        ?>
        <input type="<?php print esc_attr($type); ?>" name="<?php print $option; ?>[<?php print esc_attr($id); ?>]" value="<?php print esc_attr($settings[$id]); ?>">
        <p>
            <em>
        <?php if (isset($desc)) print $desc; ?>
            </em>
        </p>
        <?php
    }

}

if (!function_exists('azh_textarea')) {

    function azh_textarea($args) {
        extract($args);
        $option = 'azh-settings';
        if (isset($_GET['page'])) {
            $option = sanitize_text_field($_GET['page']);
        }
        $settings = get_option($option);
        if (isset($default) && !isset($settings[$id])) {
            $settings[$id] = $default;
        }
        ?>
        <textarea name="<?php print $option; ?>[<?php print esc_attr($id); ?>]" cols="50" rows="5"><?php print esc_attr($settings[$id]); ?></textarea>
        <p>
            <em>
        <?php if (isset($desc)) print $desc; ?>
            </em>
        </p>
        <?php
    }

}

if (!function_exists('azh_checkbox')) {

    function azh_checkbox($args) {
        extract($args);
        $option = 'azh-settings';
        if (isset($_GET['page'])) {
            $option = sanitize_text_field($_GET['page']);
        }
        $settings = get_option($option);
        if (isset($default) && !isset($settings[$id])) {
            $settings[$id] = $default;
        }
        foreach ($options as $value => $label) {
            ?>
            <div>
                <input id="<?php print esc_attr($id) . '-' . esc_attr($value); ?>" type="checkbox" name="<?php print $option; ?>[<?php print esc_attr($id); ?>][<?php print esc_attr($value); ?>]" value="1" <?php @checked($settings[$id][$value], 1); ?>>
                <label for="<?php print esc_attr($id) . '-' . esc_attr($value); ?>"><?php print esc_html($label); ?></label>
            </div>
            <?php
        }
        ?>
        <p>
            <em>
        <?php if (isset($desc)) print $desc; ?>
            </em>
        </p>
        <?php
    }

}

if (!function_exists('azh_select')) {

    function azh_select($args) {
        extract($args);
        $option = 'azh-settings';
        if (isset($_GET['page'])) {
            $option = sanitize_text_field($_GET['page']);
        }
        $settings = get_option($option);
        if (isset($default) && !isset($settings[$id])) {
            $settings[$id] = $default;
        }
        ?>
        <select name="<?php print $option; ?>[<?php print esc_attr($id); ?>]">
            <?php
            foreach ($options as $value => $label) {
                ?>
                <option value="<?php print esc_attr($value); ?>" <?php @selected($settings[$id], $value); ?>><?php print esc_html($label); ?></option>
                <?php
            }
            ?>
        </select>
        <p>
            <em>
        <?php if (isset($desc)) print $desc; ?>
            </em>
        </p>
        <?php
    }

}

if (!function_exists('azh_radio')) {

    function azh_radio($args) {
        extract($args);
        $option = 'azh-settings';
        if (isset($_GET['page'])) {
            $option = sanitize_text_field($_GET['page']);
        }
        $settings = get_option($option);
        if (isset($default) && !isset($settings[$id])) {
            $settings[$id] = $default;
        }
        ?>
        <div>
            <?php
            foreach ($options as $value => $label) {
                ?>
                <input id="<?php print esc_attr($id) . esc_attr($value); ?>" type="radio" name="<?php print $option; ?>[<?php print esc_attr($id); ?>]" value="<?php print esc_attr($value); ?>" <?php @checked($settings[$id], $value); ?>>
                <label for="<?php print esc_attr($id) . esc_attr($value); ?>"><?php print esc_html($label); ?></label>
                <?php
            }
            ?>
        </div>
        <p>
            <em>
        <?php if (isset($desc)) print $desc; ?>
            </em>
        </p>
        <?php
    }

}

