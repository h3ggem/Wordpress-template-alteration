<?php

namespace CPT;

class badge
{
    private $type               = 'Badge';
    private $slug               = 'badges';
    private $name               = 'Badges';
    private $singular_name      = 'badge';
    /**
     * Register post type
     */
    public function register()
    {

        $labels = array(
            'name'                  => _x($this->name, 'Post Type General Name', 'mp'),
            'singular_name'         => _x($this->singular_name, 'Post Type Singular Name', 'mp'),
            'menu_name'             => __($this->name, 'mp'),
            'name_admin_bar'        => __($this->name, 'mp'),
            'archives'              => __($this->name . ' Archives', 'mp'),
            'attributes'            => __($this->singular_name . ' Attributes', 'mp'),
            'parent_item_colon'     => __('Parent Item:', 'mp'),
            'all_items'             => __('All ' . $this->name, 'mp'),
            'add_new_item'          => __('Add New ' . $this->singular_name, 'mp'),
            'add_new'               => __('Add New', 'mp'),
            'new_item'              => __('New ' . $this->singular_name, 'mp'),
            'edit_item'             => __('Edit ' . $this->singular_name, 'mp'),
            'update_item'           => __('Update ' . $this->singular_name, 'mp'),
            'view_item'             => __('View ' . $this->singular_name, 'mp'),
            'view_items'            => __('View ' . $this->name, 'mp'),
            'not_found'             => __('Not found', 'mp'),
            'search_items'          => __('Search ' . strtolower($this->name), 'mp'),
            'not_found_in_trash'    => __('Not found in Trash', 'mp'),
            'featured_image'        => __('Featured Image', 'mp'),
            'set_featured_image'    => __('Set featured image', 'mp'),
            'remove_featured_image' => __('Remove featured image', 'mp'),
            'use_featured_image'    => __('Use as featured image', 'mp'),
            'insert_into_item'      => __('Insert into', 'mp'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'mp'),
            'items_list'            => __($this->name . ' list', 'mp'),
            'items_list_navigation' => __($this->name . ' list navigation', 'mp'),
            'filter_items_list'     => __('Filter ' . $this->name . ' list', 'mp'),
        );
        $rewrite = array(
            'slug'                  => $this->slug,
            'pages'                 => true,
            'with_front'            => true,
            'feeds'                 => true,
        );
        $args = array(
            'label'                 => __($this->singular_name, 'mp'),
            'description'           => __($this->name, 'mp'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'revisions', 'custom-fields'),
            'taxonomies'            => array('category', 'post_tag'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 10,
            'menu_icon'             => 'dashicons-welcome-learn-more',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'rewrite'               => $rewrite,
            'capability_type'       => 'page',
        );
        register_post_type($this->type, $args);
    }
    public function metabox()
    {
        //Register meta box for custom post types
        add_meta_box(
            'badge-mb',            // Unique ID
            'Additional Information',   // Box title
            array($this, 'badge_metabox'),       // Content callback, must be of type callable
            'badge'                // Post type
        );
    }

    // Meta box logic
    public function badge_metabox()
    {
        global $post;
?>
        <div class="badge_box">
            <style scoped>
                .badge_box {
                    display: grid;
                    grid-template-columns: max-content 1fr;
                    grid-row-gap: 10px;
                    grid-column-gap: 20px;
                }

                .badge_field {
                    display: contents;
                }
            </style>
            <p class="meta-options badge_field">
                <label for="badge_url">URL</label>
                <input id="badge_url" type="url" name="badge_url" value="<?= get_post_meta($post->ID, 'badge_url', true) ?? '' ?>">
            </p>
            <p class="meta-options badge_field">
                <label for="show_main">Show On Main Page</label>
                <input id="show_main" type="checkbox" name="show_main" value="show" <?= get_post_meta($post->ID, 'show_main', true) == 'show' ? 'checked' : '' ?>>
            </p>
            <p class="meta-options badge_field">
                <label for="order_main">Order on Main Page</label>
                <input id="order_main" type="number" min="0" name="order_main" value="<?= get_post_meta($post->ID, 'order_main', true) ?? '5' ?>">
            </p>
        </div>
<?php
    }

    public function badge_save_metabox($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if ($parent_id = wp_is_post_revision($post_id)) {
            $post_id = $parent_id;
        }

        $fields = [
            'badge_url',
            'order_main',
            'show_main'
        ];

        if (!isset($_POST['show_main'])) {
            update_post_meta($post_id, 'show_main', false);
        }

        foreach ($fields as $field) {
            if (array_key_exists($field, $_POST)) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
    /**
     * Event constructor.
     *
     * When class is instantiated
     */
    public function __construct()
    {

        // Register the post type
        add_action('init', array($this, 'register'));
        // Register Meta box for custom fields
        add_action('add_meta_boxes', array($this, 'metabox'));
        // Add save post function for this specific field
        add_action('save_post', array($this, 'badge_save_metabox'));
    }
}
