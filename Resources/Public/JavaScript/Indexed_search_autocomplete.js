class IndexSearchAutoComplete {
    constructor() {
        this.debounceTimeout = null;   // Used to reduce the amount of queries
        this.lastSearchQuery = '';     // Used to reduce the amount of queries

        // Alle relevanten Input-Felder suchen
        const selectors = 'input.search, input.tx-indexedsearch-searchbox-sword, input.indexed-search-atocomplete-sword, input.indexed-search-autocomplete-sword';
        const inputs = document.querySelectorAll(selectors);

        if (inputs.length === 0) {
            return;
        }

        // Event-Listener registrieren
        inputs.forEach((input) => {
            input.addEventListener('keyup', (e) => this.autocomplete(e, input));
            input.addEventListener('keypress', (e) => this.autocomplete(e, input));
            input.setAttribute('autocomplete', 'off');
        });

        // Klick überall auf der Seite: Autocomplete schließen, wenn außerhalb geklickt wird
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.search-autocomplete-results')) {
                document.querySelectorAll('.search-autocomplete-results').forEach((box) => {
                    box.innerHTML = '';
                    box.style.display = 'none';
                    box.classList.remove('results');
                    box.classList.add('no-results');
                });
            }
        });
    }

    /**
     * Autocomplete a query
     *
     * @param {KeyboardEvent} e
     * @param {HTMLInputElement} ref
     */
    autocomplete(e, ref) {
        const input = ref;
        let elem = ref;
        let results = null;

        // Das passende .search-autocomplete-results-Element finden
        while (elem && elem.tagName !== 'HTML') {
            results = elem.querySelector('.search-autocomplete-results');
            if (results) {
                break;
            }
            elem = elem.parentElement;
        }

        if (!results) {
            console.log("we couldn't find a result div (.search-autocomplete-results)");
            return;
        }

        // Optionen aus data-Attributen lesen
        const mode = results.dataset.mode || 'word';
        const soc = results.dataset.searchonclick === 'true'; // search on click

        const keyCode = e.keyCode || e.which || 0;

        // Navigation durch die Vorschläge (Pfeil hoch/runter, Enter)
        if (keyCode === 38 || keyCode === 40 || keyCode === 10 || keyCode === 13) {
            const highlighted = results.querySelector('li.highlighted');

            // Pfeil hoch
            if (keyCode === 38 && e.type === 'keyup') {
                let target;
                if (!highlighted || !highlighted.previousElementSibling) {
                    target = results.querySelector('li:last-child');
                } else {
                    target = highlighted.previousElementSibling;
                }

                if (target) {
                    if (highlighted) {
                        highlighted.classList.remove('highlighted');
                    }
                    target.classList.add('highlighted');
                }
            }

            // Pfeil runter
            if (keyCode === 40 && e.type === 'keyup') {
                let target;
                if (!highlighted || !highlighted.nextElementSibling) {
                    target = results.querySelector('li:first-child');
                } else {
                    target = highlighted.nextElementSibling;
                }

                if (target) {
                    if (highlighted) {
                        highlighted.classList.remove('highlighted');
                    }
                    target.classList.add('highlighted');
                }
            }

            // Enter
            if ((keyCode === 10 || keyCode === 13) && e.type === 'keypress') {
                const isVisible = results.offsetParent !== null;
                const current = results.querySelector('li.highlighted');

                if (isVisible && current) {
                    if (mode === 'word') {
                        // Klick simulieren
                        current.click();

                        if (soc) {
                            const form = input.closest('form');
                            if (form) {
                                form.submit();
                            }
                        }
                    } else {
                        const link = current.querySelector('a.navigate-on-enter');
                        if (link) {
                            window.location.href = link.href;
                        }
                    }
                    e.preventDefault();
                }
            }

            return;
        }

        // Links/Rechts ignorieren
        if (keyCode === 37 || keyCode === 39) {
            return;
        }

        // Nur auf keyup reagieren (wie im Original)
        if (e.type !== 'keyup') {
            return;
        }

        // Ergebnisse leeren
        results.innerHTML = '';
        results.style.display = 'none';
        results.classList.remove('results');
        results.classList.add('no-results');

        // Suchbegriff
        const val = input.value.trim();
        const minlen = results.dataset.minlength ? parseInt(results.dataset.minlength, 10) : 3;
        const maxResults = results.dataset.maxresults ? parseInt(results.dataset.maxresults, 10) : 10;

        // Mindestlänge prüfen
        if (val.length < minlen) {
            return;
        }

        // Nur neue Suchbegriffe wirklich losschicken
        if (val === this.lastSearchQuery) {
            return;
        }

        this.lastSearchQuery = val;

        // User anzeigen, dass gesucht wird
        results.classList.add('autocomplete_searching');

        // Anfrage ausführen
        this.performQuery(val, mode, maxResults, results, input);
    }

    performQuery(val, mode, maxResults, results, input) {
        const soc = results.dataset.searchonclick === 'true';

        // Debounce
        clearTimeout(this.debounceTimeout);
        this.debounceTimeout = setTimeout(() => {
            clearTimeout(this.debounceTimeout);

            const url = results.dataset.searchurl;
            if (!url) {
                console.error('No data-searchurl defined on .search-autocomplete-results');
                results.classList.remove('autocomplete_searching');
                return;
            }

            // Request-Daten wie vorher: s, m, mr
            const formData = new FormData();
            formData.append('s', val);
            formData.append('m', mode);
            formData.append('mr', String(maxResults));

            fetch(url, {
                method: 'POST',
                body: formData,
                cache: 'no-store'
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then((data) => {
                    // Ergebnisse einfügen
                    results.innerHTML = data;
                    results.style.display = '';
                    results.classList.remove('autocomplete_searching');

                    const items = results.querySelectorAll('li');

                    items.forEach((li) => {
                        li.addEventListener('click', () => {
                            if (mode === 'word') {
                                input.value = li.textContent.trim();
                                results.innerHTML = '';
                                results.style.display = 'none';

                                if (soc) {
                                    const form = input.closest('form');
                                    if (form) {
                                        form.submit();
                                    }
                                }
                            } else {
                                const link = li.querySelector('a.navigate-on-enter');
                                if (link) {
                                    window.location.href = link.href;
                                }
                            }
                        });
                    });

                    if (items.length === 0) {
                        // keine Ergebnisse
                        results.innerHTML = '';
                        results.style.display = 'none';
                        results.classList.remove('results');
                        results.classList.add('no-results');
                    } else {
                        // Ergebnisse vorhanden
                        results.classList.remove('no-results');
                        results.classList.add('results');
                    }
                })
                .catch((error) => {
                    console.error('Autocomplete request failed:', error);
                    results.classList.remove('autocomplete_searching');
                    results.innerHTML = '';
                    results.style.display = 'none';
                    results.classList.remove('results');
                    results.classList.add('no-results');
                });
        }, 250);
    }
}

// Init nach DOM-Ready
document.addEventListener('DOMContentLoaded', () => {
    new IndexSearchAutoComplete();
});
