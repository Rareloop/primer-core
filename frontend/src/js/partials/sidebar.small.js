import el from '../utilities/el';

export default class {
    constructor(surface) {
        if (!('matchMedia' in window)) {
            return;
        }

        this.surface = surface;

        this.mediaQuery = window.matchMedia('(max-width: 1000px)');

        this.mediaQuery.addListener((event) => {
            if (event.matches) {
                this.bootstrap();
            } else {
                this.teardown();
            }
        });

        if (this.mediaQuery.matches) {
            this.bootstrap();
        }
    }

    bootstrap() {

        this.burger = el(`
            <button class="primer-burger" type="button">
                <span class="primer-burger__text">Show Menu</span>
            </button>
        `);

        this.burger.addEventListener('click', () => {
            this.toggleMenu();
        });

        this.nav = document.querySelector('.primer-sidebar__nav');

        this.nav.parentNode.insertBefore(this.burger, this.nav);

        this.nav.hidden = true;

        this.surface.classList.add('primer-sidebar--small');
    }

    teardown() {
        this.surface.classList.remove('primer-sidebar--small');

        if (this.burger) {
            this.burger.parentNode.removeChild(this.burger);
            this.burger = null;
        }

        if (this.nav) {
            this.nav.hidden = false;
        }
    }

    toggleMenu() {
        if (this.burger.classList.contains('primer-burger--open')) {
            this.burger.classList.remove('primer-burger--open');
            this.burger.classList.add('primer-burger--closed');
            this.burger.children[0].innerText = 'Show Menu';
            this.nav.hidden = true;
        } else {
            this.burger.classList.add('primer-burger--open');
            this.burger.classList.remove('primer-burger--closed');
            this.burger.children[0].innerText = 'Hide Menu';
            this.nav.hidden = false;
        }
    }
}
