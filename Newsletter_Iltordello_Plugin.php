<?php

    class Newsletter_Iltordello_Plugin {
        function newsletter_install() {
            // Création de la table
            global $wpdb;
            $table_newsletter = $wpdb->prefix.'newsletter';
            if($wpdb->get_var("SHOW TABLES LIKE `$table_newsletter`")!=$table_newsletter){
                $sql = "CREATE TABLE `$table_newsletter` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `mail` TEXT NOT NULL,
                    `date_inscription` DATE NOT NULL)
                    ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                require_once(ABSPATH.'wp-admin/includes/upgrade.php');
                dbDelta( $sql );
            }

            // Creation page unsubscribe
            $unsubscribe_page = get_page_by_title( 'Se désabonner de la Newsletter');

            if($unsubscribe_page == null) {
                $nonce = wp_create_nonce( 'newsletter_unsubscribe_nonce');
                $url = admin_url( 'admin-ajax.php');
                $unsubscribe_page = array(
                    'post_title'    => 'Se désabonner de la Newsletter',
                    'post_content'  => '<p> Pour vous désabonnez de notre Newsletter, entrez le mail utilisé, validez et toutes vos données seront effacées. </p>
                                        <form action="" method="post" id="unsubscribe_form">
                                            <input type="hidden" name = "newsletter_unsubscribe_nonce" id="newsletter_unsubscribe_nonce" value="'.$nonce.'">
                                            <input type="hidden" name = "newsletter_unsubscribe_url" id="newsletter_unsubscribe_url" value="'.$url.'">
                                            <div class="input-group">
                                                <label for="mail" class="label-form">Votre email</label>
                                                <input type="email" name="mail" id="mail" class="input-form">
                                            </div>
                                            <input type="submit" name="newsletter_unsubscribe" id="newsletter_unsubscribe" class="button-primary">
                                        </form>
                                        <div id="message_desinscription"></div>',
                    'post_author'  => 1,
                    'post_status'   => 'publish',
                    'post_type'     => 'page',
                    'post_name' => 'unsubscribe'
                    ); 

                    wp_insert_post( $unsubscribe_page );
                }
            }

    

        // Suppression des abonnés au bout de 600 jours
        function delete_auto() {
            global $wpdb;
            $table_newsletter = $wpdb->prefix.'newsletter';
            $wpdb->query('DELETE FROM `'.$table_newsletter.'` WHERE DATEDIFF( NOW(), date_inscription) > 600;');
        }

        // Ajout feuille de style et scripts au head côté admin
        function newsletter_admin_header() {
            wp_register_style('newsletter_admin_css', plugins_url('css/style_admin.css', __FILE__));
            wp_enqueue_style( 'newsletter_admin_css');
            wp_enqueue_script( 'newsletter_admin_js', plugins_url('js/script_admin.js', __FILE__), array('jquery'));
        }

        // Ajout feuille de style et script au head côté front
        function newsletter_front_head() {
            wp_register_style('newsletter_front_css', plugins_url('css/style_front.css', __FILE__));
            wp_enqueue_style( 'newsletter_front_css');
            wp_enqueue_script( 'newsletter_front_js', plugins_url('js/script_front.js', __FILE__), array('jquery'));
        }

        function get_contacts_list() {
            global $wpdb;
            $table_newsletter = $wpdb->prefix.'newsletter';
            $sql ="SELECT * FROM `".$table_newsletter."`;";
            $contacts_list = $wpdb->get_results($sql);
            return $contacts_list;
        }

 


        // Page admin
        function newsletter_admin_page() {
            require_once('templates/newsletter_admin_page.php');

            if(isset($_GET['action'])){
                if($_GET['action'] == 'sendMail') {
                    if(($_POST['subject']!='') && ($_FILES['file']['tmp_name']!='')){
                        $contacts = $this->get_contacts_list();
                        $subject = $_POST['subject'];
                        $file = $_FILES['file']['tmp_name'];
                        $uid = 'template_newsletter';
                        $name = $_FILES['file']['name'];
                        global $phpmailer;
                            add_action( 'phpmailer_init', function(&$phpmailer)use($file,$uid,$name){
                                $phpmailer->SMTPKeepAlive = true;
                                $phpmailer->AddEmbeddedImage($file, $uid, $name);
                            });
                            function set_content_type() {
                                return "text/html";
                            }
                            add_filter( 'wp_mail_content_type', 'set_content_type');

                        $nbr_mails_send = 0;
                        $nbr_mails_error = 0;
                        
                        foreach ($contacts as $contact) {
                            $to = $contact->mail;
                            $message = '<!DOCTYPE html>
                            <html lang="fr">
                            <head>
                                <meta charset="UTF-8">
                                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Newsletter</title>
                            </head>
                            <body>
                                <div class= "wrapper" style="max-width: 1000px;">
                                    <h1 style="width: 100%; margin: 1em auto; text-align: center; font-size: 4em; font-family: &quot;Georgia&quot;, serif; color: #9c5220;"> Newsletter '.bloginfo('name' ).' du '.date('d-m-y').'</h1>
                                    <p style="width: 90%; margin: 1em auto;"><img src="cid:template_newsletter" alt="Newsletter Il Tordello" style="max-width: 100%; height: auto;"></p>
                                    <p style="width: 100%; margin: 1em auto; text-align: center; font-size: 3em; font-family: &quot;Georgia&quot;, serif; color: #9c5220;">Venez visiter <a href="'.get_site_url().'" style="font-weight: bold;">notre site</a></p>
                                    <p style="margin: 1em auto; text-align: center; font-style: italic; font-size: 0.8em;"><a href="'.get_site_url().'/unsubscribe" style="width: 100%;  text-align: center; font-family: &quot;Georgia&quot;, serif; color: #9c5220;">Vous désinscrire de la Newsletter</a></p>
                                </div>
                            </body>
                            </html>';
                            $headers = 'From: Il Tordello <newsletter_noreply@iltordello.fr>';
        
                            $mail_send = wp_mail( $to, $subject, $message, $headers);
                            if($mail_send) {
                                $nbr_mails_send++;
                            } else {
                                $nbr_mails_error++;
                            }
                            
                        } 
                        remove_filter('wp_mail_content_type', 'set_content_type');

                        echo $nbr_mails_send.' envoyés / '.$nbr_mails_error.' échoués';
                    } else {
                        echo '<p class="erreur">Veuillez remplir tous les champs</p>';
                    }
                };
            }
        }

        // Ajout au menu WordPress
        function newsletter_iltordello_menu() {
            $pagePlugin = add_menu_page('Newsletter', 'Newsletter', 'administrator', 'Newsletter_Iltordello.php', array($this, 'newsletter_admin_page'),'dashicons-email-alt2');
            add_action( 'load-'.$pagePlugin, array($this, 'newsletter_admin_header'), 'dashicons-email-alt2');
        }

        // Ajout contact
        function insert_contact($mail) {
            global $wpdb;
            $table_newsletter = $wpdb->prefix.'newsletter';
            $sql = "INSERT INTO `".$table_newsletter."` (`id`, `mail`, `date_inscription`) VALUES (NULL, '".$mail."', NOW() );";
            $req = $wpdb->query($sql);
            return $req;
        }

         // Supression du contact
         function delete_contact($mail) {
            global $wpdb;
            $table_newsletter = $wpdb->prefix.'newsletter';
                $sql = "DELETE FROM `".$table_newsletter."` WHERE `mail`= '".$mail."';";
                $req = $wpdb->query($sql);
                return $req;
        }

        function verifMailBD($mail) {
            global $wpdb;
            $table_newsletter = $wpdb->prefix.'newsletter';
            $sql = "SELECT * FROM `".$table_newsletter."` WHERE `mail`= '".$mail."';";
            $req = $wpdb->query($sql);
            return $req;
        }


        function newsletter_iltordello_front_ajax(){
            check_ajax_referer( 'newsletter_nonce', 'nonce');
            $verif = $this->verifMailBD($_POST['mail']);
            if($verif) {
                $message = '<span class="error"> Vous êtes déjà inscrit-e !</span>';
            } else {
                $insert = $this->insert_contact($_POST['mail']);
                if ($insert) {
                    $message = '<span class="succes"> Vous êtes inscrit-e !</span>';
                } else {
                    $message = '<span class="error"> Un erreur est survenue, veuillez réessayer</span>';
                }
            }
            echo $message;
            exit();
        }

        function newsletter_unsubscribe_front_ajax(){
            check_ajax_referer( 'newsletter_unsubscribe_nonce', 'nonce');
            $delete = $this->delete_contact($_POST['mail']);
            if ($delete) {
                $message = '<span class="succes"> Vos données ont bien été effacées</span>';
            } else {
                $message = "<span class='error'> Votre email n'est pas enregistré</span>";
            }
            echo $message;
            exit();
        }
        
       
        function create_contact_wpcf7_form($contact_form, $abort, $submission) {

                $mail = $submission->get_posted_data('your-email');

                $verif = $this->verifMailBD($mail);

                if($submission->get_posted_data('newsletter_subscribe') == '1') {
                    if($verif) {
                        echo 'Vous êtes déjà inscrit-e !';
                    } else {
                        global $wpdb;
                        $table_newsletter = $wpdb->prefix.'newsletter';
                        $sql = "INSERT INTO `".$table_newsletter."` (`id`, `mail`, `date_inscription`) VALUES (NULL, '".$mail."', NOW() );";
                        $wpdb->query($sql);
                    }
                }

                return $contact_form;

        }
        
    }

   



    
