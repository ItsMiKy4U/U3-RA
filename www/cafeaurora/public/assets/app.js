document.querySelectorAll('[data-producto]').forEach((link) => {
  link.addEventListener('click', () => {
    const select = document.querySelector('select[name="producto_id"]');
    if (select) select.value = link.dataset.producto;
  });
});
