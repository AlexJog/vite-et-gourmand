<?php error_log("FOOTER included from: " . (__FILE__) . " | caller: " . ($_SERVER['SCRIPT_NAME'] ?? 'cli')); ?>

<footer class="site-footer">
  <div class="div-footer">
    <!-- HORAIRES -->
    <div>
      <h3>Horaires d'ouverture</h3>
      <p>Lundi - Vendredi : 9h - 18h</p>
      <p>Samedi : 10h - 16h</p>
      <p>Dimanche : Fermé</p>
    </div>

    <!-- CONTACT -->
    <div>
      <h3>Contact</h3>
      <p>📧 contact@vitegourmand.fr</p>
      <p>📞 05 56 00 00 00</p>
      <p>📍 Bordeaux, France</p>
    </div>

    <!-- INFORMATIONS LÉGALES -->
    <div>
      <h3>Informations légales</h3>
      <ul>
        <li><a href="#">Mentions légales</a></li>
        <li><a href="#">Conditions générales de vente</a></li>
        <li><a href="#">Politique de confidentialité</a></li>
      </ul>
    </div>
  </div>

  <p class="copyright">© 2025 Vite & Gourmand – Tous droits réservés</p>
</footer>

<script src="<?= BASE_URL ?>assets/js/main.js?v=<?= time() ?>"></script>
</body>
</html>
