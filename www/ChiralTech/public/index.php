<?php
require_once __DIR__ . '/../src/app.php';
require_once __DIR__ . '/../src/ProductoRepository.php';
$valorBuscar = $_GET['q'] ?? '';
$buscar = is_string($valorBuscar) ? trim($valorBuscar) : '';
$productos = (new ProductoRepository(db()))->listar($buscar, false);
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#1a1a1a">
  <title>Chiral Tech | Componentes y Ensambles</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
  <header class="site-header">
    <a class="brand" href="#" aria-label="Chiral Tech, inicio"><span class="brand-mark">✺</span><span>chiral<br><strong>tech</strong></span></a>
    <nav class="main-nav" aria-label="Navegación principal">
      <a href="#nosotros">Nosotros</a><a href="#catalogo">Catálogo</a><a href="#pedido">Hacer Pedido</a>
    </nav>
    <div class="header-actions"><a class="admin-link" href="#equipo">Red Chiral ↗</a></div>
  </header>
  <main>
    <section class="hero">
      <div class="hero-copy">
        <span class="eyebrow">HARDWARE ESPECIALIZADO · ENTREGAS SEGURAS</span>
        <h1>Conectando<br><em>el futuro.</em></h1>
        <p>Procesadores, gráficas y componentes de alto rendimiento para tu próximo ensamble. Mantén tu red estable.</p>
        <a class="button button-light" href="#catalogo">Explorar inventario <span aria-hidden="true">↓</span></a>
      </div>
      <div class="hero-art" aria-label="Ilustración decorativa de hardware" role="img">
        <div class="sun-disc"></div>
        <div class="steam steam-one"></div>
        <div class="steam steam-two"></div>
        <div class="cup">
          <div class="coffee"></div>
          <div class="cup-handle"></div>
        </div>
        <div class="saucer"></div><span class="art-caption">Expande<br>tu red.</span>
      </div>
      <div class="hero-bottom"><span>01 — Ensambles a medida</span><span>TAMAULIPAS · Desde 2026</span></div>
    </section>
    <section class="section intro" id="nosotros">
      <div class="section-kicker">NUESTRA ESENCIA <span>✺</span></div>
      <div>
        <h2>El rendimiento<br>también puede<br><em>ser accesible.</em></h2>
      </div>
      <div class="intro-note">
        <p>En Chiral Tech creemos que cada componente cuenta. Ya sea para renderizado pesado, desarrollo o para correr tus juegos favoritos al máximo, tenemos el hardware que necesitas.</p><a class="text-link" href="#pedido">Cotiza tu equipo <span>↗</span></a>
      </div>
    </section>
    <section class="section menu-section" id="catalogo">
      <div class="section-heading">
        <div>
          <div class="section-kicker">INVENTARIO DISPONIBLE</div>
          <h2>Catálogo <em>Chiral</em></h2>
        </div>
        <form class="search-form" method="get">
          <label for="q">Buscar componentes</label>
          <div>
            <input
              id="q"
              name="q"
              type="search"
              value="<?= htmlspecialchars($buscar, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
              placeholder="Ryzen, RTX, DDR4…">
            <button class="button button-dark" type="submit">Buscar</button>
          </div>
        </form>
      <div class="product-grid">
        <?php foreach ($productos as $producto): ?>
          <?php
          // Ajustamos las variables para que coincidan con el nuevo schema y el JOIN
          $categoria = (string) ($producto['categoria_nombre'] ?? 'Hardware');
          $especificaciones = (string) ($producto['especificaciones'] ?? '');
          $stock = (int) ($producto['stock'] ?? 0);
          
          // Mantenemos tu lógica de clases de arte, adaptada a categorías de PC
          $claseArte = stripos($categoria, 'procesador') !== false
            ? 'art-p'
            : (stripos($categoria, 'tarjeta') !== false ? 'art-b' : 'art-c');
          ?>
          <article class="product-card">
            <div class="product-art <?= $claseArte ?>"><span><?= h(mb_strtoupper(mb_substr((string) $producto['nombre'], 0, 1, 'UTF-8'), 'UTF-8')) ?></span><i>&#10033;</i></div>
            <div class="product-meta"><span><?= h($categoria) ?></span><strong>$<?= number_format((float) $producto['precio'], 2) ?></strong></div>
            <h3><?= h((string) $producto['nombre']) ?></h3>
            <p style="font-size: 0.85em; color: #666;"><em><?= h($especificaciones) ?></em></p>
            <p><?= h((string) $producto['descripcion']) ?></p>
            
            <?php if ($stock > 0): ?>
                <p style="font-size: 0.8em; font-weight: bold;">En stock: <?= $stock ?> unidades</p>
                <a class="text-link" href="#pedido" data-producto="<?= (int) $producto['id'] ?>">Solicitar pieza <span>&rarr;</span></a>
            <?php else: ?>
                <p style="font-size: 0.8em; color: red; font-weight: bold;">Agotado temporalmente</p>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>

        <?php if (!$productos): ?>
          <p class="search-empty">No encontramos componentes que coincidan con &ldquo;<?= h($buscar) ?>&rdquo;.</p>
        <?php endif; ?>
      </div>

    </section>
<section class="reservation-section" id="pedido">
      <div class="reservation-copy">
        <div class="section-kicker">TU HARDWARE TE ESPERA</div>
        <h2>Asegura tu<br><em>componente.</em></h2>
        <p>Déjanos tus datos para procesar la cotización o apartado. Te contactaremos para coordinar la entrega y el método de pago.</p>
        <div class="reservation-detail"><span>01</span>
          <div><strong>Atención local</strong><small>Envíos y recolecciones seguras</small></div>
        </div>
      </div>
      <!-- Cambia el action a pedido.php si decides renombrar el archivo, por ahora lo dejo como reserva.php para no romper tu ruteo actual -->
      <form class="reservation-form" action="/reserva.php" method="post">

        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

        <label>Nombre completo
          <input name="nombre" required maxlength="100">
        </label>
        <label>Correo electrónico
          <input name="correo" type="email" required maxlength="190">
        </label>
        <label>Teléfono de contacto
          <input name="telefono" required maxlength="30">
        </label>
        <label>Fecha deseada de entrega
          <input name="fecha" type="date" min="<?= date('Y-m-d') ?>" required>
        </label>
        <label>Hora preferida de contacto
          <input name="hora" type="time" min="08:00" max="20:30" required>
        </label>
        <label>Cantidad de piezas
          <select name="personas">
            <?php for ($n = 1; $n <= 8; $n++): ?>
              <option value="<?= $n ?>"><?= $n ?></option>
            <?php endfor; ?>
          </select>
        </label>
        <label>Componente (opcional)
          <select name="producto_id">
            <option value="">Cotizar ensamble completo</option>
            <?php foreach ($productos as $producto): ?>
              <option value="<?= (int) $producto['id'] ?>"><?= h($producto['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Especificaciones adicionales o dudas
          <textarea name="mensaje" maxlength="500"></textarea>
        </label>
        <button type="submit">Enviar solicitud</button>
      </form>
    </section>
    <section class="closing" id="equipo"><span>CHIRAL TECH</span>
      <p>Construyendo puentes<br>un componente a la vez.</p><a class="button button-light" href="#catalogo">Volver al catálogo ↑</a>
    </section>
  </main>
  <footer class="site-footer"><a class="brand brand-footer" href="#"><span class="brand-mark">✺</span><span>chiral<br><strong>tech</strong></span></a><span>Hardware & Ensambles</span><a href="#equipo">Red Chiral ↗</a></footer>
  <script src="assets/app.js" defer></script>
</body>

</html>