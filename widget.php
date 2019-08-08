<?php

class jrv_widget extends WP_Widget
{
    protected $defaults = [
        'title'  => 'Title',
        'amount' => 5,
    ];

    public function __construct()
    {
        parent::__construct('jrv_widget', __('JD\'s Recently Viewed Pages', 'jrv_widget_domain'), [
            'description' => __('Shows the recently viewed pages in a convenient widget', 'jrv_widget_domain'),
        ]);

        $this->defaults['title'] = __('Title', 'jrv_widget_domain');
    }

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];

        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        // actual output
        $pagelist = jrv_lastviewed_repo::getRecentlyViewed();
        if (count($pagelist) > 0) {
            $pagelist = array_slice($pagelist, 0 - $instance['amount']);

            echo '<ul>';

            foreach ($pagelist as $page) {
                $post = get_post($page[0]);
                printf('<li title="%s"><a href="%s">%s</a></li>', get_the_title($post), get_permalink($post), get_the_title($post));
            }

            echo '</ul>';
        }

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $options = array_merge($this->defaults, $instance)

        // admin form
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input
                class="widefat"
                id="<?php echo $this->get_field_id('title'); ?>"
                name="<?php echo $this->get_field_name('title'); ?>"
                type="text"
                value="<?php echo esc_attr($options['title']); ?>"
            />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('amount'); ?>"><?php _e('Amount:'); ?></label>
            <input
                class="widefat"
                id="<?php echo $this->get_field_id('amount'); ?>"
                name="<?php echo $this->get_field_name('amount'); ?>"
                type="number"
                value="<?php echo esc_attr($options['amount']); ?>"
            />
        </p>

        <?php
    }

    public function update($new, $old)
    {
        if (isset($new['amount'])) {
            $new['amount'] = min(25, intval($new['amount'], 10));
        }

        return [
            'title'  => !empty($new['title']) ? strip_tags($new['title']) : (!empty($old['title']) ? $old['title'] : $this->defaults['title']),
            'amount' => !empty($new['amount']) ? intval($new['amount'], 10) : (!empty($old['amount']) ? $old['amount'] : $this->defaults['amount']),
        ];
    }
}
