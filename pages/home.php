<main class="container py-5">
    <div class="hero-section">
        <div class="row align-items-center g-5"> <div class="col-lg-5 ps-lg-5">
                <h1 class="display-5 fw-bold mb-3" style="color: #2d3436;">Seu novo melhor amigo espera por você.</h1>
                <p class="lead text-muted mb-4">Milhares de gatinhos e cães buscam um lar cheio de amor. Que tal mudar uma vida hoje?</p>
                
                    <div class="d-grid d-md-flex justify-content-md-start gap-3">
                        <a class="btn btn-adopt btn-lg shadow-sm" href="?page=listar_pets">Adotar Agora</a>
                        
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <a class="btn btn-outline-custom btn-lg" href="?page=cadastrar_pet">Cadastrar Pet</a>
                        <?php endif; ?>
                    </div>
            </div>
            
            <div class="col-lg-7 pe-lg-5">
                <div id="petCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner shadow-lg" style="border-radius: 25px;">
                        <div class="carousel-item active">
                            <img src="imgcarrosel/gato.jpg" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Gato">
                        </div>
                        <div class="carousel-item">
                            <img src="imgcarrosel/dog.jpg" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Cachorro">
                        </div>
                        <div class="carousel-item">
                            <img src="imgcarrosel/gatoamarelo.jpg" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Gato">
                        </div>
                        <div class="carousel-item">
                            <img src="imgcarrosel/dogrosa.jpg" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Gato">
                        </div>
                        <div class="carousel-item">
                            <img src="imgcarrosel/amarelo.jpg" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Gato">
                        </div>
                        <div class="carousel-item">
                            <img src="imgcarrosel/laranja.jpg" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Gato">
                        </div>
                        <div class="carousel-item">
                            <img src="imgcarrosel/gatorosa.jpg" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Gato">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#petCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#petCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>