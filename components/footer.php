<?php
function get_footer() {
    // Start output buffering
    ob_start();
    ?>
      </div>
      <!-- end page content-->
    </div>
    <!--end page content wrapper-->

    <!--start footer-->
    <footer class="footer">
      <div class="footer-text">
    Copyright © <?php echo date('Y'); ?> Lead Management CRM. All rights reserved.
      </div>
    </footer>
    <!--end footer-->

    <!--Start Back To Top Button-->
    <a href="javaScript:;" class="back-to-top">
      <ion-icon name="arrow-up-outline"></ion-icon>
    </a>
    <!--End Back To Top Button-->

    <!--start overlay-->
    <div class="overlay nav-toggle-icon"></div>
    <!--end overlay-->

  </div>
  <!--end wrapper-->

  <?php print_footer_scripts(); ?>
</body>
</html>
    <?php
    // Return the output
    return ob_get_clean();
}
?>