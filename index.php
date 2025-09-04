<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Clothes Stock — Inventory, Retail & Payments</title>
  <link rel="stylesheet" href="css/style.css"/>
  <style>
    body { margin:0; font-family: 'Segoe UI', sans-serif; background:#fff; color:#222; }

    /* Hero */
    .hero { background:#f8faff; padding:80px 20px; text-align:center; }
    .hero h1 { font-size:2.8rem; color:#007bff; margin-bottom:1rem; }
    .hero p { font-size:1.2rem; color:#444; margin-bottom:2rem; }
    .hero-cta a { margin:0 10px; }

    /* Buttons */
    .btn-primary { background:#007bff; color:#fff; padding:12px 25px; border-radius:6px; display:inline-block; }
    .btn-primary:hover { background:#0056b3; }
    .btn-ghost { border:1px solid #007bff; color:#007bff; padding:12px 25px; border-radius:6px; display:inline-block; }
    .btn-ghost:hover { background:#007bff; color:#fff; }

    /* Stats */
    .stats-row { display:flex; justify-content:center; gap:30px; margin-top:30px; }
    .stat { text-align:center; }
    .stat-value { font-size:1.6rem; font-weight:bold; color:#007bff; }
    .stat-label { font-size:.9rem; color:#666; }

    /* Carousel */
    .carousel { position: relative; max-width: 100%; margin: 3rem auto; overflow: hidden; border-radius: 12px; }
    .carousel-inner { display: flex; transition: transform .8s ease-in-out; }
    .carousel img { width: 100%; flex-shrink: 0; height: 400px; object-fit: cover; }
    .carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(0,123,255,0.6); border: none; color: #fff; font-size: 2rem; padding: .5rem 1rem; cursor: pointer; border-radius: 6px; }
    .carousel-btn.left { left: 10px; }
    .carousel-btn.right { right: 10px; }

    /* Sections */
    .container { padding:60px 20px; max-width:1100px; margin:auto; }
    .section-title { font-size:2rem; text-align:center; margin-bottom:40px; color:#007bff; }
    .grid-3 { display:grid; grid-template-columns:repeat(auto-fit, minmax(280px,1fr)); gap:20px; }

    /* Cards */
    .card { background:#fff; padding:20px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08); transition:.3s; }
    .card:hover { transform:translateY(-5px); }
    .card.quote { font-style:italic; text-align:center; }
    .card h3 { margin-bottom:10px; color:#007bff; }

    /* Workflow */
    .workflow { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:20px; }
    .wf-step { text-align:center; padding:20px; }
    .wf-badge { background:#007bff; color:#fff; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; margin:auto; margin-bottom:10px; }

    /* CTA */
    .cta-card { text-align:center; padding:30px; background:#f0f7ff; border-radius:12px; }
    .cta-card h3 { margin-bottom:20px; color:#007bff; }

    /* Animations */
    .fade-in { opacity:0; transform:translateY(20px); transition:all .8s ease; }
    .fade-in.visible { opacity:1; transform:translateY(0); }
    .slide-fade { opacity:0; transform:translateX(-40px); transition:all 1s ease; }
    .slide-fade.visible { opacity:1; transform:translateX(0); }

    /* Newsletter */
    .cta-form { display:flex; justify-content:center; gap:10px; flex-wrap:wrap; }
    .input { padding:10px; border:1px solid #ccc; border-radius:6px; }
  </style>
</head>
<body>
  
  <!-- Navbar -->
  <?php include "header.php"; ?>

  <!-- Hero -->
  <section class="hero">
    <div class="hero-inner">
      <h1 class="title slide-fade">Run your apparel inventory like a pro</h1>
      <p class="subtitle fade-in">Powerful stock management for clothes — variants, alerts, supplier tracking, retailer catalogue, and smooth checkout.</p>
      <div class="hero-cta">
        <a class="btn-primary" href="retailer.php">View Stock</a>
        <a class="btn-ghost" href="#">Go to Admin</a>
      </div>
      <div class="stats-row fade-in">
        <div class="stat"><div class="stat-value" id="statItems">0</div><div class="stat-label">Items</div></div>
        <div class="stat"><div class="stat-value" id="statQty">0</div><div class="stat-label">Units in stock</div></div>
        <div class="stat"><div class="stat-value" id="statWorth">₹0</div><div class="stat-label">Inventory value</div></div>
      </div>
    </div>
  </section>

  <!-- Carousel -->
  <section class="carousel fade-in">
    <button class="carousel-btn left" onclick="moveSlide(-1)">❮</button>
    <div class="carousel-inner" id="carouselInner">
      <img src="images/arrival1.jpg" alt="New Fashion Stock">
      <img src="images/arrival2.jpg" alt="Trendy Clothes">
      <img src="images/arrival3.jpg" alt="Warehouse Inventory">
      <img src="images/arrival4.jpg" alt="Retail Display">
    </div>
    <button class="carousel-btn right" onclick="moveSlide(1)">❯</button>
  </section>

  <!-- Why Choose Us -->
  <section class="container fade-in">
    <h2 class="section-title">Why choose Clothes Stock?</h2>
    <div class="grid-3">
      <div class="card lift"><h3>All the right fields</h3><p>SKU, size, color, brand, season, supplier, location, tax, and more.</p></div>
      <div class="card lift"><h3>Fast, modern UI</h3><p>Responsive, clean, and feels premium on every device.</p></div>
      <div class="card lift"><h3>Scales with you</h3><p>Ready for PHP/MySQL and payments without redesigns.</p></div>
    </div>
  </section>

  <!-- How it works -->
  <section class="container fade-in">
    <h2 class="section-title">How it works</h2>
    <div class="workflow">
      <div class="wf-step"><div class="wf-badge">1</div><h4>Add Inventory</h4><p>Admin adds items with images, variants, pricing.</p></div>
      <div class="wf-step"><div class="wf-badge">2</div><h4>Publish Catalogue</h4><p>Retailer page shows live stock with filters, search.</p></div>
      <div class="wf-step"><div class="wf-badge">3</div><h4>Cart & Checkout</h4><p>Retailers add items to cart; totals with taxes.</p></div>
      <div class="wf-step"><div class="wf-badge">4</div><h4>Stay in Control</h4><p>Reorder alerts, inline edit, and KPIs for growth.</p></div>
    </div>
  </section>

  <!-- Testimonials -->
  <section class="container fade-in">
    <h2 class="section-title">What our users say</h2>
    <div class="grid-3">
      <div class="card quote"><p>“The UI looks premium and saves us hours every week.”</p><span>— Nisha</span></div>
      <div class="card quote"><p>“Adding variants and images is effortless.”</p><span>— Arjun</span></div>
      <div class="card quote"><p>“Retailer view is super fast, tax totals are spot on.”</p><span>— Kavya</span></div>
    </div>
  </section>

  <!-- Newsletter -->
  <section class="container cta fade-in">
    <div class="card cta-card">
      <h3>Get product updates & tips</h3>
      <form id="newsletterForm" class="cta-form">
        <input id="nlEmail" type="email" class="input" placeholder="Email address" required/>
        <button class="btn-primary" type="submit">Subscribe</button>
      </form>
      <small class="muted" id="nlMsg"></small>
    </div>
  </section>

  <?php include "footer.php"; ?>

  <!-- <script src="js/app.js"></script> -->
  <script>
    // Fade-in animations
    const observers = document.querySelectorAll('.fade-in, .slide-fade');
    const observer = new IntersectionObserver(entries=>{
      entries.forEach(entry=>{
        if(entry.isIntersecting){ entry.target.classList.add('visible'); }
      });
    }, {threshold:0.2});
    observers.forEach(el=>observer.observe(el));

    // Carousel
    let currentSlide = 0;
    function moveSlide(dir) {
      const inner = document.getElementById('carouselInner');
      const slides = inner.children.length;
      currentSlide = (currentSlide + dir + slides) % slides;
      inner.style.transform = `translateX(-${currentSlide*100}%)`;
    }
    setInterval(()=>moveSlide(1), 5000);
  </script>
</body>
</html>
