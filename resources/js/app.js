document.addEventListener('DOMContentLoaded', () => {
    const header = document.getElementById('header');
    let lastScrollTop = 0;
    let isTicking = false;
    let mobile = window.innerWidth <= 768;

    const updateMobileFlag = () => {
        mobile = window.innerWidth <= 768;
    };

    const handleScroll = () => {
        const isAnyMenuOpen = document.querySelector('.menu.is-open');

        if (!mobile || isAnyMenuOpen) {
            header.classList.remove('hidden'); // Явно показываем хедер
            isTicking = false;
            return;
        }

        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        if (currentScroll > 60) {
            header.classList.toggle('hidden', currentScroll > lastScrollTop);
        } else {
            header.classList.remove('hidden');
        }

        lastScrollTop = Math.max(currentScroll, 0);
        isTicking = false;
    };

    const onScroll = () => {
        if (!isTicking) {
            isTicking = true;
            requestAnimationFrame(handleScroll);
        }
    };

    window.addEventListener('scroll', onScroll);
    window.addEventListener('resize', updateMobileFlag);


    document.addEventListener('click', (e) => {
        const button = e.target.closest('.menu-button');
        const clickedMenu = e.target.closest('.menu');
    
        // Клик вне меню и кнопок — закрыть все меню
        if (!button && !clickedMenu) {
            document.querySelectorAll('.menu.is-open').forEach(menu => menu.classList.remove('is-open'));
            document.querySelectorAll('.menu-button[aria-expanded="true"]').forEach(btn => {
                btn.setAttribute('aria-expanded', 'false');
            });
            document.querySelectorAll('.menu-button.is-active').forEach(btn => {
                btn.classList.remove('is-active');
            });
            return;
        }
    
        // Клик по кнопке
        if (button) {
            const menuId = button.dataset.menuId;
            const menu = document.getElementById(menuId);
    
            if (menu) {
                const isOpen = menu.classList.contains('is-open');
    
                // Закрыть все другие меню
                document.querySelectorAll('.menu.is-open').forEach(m => {
                    if (m !== menu) m.classList.remove('is-open');
                });
    
                // Обновить aria и классы у других кнопок
                document.querySelectorAll('.menu-button[aria-expanded="true"]').forEach(btn => {
                    if (btn !== button) btn.setAttribute('aria-expanded', 'false');
                });
                document.querySelectorAll('.menu-button.is-active').forEach(btn => {
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
