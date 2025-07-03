document.addEventListener("DOMContentLoaded", function() {
    var floatingBtn = document.querySelector('.floating-btn');
    if (floatingBtn) {
      new bootstrap.Tooltip(floatingBtn, {
        placement: 'right'  // La scritta apparirà a sinistra del pulsante
      });
    }
  });
  