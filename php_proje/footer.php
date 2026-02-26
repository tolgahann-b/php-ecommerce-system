<!-- FOOTER BAŞLANGIÇ -->
<footer class="bg-dark text-white pt-5 pb-4 mt-5"> <!-- Bootstrap Utility sınıfları -->
    <div class="container">
        <div class="row text-center text-md-start">
            
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 fw-bold text-danger">Bülbül Motor</h5>
                <p>
                    Motor tutkunları için en kaliteli yedek parça ve aksesuarların tek adresi. 
                    Güvenli alışveriş ve hızlı teslimat garantisi.
                </p>
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 fw-bold text-danger">Hızlı Linkler</h5>
                <p><a href="index.php" class="text-white text-decoration-none">Ana Sayfa</a></p>
                <p><a href="duyurular.php" class="text-white text-decoration-none">Duyurular</a></p>
                <?php if(isset($_SESSION['id'])) { ?> <!-- Oturum varsa ekstra linkler -->
                <p><a href="cerez.php" class="text-white text-decoration-none">Çerez Yönetimi</a></p>
                <p><a href="multi_session.php" class="text-white text-decoration-none">Hesaplar</a></p>
                <?php } else { ?> <!-- Oturum yoksa giriş/kayıt linkleri -->
                <p><a href="uyegiris.php" class="text-white text-decoration-none">Giriş Yap</a></p>
                <p><a href="uyekayit.php" class="text-white text-decoration-none">Kayıt Ol</a></p>
                <?php } ?>
            </div>

            <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 fw-bold text-danger">İletişim</h5>
                <p><i class="fas fa-home me-2"></i> İstanbul, Türkiye</p>
                <p><i class="fas fa-envelope me-2"></i> info@bulbulmotor.com</p>
                <p><i class="fas fa-phone me-2"></i> +90 555 123 45 67</p>
            </div>

            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 fw-bold text-danger">Bizi Takip Edin</h5>
                <a href="#" class="btn btn-primary-custom btn-floating m-1" style="border-radius: 50%"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="btn btn-primary-custom btn-floating m-1" style="border-radius: 50%"><i class="fab fa-twitter"></i></a>
                <a href="#" class="btn btn-primary-custom btn-floating m-1" style="border-radius: 50%"><i class="fab fa-instagram"></i></a>
                <a href="#" class="btn btn-primary-custom btn-floating m-1" style="border-radius: 50%"><i class="fab fa-linkedin-in"></i></a>
            </div>
            
        </div>

        <hr class="mb-4">

        <div class="row align-items-center">
            <div class="col-md-7 col-lg-8">
                <p> © 2026 Tüm Hakları Saklıdır:
                    <a href="#" class="text-danger text-decoration-none fw-bold">Bülbül Motor</a>
                </p>
            </div>
            <div class="col-md-5 col-lg-4">
                <p class="text-md-end text-muted small">Coded by Tolga</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
