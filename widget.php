<?php

class jrv_widget extends WP_Widget {
    public function __construct()
    {
        parent::__construct('jrv_widget', __('JD\'s Recently Viewed Pages', 'jrv_widget_domain'), [
            'description' => __('Shows the recently viewed pages in a convenient widget', 'jrv_widget_domain')
        ]);
    }

    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];

        if(!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        // actual output
        $pagelist = jrv_lastviewed_repo::getRecentlyViewed();
        if(count($pagelist) > 0) {
            echo '<ul>';

            foreach($pagelist as $page) {
                $post = get_post($page);
                printf('<li title="%s"><a href="%s">%s</a></li>', get_the_title($post), get_permalink($post), get_the_title($post));
            }

            echo '</ul>';
        }
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        if(isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Title', 'jrv_widget_domain');
        }

        // admin form
        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input
                class="widefat" 
                id="<?php echo $this->get_field_id( 'title' ); ?>"
                name="<?php echo $this->get_field_name( 'title' ); ?>"
                type="text"
                value="<?php echo esc_attr( $title ); ?>"
            />
        </p>

        <?php
    }

    public function update($new, $old) {
        return [
            'title' => !empty($new['title']) ? strip_tags($new['title']) : (!empty($old['title']) ? $old['title'] : '')
        ];
    }
}