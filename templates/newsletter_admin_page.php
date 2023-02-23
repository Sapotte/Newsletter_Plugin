<div class="wrapper_newsletter_admin">
    <h1>Newsletter</h1>
    <div id="infos">
        <p>Vos clients peuvent s'inscrire à la newsletter via le widget "Newsletter"</p>
        <p>Pour intégrer une possibilité de s'abonner sur vos formulaires de contact (uniquement avec Contact Form 7):
            <ul>
                <li>insérez <strong style="color: red;"> [acceptance newsletter_subscribe optional]S'abonner à la newsletter [/acceptance]</strong>  à votre formulaire</li>
                <li>vérifiez bien que le nom de l'input du mail soit "your-email"</li>
            </ul>
        </p>
    </div>
    <div id="mail">
        <h2>La newsletter</h2>
        <form enctype="multipart/form-data" action="?page=Newsletter_Iltordello.php&action=sendMail" method="post">
            <div class="input-group">
                <label for="subject">Sujet de la newsletter</label>
                <input type="text" name="subject" id="subject" required>
            </div>
            <div class="input-group">
                <label for="file">Votre fichier contenant le corps du mail (format jpeg ou png)</label>
                <input type="file" name="file" id="file" required accept="image/jpeg, image/png" onchange="preview(this.files[0])">
            </div>
            <input type="submit" name="submit" id="envoi">
        </form>
    </div>
    <div id="preview">
        <h1 id="preview-title"></h1>
        <p id="img"><img id="preview-img" alt='Newsletter Il Tordello'></p>
        <p>Venez visiter <a href=" <?php echo bloginfo('url'); ?>">notre site</a></p>
        <p ><a id="unsubscribe-link"  href="<?php echo bloginfo( 'url' );?>/unsubscribe" style='font-style:italic; font-size: 1em;'>Vous désinscrire de la Newsletter</a>
    </div>
</div>