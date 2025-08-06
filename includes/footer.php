    </div>
    
    <!-- Footer CSS -->
    <link rel="stylesheet" href="/rtwrs_web/assets/css/footer.css">
    
    <!-- Artistic Footer Start -->
    <footer class="simple-footer mt-5 position-relative" style="overflow:hidden;">
      <div class="footer-art-left">
        <!-- SVG floral art left -->
        <svg fill="none" viewBox="0 0 90 90" xmlns="http://www.w3.org/2000/svg">
          <path d="M10,80 Q30,60 50,80 T90,80" stroke="#b48c5a" stroke-width="2" fill="none"/>
          <path d="M20,70 Q35,55 50,70" stroke="#b48c5a" stroke-width="1.2" fill="none"/>
          <circle cx="15" cy="75" r="3" fill="#b48c5a"/>
          <circle cx="85" cy="80" r="2" fill="#b48c5a"/>
        </svg>
      </div>
      <div class="footer-art-right">
        <!-- SVG floral art right (mirrored) -->
        <svg fill="none" viewBox="0 0 90 90" xmlns="http://www.w3.org/2000/svg">
          <path d="M10,80 Q30,60 50,80 T90,80" stroke="#b48c5a" stroke-width="2" fill="none"/>
          <path d="M20,70 Q35,55 50,70" stroke="#b48c5a" stroke-width="1.2" fill="none"/>
          <circle cx="15" cy="75" r="3" fill="#b48c5a"/>
          <circle cx="85" cy="80" r="2" fill="#b48c5a"/>
        </svg>
      </div>
      <div class="container py-4 position-relative" style="z-index:1;">
        <div class="row gy-3 align-items-start">
          <!-- Logo and Contact Details -->
          <div class="col-md-3 text-center text-md-start mb-3 mb-md-0">
            <img src="/rtwrs_web/assets/images/logo.svg" alt="Sherwani Rentals Logo" style="max-width:140px; height:auto; filter: drop-shadow(0 2px 8px #b48c5a22);">
            
            <!-- Contact Details -->
            <div class="contact-details mt-3">
              <div class="contact-item">
                <i class="fas fa-phone-alt"></i>
                <a href="tel:+919999777444" class="contact-link">+91 99997 77444</a>
              </div>
              <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <a href="mailto:hello@rameshwarwear.com" class="contact-link">hello@rameshwarwear.com</a>
              </div>
              <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <span class="contact-text">38, Royal Avenue, Pune, Maharashtra, India</span>
              </div>
            </div>
          </div>
          
          <!-- Categories -->
          <div class="col-md-2">
            <div class="footer-title">CATEGORIES</div>
            <ul class="footer-list">
              <li><a href="/rtwrs_web/index.php">Home</a></li>
              <li><a href="/rtwrs_web/rent.php">Rent</a></li>  
              <li><a href="/rtwrs_web/buy.php">Buy</a></li>
              <li><a href="/rtwrs_web/blogs.php">Blogs</a></li>
              <li><a href="/rtwrs_web/contact.php">Contact Us</a></li>
            </ul>
          </div>
          
          <!-- Rent Options -->
          <div class="col-md-2">  
            <div class="footer-title">RENT</div>
            <ul class="footer-list">
              <li><a href="/rtwrs_web/rent.php?category=sherwani">Sherwani Sets</a></li>
              <li><a href="/rtwrs_web/rent.php?category=indowestern">Indowestern</a></li>
              <li><a href="/rtwrs_web/rent.php?category=kurta">Kurta Sets</a></li>
              <li><a href="/rtwrs_web/rent.php?category=blazer">Blazers</a></li>
            </ul>
          </div>
          
          <!-- Get Updates -->
          <div class="col-md-3">
            <div class="footer-title">GET UPDATES</div>
            <p class="newsletter-text">Subscribe to get style alerts and exclusive offers!</p>
            <form class="newsletter-form" action="#" method="POST">
              <div class="input-group">
                <input type="email" name="subscribe_email" class="form-control newsletter-input" 
                       placeholder="Enter your email" required>
                <button class="btn newsletter-btn" type="submit">
                  <i class="fas fa-arrow-right"></i>
                </button>
              </div>
            </form>
            
            <!-- Social Links -->
            <div class="footer-social text-center">
              <a href="https://facebook.com" target="_blank" class="footer-social-icon" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="https://instagram.com" target="_blank" class="footer-social-icon" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
              <a href="https://wa.me/" target="_blank" class="footer-social-icon" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            </div>
          </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom text-center pt-3 mt-2" style="font-size:0.97rem; color:#888; border-top:1px solid #eee; background:rgba(255,255,255,0.7);">
          <?php echo date('Y'); ?> All Rights Reserved @ Sherwani Rentals
        </div>
      </div>
    </footer>
    <!-- Artistic Footer End -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/filters.js"></script>
    <script src="../assets/js/modal.js"></script>
    <script src="../assets/js/rent.js"></script>
</body>
</html>