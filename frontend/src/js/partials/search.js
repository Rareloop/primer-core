import el from '../utilities/el';

const keys = {
    ESC: 27,
    UP: 38,
    DOWN: 40,
    ENTER: 13,
};

export default class {
    constructor(container, nav) {
        this.container = container;
        this.nav = nav;
        this.items = this.getSearchData();
        this.currentIndex = -1;

        const id = `search-${Date.now()}`;

        this.surface = el(`
            <div class="primer-search">
                <div class="primer-search__control">
                    <label for="${ id }" class="primer-search__label">Search</label>
                    <input id="${ id }" type="text" class="primer-search__input" />
                </div>
                <ul class="primer-search__results"></ul>
            </div>
        `);

        this.resultsUl = this.surface.querySelector('.primer-search__results');

        this.container.appendChild(this.surface);

        this.setupEventListeners();
    }

    setupEventListeners() {
        this.addEventListener('keyup', '.primer-search__input', (event) => {
            if (event.keyCode === keys.ESC) {
                event.target.value = '';
            }

            if (event.keyCode === keys.DOWN) {
                this.setCurrentIndex(this.currentIndex + 1);
                return;
            }

            if (event.keyCode === keys.UP) {
                this.setCurrentIndex(this.currentIndex - 1);
                return;
            }

            if (event.keyCode === keys.ENTER) {
                this.selectIndex(this.currentIndex);
                return;
            }

            this.updateResults(event.target.value);
        });

        this.addEventListener('focus', '.primer-search__input', (event) => {

            this.surface.classList.add('primer-search--focus');
            this.updateResults(event.target.value);
        });

        this.addEventListener('blur', '.primer-search__input', (event) => {
            this.renderResults([]);

            this.surface.classList.remove('primer-search--focus');
            this.disableSearching();
            this.container.querySelector('.primer-search__input').value = '';
        });
    }

    selectIndex(index) {
        const results = this.surface.querySelectorAll('.primer-search__result');

        if (index < results.length) {
            window.location = results[index].querySelector('.primer-search__result-link').getAttribute('href');
        }
    }

    setCurrentIndex(index) {
        const results = this.surface.querySelectorAll('.primer-search__result');

        if (index < 0 || index >= results.length || index === undefined) {
            return;
        }

        if (results[this.currentIndex]) {
            results[this.currentIndex].classList.remove('primer-search__result--current');
        }

        results[index].classList.add('primer-search__result--current');

        this.currentIndex = index;

    }

    enableSearching() {
        this.surface.closest('.primer-sidebar').classList.add('primer-sidebar--searching');

        this.currentIndex = -1;
    }

    disableSearching() {
        this.surface.closest('.primer-sidebar').classList.remove('primer-sidebar--searching');
    }

    updateResults(query) {
        if (query === '') {
            this.disableSearching();
        } else {
            this.enableSearching();
        }

        const results = this.getFilteredData(query);

        if (results.length || query.length === 0) {
            this.renderResults(results);
        } else {
            this.renderNoResults();
        }

    }

    getSearchData() {
        const allItems = [];

        this.nav.querySelectorAll('.primer-nav-item--leaf-node').forEach((item) => {
            const link = item.querySelector('.primer-nav-item__link').getAttribute('href');
            const title = item.querySelector('.primer-nav-item__link-title').innerText;

            const data = {
                id: item.dataset.id,
                path: item.dataset.id.split('/'),
                title,
                link,
            }

            allItems.push(data);
        });

        return allItems.sort((a, b) => {
            return a.title.localeCompare(b.title);
        });
    }

    getFilteredData(query) {
        if (query === '') {
            return [];
        }

        return this.items.filter((item) => {
            return item.id.includes(query.toLowerCase());
        });
    };

    renderResults(results) {
        const fragment = document.createDocumentFragment();

        results.forEach((result) => {
            const li = el(`
                <li class="primer-search__result">
                    <a href="${ result.link }" class="primer-search__result-link">
                        <span class="primer-search__result-title">${ result.title }</span>
                        <span class="primer-search__result-id">${ result.id }</span>
                    </a>
                </li>
            `);

            fragment.appendChild(li);
        });

        this.resultsUl.innerHTML = '';
        this.resultsUl.appendChild(fragment);
    }

    renderNoResults() {
        const li = el(`
            <li class="primer-search__result primer-search__result--empty">
                <span class="primer-search__result-title">No matches</span>
            </li>
        `);

        this.resultsUl.innerHTML = '';
        this.resultsUl.appendChild(li);
    };

    addEventListener(eventName, selector, callback) {
        // https://www.quirksmode.org/blog/archives/2008/04/delegating_the.html
        const captureEvents = ['focus', 'blur', 'change'];

        const capture = captureEvents.indexOf(eventName) !== -1;

        this.surface.addEventListener(eventName, (event) => {
            if (event.target.matches(selector)) {
                callback(event);
            }
        }, capture);
    }
};
