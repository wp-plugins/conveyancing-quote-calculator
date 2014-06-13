<?php

namespace HoowlaPlugin;

class Hoowla_Plugin_Widget extends \WP_Widget 
{


    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'foo_widget', // Base ID
            __('Conveyancing Quote Button', 'text_domain'), // Name
            array( 'description' => __( 'Add a conveyancing quote calculator to your website.', 'text_domain' ), ) // Args
        );
    }


    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        $options = get_option(HOOWLA_OPTIONS_NAME);

        // Set to null if no size.
        if (!isset($options['hoowla_default_size'])) { 
            $options['hoowla_default_size'] = null; 
        }

        print_hoowla_div($options['hoowla_default_id'], $options['hoowla_default_text'], $options['hoowla_default_color'], $options['hoowla_default_size']);
    }


    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'text_domain' );
        }
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php 
    }


    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = ( !empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';


        return $instance;
    }



} // class Foo_Widget