<?php
require_once __DIR__ . '/../src/app.php';
require_once __DIR__ . '/../src/ProductoRepository.php';
$valorBuscar = $_GET['q'] ?? '';
$buscar = is_string($valorBuscar) ? trim($valorBuscar) : '';
$productos = (new ProductoRepository(db()))->listar($buscar);
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#f7f3ec">
  <title>Café Aurora | Un lugar para hacer pausa</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
  <header class="site-header">
    <a class="brand" href="#" aria-label="Café Aurora, inicio"><span class="brand-mark">✳</span><span>café<br><strong>aurora</strong></span></a>
    <nav class="main-nav" aria-label="Navegación principal">
      <a href="#nosotros">Nosotros</a><a href="#menu">Menú</a><a href="#reservar">Reservar</a>
    </nav>
    <div class="header-actions"><a class="admin-link" href="#equipo">Equipo Aurora ↗</a></div>
  </header>
  <main>
    <section class="hero">
      <div class="hero-copy">
        <span class="eyebrow">CAFÉ DE BARRIO · HECHO CON CALMA</span>
        <h1>Una pausa<br><em>con buen café.</em></h1>
        <p>Sabores preparados al momento, pan recién horneado y un espacio para encontrarnos.</p>
        <a class="button button-light" href="#menu">Explorar el menú <span aria-hidden="true">↓</span></a>
      </div>
      <div class="hero-art" aria-label="Ilustración decorativa de una taza de café" role="img">
        <div class="sun-disc"></div>
        <div class="steam steam-one"></div>
        <div class="steam steam-two"></div>
        <div class="cup">
          <div class="coffee"></div>
          <div class="cup-handle"></div>
        </div>
        <div class="saucer"></div><span class="art-caption">Tómate<br>un momento.</span>
      </div>
      <div class="hero-bottom"><span>01 — Hecho en casa</span><span>CDMX · Desde 2021</span></div>
    </section>
    <section class="section intro" id="nosotros">
      <div class="section-kicker">NUESTRA ESENCIA <span>✳</span></div>
      <div>
        <h2>Lo cotidiano<br>también puede<br><em>ser especial.</em></h2>
      </div>
      <div class="intro-note">
        <p>En Aurora creemos que las mejores ideas y conversaciones empiezan con una mesa, algo rico y tiempo para disfrutarlo.</p><a class="text-link" href="#reservar">Ven a conocernos <span>↗</span></a>
      </div>
    </section>
    <section class="section menu-section" id="menu">
      <div class="section-heading">
        <div>
          <div class="section-kicker">PREPARADO PARA TI</div>
          <h2>Del menú <em>Aurora</em></h2>
        </div>
        <form class="search-form" method="get">
          <label for="q">Buscar en el menú</label>
          <div>
            <input
              id="q"
              name="q"
              type="search"
              value="<?= htmlspecialchars($buscar, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
              placeholder="Café, pan…">
            <button class="button button-dark" type="submit">Buscar</button>
          </div>
        </form>
      <div class="product-grid">
        <?php foreach ($productos as $producto): ?>
          <?php
          $categoria = (string) $producto['categoria'];
          $claseArte = stripos($categoria, 'panader') !== false
            ? 'art-p'
            : (stripos($categoria, 'bebida') !== false ? 'art-b' : 'art-c');
          ?>
          <article class="product-card">
            <div class="product-art <?= $claseArte ?>"><span><?= h(mb_strtoupper(mb_substr((string) $producto['nombre'], 0, 1, 'UTF-8'), 'UTF-8')) ?></span><i>&#10033;</i></div>
            <div class="product-meta"><span><?= h($categoria) ?></span><strong>$<?= number_format((float) $producto['precio'], 2) ?></strong></div>
            <h3><?= h((string) $producto['nombre']) ?></h3>
            <p><?= h((string) $producto['descripcion']) ?></p>
            <a class="text-link" href="#reservar" data-producto="<?= (int) $producto['id'] ?>">Elegir para mi visita <span>&rarr;</span></a>
          </article>
        <?php endforeach; ?>

        <?php if (!$productos): ?>
          <p class="search-empty">No encontramos productos que coincidan con &ldquo;<?= h($buscar) ?>&rdquo;.</p>
        <?php endif; ?>
      </div>

    </section>
<section class="reservation-section" id="reservar">
      <div class="reservation-copy">
        <div class="section-kicker">TU MESA TE ESPERA</div>
        <h2>Nos vemos<br><em>pronto.</em></h2>
        <p>Déjanos tus datos y el horario que prefieres. Te contactaremos para confirmar la visita.</p>
        <div class="reservation-detail"><span>01</span>
          <div><strong>Formulario visual</strong><small>placeholderplaceholder</small></div>
        </div>
      </div>
      <form class="reservation-form" action="/reserva.php" method="post">

        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

        <label>Nombre
          <input name="nombre" required maxlength="100">
        </label>
        <label>Correo
          <input name="correo" type="email" required maxlength="190">
        </label>
        <label>Teléfono
          <input name="telefono" required maxlength="30">
        </label>
        <label>Fecha
          <input name="fecha" type="date" min="<?= date('Y-m-d') ?>" required>
        </label>
        <label>Hora
          <input name="hora" type="time" min="08:00" max="20:30" required>
        </label>
        <label>Personas
          <select name="personas">
            <?php for ($n = 1; $n <= 8; $n++): ?>
              <option value="<?= $n ?>"><?= $n ?></option>
            <?php endfor; ?>
          </select>
        </label>
        <label>Producto (opcional)
          <select name="producto_id">
            <option value="">Lo decido al llegar</option>
            <?php foreach ($productos as $producto): ?>
              <option value="<?= (int) $producto['id'] ?>"><?= h($producto['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Mensaje (opcional)
          <textarea name="mensaje" maxlength="500"></textarea>
        </label>
        <button type="submit">Enviar solicitud</button>
      </form>
    </section>
    <section class="closing" id="equipo"><span>CAFÉ AURORA</span>
      <p>Un buen día empieza<br>con un buen café.</p><a class="button button-light" href="#menu">Volver al menú ↑</a>
    </section>
  </main>
  <footer class="site-footer"><a class="brand brand-footer" href="#"><span class="brand-mark">✳</span><span>café<br><strong>aurora</strong></span></a><span>placeholderplaceholder</span><a href="#equipo">Equipo Aurora ↗</a></footer>
  <script src="assets/app.js" defer></script>
</body>

</html>