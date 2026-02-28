document.addEventListener('DOMContentLoaded', function () {
  var header = document.getElementById('header');
  var lastScrollTop = 0;
  var isTicking = false;
  var mobile = window.innerWidth <= 768;
  var updateMobileFlag = function updateMobileFlag() {
    mobile = window.innerWidth <= 768;
  };
  var handleScroll = function handleScroll() {
    var isAnyMenuOpen = document.querySelector('.menu.is-open');
    if (!mobile || isAnyMenuOpen) {
      header.classList.remove('hidden'); // Явно показываем хедер
      isTicking = false;
      return;
    }
    var currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    if (currentScroll > 60) {
      header.classList.toggle('hidden', currentScroll > lastScrollTop);
    } else {
      header.classList.remove('hidden');
    }
    lastScrollTop = Math.max(currentScroll, 0);
    isTicking = false;
  };
  var onScroll = function onScroll() {
    if (!isTicking) {
      isTicking = true;
      requestAnimationFrame(handleScroll);
    }
  };
  window.addEventListener('scroll', onScroll);
  window.addEventListener('resize', updateMobileFlag);
  document.addEventListener('click', function (e) {
    var button = e.target.closest('.menu-button');
    var clickedMenu = e.target.closest('.menu');

    // Клик вне меню и кнопок — закрыть все меню
    if (!button && !clickedMenu) {
      document.querySelectorAll('.menu.is-open').forEach(function (menu) {
        return menu.classList.remove('is-open');
      });
      document.querySelectorAll('.menu-button[aria-expanded="true"]').forEach(function (btn) {
        btn.setAttribute('aria-expanded', 'false');
      });
      document.querySelectorAll('.menu-button.is-active').forEach(function (btn) {
        btn.classList.remove('is-active');
      });
      return;
    }

    // Клик по кнопке
    if (button) {
      var menuId = button.dataset.menuId;
      var menu = document.getElementById(menuId);
      if (menu) {
        var isOpen = menu.classList.contains('is-open');

        // Закрыть все другие меню
        document.querySelectorAll('.menu.is-open').forEach(function (m) {
          if (m !== menu) m.classList.remove('is-open');
        });

        // Обновить aria и классы у других кнопок
        document.querySelectorAll('.menu-button[aria-expanded="true"]').forEach(function (btn) {
          if (btn !== button) btn.setAttribute('aria-expanded', 'false');
        });
        document.querySelectorAll('.menu-button.is-active').forEach(function (btn) {
          if (btn !== button) btn.classList.remove('is-active');
        });

        // Переключить текущее меню и кнопку
        menu.classList.toggle('is-open', !isOpen);
        button.setAttribute('aria-expanded', String(!isOpen));
        button.classList.toggle('is-active', !isOpen);

        // Показать header, если меню открылось
        if (!isOpen && typeof header !== 'undefined') {
          header.classList.remove('hidden');
        }
      }
    }
  });
});
