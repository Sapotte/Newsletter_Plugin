<?php

class Newsletter_Iltordello_Widget extends WP_Widget {
    function __construct() {
        $widget_options = array(
            'classname' => 'widget-newsletter',
            'description' => "Permet l'inscription à la newsletter"
        );
        parent::__construct('widget-newsletter', 'Newsletter', $widget_options);
    }

    function form($instance){
        $defaults= array( 
            'title' => "Inscription à la newsletter",
            'button' => "S'inscrire",
            'pdc' => "#"
        );
        $instance=wp_parse_args( $instance, $defaults );
      ?>
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>">Titre :</label>
        <input 
            class="widefat"
            type="text"
            id="<?php echo $this->get_field_id('title');?>"
            name="<?php echo $this->get_field_name('title');?>">
      </p>
      <p>
        <label for="<?php echo $this->get_field_id('button'); ?>">Bouton d'inscription :</label>
        <input 
            class="widefat"
            type="text"
            id="<?php echo $this->get_field_id('button');?>"
            name="<?php echo $this->get_field_name('button');?>">
      </p>
      <p>
        <label for="<?php echo $this->get_field_id('pdc'); ?>">L'URL de votre page de politique de confidentialité :</label>
        <input 
            class="widefat"
            type="text"
            id="<?php echo $this->get_field_id('pdc');?>"
            name="<?php echo $this->get_field_name('pdc');?>"
            class="block-editor-url-input__input"
            role="combobox"
            aria-label="URL"
            aria-expanded="false"
            aria-autocomplete="list"
            aria-controls="block-editor-url-input-suggestions-0">
      </p>
      <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title']= strip_tags($new_instance['title']);
        $instance['button']= strip_tags($new_instance['button']);
        $instance['pdc']= strip_tags($new_instance['pdc']);
        $instance['title_display']= strip_tags($new_instance['title_display']);
        return $instance;
    }

    function widget($args, $instance) {
        extract ($args);
        echo $before_widget;
        ?>
        <div class="newsletter_widget">
            <?php 
                echo $before_title.$instance['title'].$after_title;
            ?>
            <form action="" method="post" class="newsletter_form">
                <input type="hidden" name="newsletter_nonce" id="newsletter_nonce" value="<?php echo wp_create_nonce('newsletter_nonce') ?>">
                <input type="hidden" name="newsletter_url" id="newsletter_url" value="<?php echo admin_url( 'admin-ajax.php') ?>">
                <input type="mail" name="mail" id="mail_newsletter">
                <input type="submit" value="<?php echo $instance['button']; ?>" id="newsletter_submit">
            </form>
            <small class="infoLeg">Il Tordello traite les données recueillies pour l'envoi de sa newsletter. En cliquant ici vous acceptez notre <a href="<?php echo $instance['pdc'] ?>">Politique de confidentialité </a></small>
            <div id="message_inscription"></div>
        </div>
        <?php
        echo $after_widget;
    }

}
