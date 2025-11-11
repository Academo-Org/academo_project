<?php
// A sessão já foi verificada no dashboard principal
require_once __DIR__ . '/../db.php';

// --- LÓGICA PARA O RESUMO DA COORDENAÇÃO ---
$stmt_prof = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role_id = 2 AND status = 'ativo'");
$stmt_prof->execute();
$total_professores = $stmt_prof->get_result()->fetch_assoc()['total'];
$stmt_prof->close();

$stmt_alunos = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role_id = 3 AND status = 'ativo'");
$stmt_alunos->execute();
$total_alunos = $stmt_alunos->get_result()->fetch_assoc()['total'];
$stmt_alunos->close();

$stmt_turmas = $conn->prepare("SELECT COUNT(*) as total FROM classes");
$stmt_turmas->execute();
$total_turmas = $stmt_turmas->get_result()->fetch_assoc()['total'];
$stmt_turmas->close();
?>

<style>
  /* CSS copiado do professor/inicio.php */
  .logo { text-align: center; }
  .logo img {
    width: 90px;
  }
  .logo h1 {
    color: #6b3df2;
    font-size: 34px;
    margin-top: 8px;
    border: none; /* Sobrescreve o H1 genérico do dashboard */
  }
  .welcome {
    font-size: 22px;
    margin-top: 16px;
    font-weight: 600;
    text-align: center;
  }
  .descricao {
    font-size: 17px;
    margin: 16px auto 36px;
    max-width: 920px;
    color: #46515b;
    text-align: center;
  }
  .carousel {
    position: relative;
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .viewport {
    overflow: hidden;
    border-radius: 14px;
    flex: 1;
    background: var(--soft);
    border: 1px solid var(--line);
    padding: 12px;
  }
  .carousel-images {
    display: flex;
    gap: 12px;
    transition: transform 0.45s ease;
  }
  .carousel-images img {
    width: calc((100% - 24px) / 3);
    height: 200px;
    object-fit: cover;
    border-radius: 12px;
    flex-shrink: 0;
    background: #eee;
    display: block;
  }
  .ctrl {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border: 0;
    border-radius: 50%;
    background: var(--purple);
    color: #fff;
    font-size: 20px;
    cursor: pointer;
    display: grid;
    place-items: center;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
    transition: transform 0.12s, filter 0.2s;
    z-index: 5;
  }
  .ctrl:hover { filter: brightness(1.05); }
  .ctrl:active { transform: translateY(-50%) scale(0.98); }
  .prev { left: 12px; }
  .next { right: 12px; }
  
  @media (max-width: 1024px) {
    .carousel-images img { width: calc((100% - 12px) / 2); height: 190px; }
  }
  @media (max-width: 640px) {
    .carousel-images img { width: 100%; height: 180px; }
  }

  /* CSS do Resumo */
  .summary-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
  }
  .summary-item {
      background-color: #f9f9f9;
      border: 1px solid #e0e0e0;
      border-left: 5px solid var(--teal);
      padding: 25px;
      border-radius: 5px;
      text-align: center;
  }
  .summary-item h3 {
      margin-top: 0;
      margin-bottom: 10px;
      color: #333;
      border: none;
      font-size: 1em;
  }
  .summary-item p {
      font-size: 2.5em;
      font-weight: 600;
      color: var(--teal);
      margin: 0;
  }
</style>

<div class="logo">
  <img src="assets/Academo.jpeg" alt="Logo Academo" />
  <h1>ACADEMO</h1>
</div>
<p class="welcome">
  Bem-vindo(a), <?= htmlspecialchars($_SESSION['usuario_nome']); ?>!<br />Este é o seu centro de controle acadêmico.
</p>
<p class="descricao">
  Utilize o menu lateral para gerenciar usuários, turmas e matrículas de todo o sistema.
</p>

<section class="carousel" aria-label="Galeria de imagens">
  <button class="ctrl prev" aria-label="Anterior">
    <i class="fa-solid fa-chevron-left"></i>
  </button>
  <div class="viewport">
    <div class="carousel-images" id="track">
      <img src="assets/imagem1.jpg" alt="Imagem 1" />
      <img src="assets/imagem2.jpeg" alt="Imagem 2" />
      <img src="assets/imagem3.jpg" alt="Imagem 3" />
      <img src="assets/imagem4.jpg" alt="Imagem 4" />
    </div>
  </div>
  <button class="ctrl next" aria-label="Próxima">
    <i class="fa-solid fa-chevron-right"></i>
  </button>
</section>

<hr style="border: 0; border-top: 1px solid #eee; margin: 40px 0;">
<div class="box">
    <h2>Resumo do Sistema</h2>
    <div class="summary-grid">
        <div class="summary-item">
            <h3>Professores Ativos</h3>
            <p><?= $total_professores ?></p>
        </div>
        <div class="summary-item">
            <h3>Alunos Ativos</h3>
            <p><?= $total_alunos ?></p>
        </div>
        <div class="summary-item">
            <h3>Turmas Criadas</h3>
            <p><?= $total_turmas ?></p>
        </div>
    </div>
</div>
<script>
  const track = document.getElementById("track");
  if(track) {
    const slides = Array.from(track.children);
    const prev = document.querySelector(".prev");
    const next = document.querySelector(".next");
    let index = 0;
    function visibleCount() {
      if (!track.parentElement) return 3;
      const vw = track.parentElement.clientWidth;
      if (vw < 520) return 1;
      if (vw < 920) return 2;
      return 3;
    }
    function slideWidth() {
      if (!slides.length) return 0;
      const w = slides[0].getBoundingClientRect().width;
      return w + 12;
    }
    function clampIndex() {
      const vis = visibleCount();
      const max = Math.max(0, slides.length - vis);
      if (index < 0) index = 0;
      if (index > max) index = max;
    }
    function update() {
      if (!track.style) return;
      clampIndex();
      track.style.transform = `translateX(-${index * slideWidth()}px)`;
    }
    next.addEventListener("click", () => { index++; update(); });
    prev.addEventListener("click", () => { index--; update(); });
    window.addEventListener("resize", update);
    window.addEventListener("load", update);
  }
</script>