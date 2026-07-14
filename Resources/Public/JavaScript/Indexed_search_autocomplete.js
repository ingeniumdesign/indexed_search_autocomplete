class IndexSearchAutoComplete {
    constructor() {
        // Per-Box-State (Debounce-Timer, letzte Query, laufende Anfrage), gekeyed am results-Container
        this.state = new WeakMap();
        this.seq = 0; // fortlaufende Nummer für eindeutige ARIA-IDs

        // Alle relevanten Input-Felder suchen
        const selectors = 'input.search, input.tx-indexedsearch-searchbox-sword, input.indexed-search-autocomplete-sword';
        const inputs = document.querySelectorAll(selectors);

        if (inputs.length === 0) {
            return;
        }

        // Event-Listener registrieren
        inputs.forEach((input) => {
            input.addEventListener('keyup', (e) => this.autocomplete(e, input));
            input.addEventListener('keydown', (e) => this.autocomplete(e, input));
            input.setAttribute('autocomplete', 'off');
            // ARIA-Combobox (statischer Teil)
            input.setAttribute('role', 'combobox');
            input.setAttribute('aria-autocomplete', 'list');
            input.setAttribute('aria-expanded', 'false');
        });

        // Klick überall auf der Seite: Autocomplete schließen, wenn außerhalb geklickt wird
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.search-autocomplete-results')) {
                document.querySelectorAll('.search-autocomplete-results').forEach((box) => {
                    box.innerHTML = '';
                    box.style.display = 'none';
                    box.classList.remove('results');
                    box.classList.add('no-results');
                    const st = this.state.get(box);
                    if (st) {
                        this.collapse(st);
                    }
                });
            }
        });
    }

    /**
     * Per-Box-State (Debounce-Timer, letzte Query, laufender AbortController, Input, ARIA-Listbox-ID)
     * @param {HTMLElement} results
     */
    getState(results) {
        let state = this.state.get(results);
        if (!state) {
            state = {
                debounceTimeout: null,
                lastQuery: '',
                controller: null,
                input: null,
                listId: 'isac-' + (++this.seq)
            };
            this.state.set(results, state);
        }
        return state;
    }

    /**
     * Combobox schließen: ARIA-Zustand am Input zurücksetzen
     * @param {object} state
     */
    collapse(state) {
        if (state.input) {
            state.input.setAttribute('aria-expanded', 'false');
            state.input.removeAttribute('aria-activedescendant');
            state.input.removeAttribute('aria-controls');
        }
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

        const state = this.getState(results);
        state.input = input;

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
                        highlighted.removeAttribute('aria-selected');
                    }
                    target.classList.add('highlighted');
                    target.setAttribute('aria-selected', 'true');
                    if (target.id) {
                        input.setAttribute('aria-activedescendant', target.id);
                    }
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
                        highlighted.removeAttribute('aria-selected');
                    }
                    target.classList.add('highlighted');
                    target.setAttribute('aria-selected', 'true');
                    if (target.id) {
                        input.setAttribute('aria-activedescendant', target.id);
                    }
                }
            }

            // Enter
            if ((keyCode === 10 || keyCode === 13) && e.type === 'keydown') {
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

        // Suchbegriff
        const val = input.value.trim();
        const minlen = results.dataset.minlength ? parseInt(results.dataset.minlength, 10) : 3;
        const maxResults = results.dataset.maxresults ? parseInt(results.dataset.maxresults, 10) : 10;

        // Mindestlänge nicht erreicht: zurücksetzen, geplante Anfrage abbrechen, laufende Anfrage canceln
        if (val.length < minlen) {
            clearTimeout(state.debounceTimeout);
            if (state.controller) {
                state.controller.abort();
                state.controller = null;
            }
            state.lastQuery = '';
            results.classList.remove('autocomplete_searching');
            results.innerHTML = '';
            results.style.display = 'none';
            results.classList.remove('results');
            results.classList.add('no-results');
            this.collapse(state);
            return;
        }

        // Nur neue Suchbegriffe losschicken; wertgleiche Tasten (Shift etc.) lassen die Vorschläge stehen
        if (val === state.lastQuery) {
            return;
        }

        state.lastQuery = val;

        // Ergebnisse erst jetzt leeren, wenn wirklich eine neue Suche startet
        results.innerHTML = '';
        results.style.display = 'none';
        results.classList.remove('results');
        results.classList.add('no-results');
        this.collapse(state);

        // Anfrage ausführen
        this.performQuery(val, mode, maxResults, results, input, state);
    }

    performQuery(val, mode, maxResults, results, input, state) {
        const soc = results.dataset.searchonclick === 'true';

        // Debounce (pro Box)
        clearTimeout(state.debounceTimeout);
        state.debounceTimeout = setTimeout(() => {
            const url = results.dataset.searchurl;
            if (!url) {
                console.error('No data-searchurl defined on .search-autocomplete-results');
                return;
            }

            // Noch laufende Anfrage dieser Box abbrechen, damit keine veralteten Ergebnisse überschreiben
            if (state.controller) {
                state.controller.abort();
            }
            const controller = new AbortController();
            state.controller = controller;

            // Lade-Indikator: Box sichtbar machen + Spinner-Klasse setzen
            results.classList.add('autocomplete_searching');
            results.style.display = '';

            // Request-Daten wie vorher: s, m, mr
            const formData = new FormData();
            formData.append('s', val);
            formData.append('m', mode);
            formData.append('mr', String(maxResults));

            fetch(url, {
                method: 'POST',
                body: formData,
                cache: 'no-store',
                signal: controller.signal
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then((data) => {
                    state.controller = null;

                    results.classList.remove('autocomplete_searching');

                    // Ergebnisse einfügen
                    results.innerHTML = data;
                    results.style.display = '';

                    const items = results.querySelectorAll('li');

                    // ARIA: Listbox-ID + eindeutige Option-IDs vergeben
                    const list = results.querySelector('ul');
                    if (list) {
                        list.id = state.listId;
                    }
                    items.forEach((li, i) => {
                        li.id = state.listId + '-opt-' + i;
                    });

                    items.forEach((li) => {
                        li.addEventListener('click', () => {
                            if (mode === 'word') {
                                input.value = li.textContent.trim();
                                results.innerHTML = '';
                                results.style.display = 'none';
                                this.collapse(state);

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
                        this.collapse(state);
                    } else {
                        // Ergebnisse vorhanden
                        results.classList.remove('no-results');
                        results.classList.add('results');
                        // ARIA: Combobox als geöffnet markieren
                        input.setAttribute('aria-controls', state.listId);
                        input.setAttribute('aria-expanded', 'true');
                        input.removeAttribute('aria-activedescendant');
                    }
                })
                .catch((error) => {
                    // Abgebrochene (überholte) Anfrage ignorieren – kein Fehler
                    if (error.name === 'AbortError') {
                        return;
                    }
                    state.controller = null;
                    results.classList.remove('autocomplete_searching');
                    console.error('Autocomplete request failed:', error);
                    results.innerHTML = '';
                    results.style.display = 'none';
                    results.classList.remove('results');
                    results.classList.add('no-results');
                    this.collapse(state);
                });
        }, 250);
    }
}

// Init nach DOM-Ready
document.addEventListener('DOMContentLoaded', () => {
    new IndexSearchAutoComplete();
});
