/* Client Management System – small vanilla JavaScript (no library).
   1. Delete confirmation dialog (native <dialog>)
   2. Live search + pagination without page reload (falls back to normal links/forms)
   3. Flash message auto-hide, double-submit protection                            */
(function () {
    'use strict';

    /* ------------------------------------------------------ live search --- */
    var searchSeq = 0;

    function resultsBox() { return document.getElementById('results'); }

    function loadResults(url) {
        var box = resultsBox();
        if (!box || !window.fetch || !window.DOMParser) { window.location.href = url; return; }
        var seq = ++searchSeq;
        box.classList.add('loading');
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function (r) { if (!r.ok) { throw new Error('bad status'); } return r.text(); })
            .then(function (html) {
                if (seq !== searchSeq) { return; }                 // a newer search already started
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var fresh = doc.getElementById('results');
                if (!fresh) { throw new Error('no results block'); }
                box.innerHTML = fresh.innerHTML;
                box.classList.remove('loading');
                if (window.history && history.replaceState) { history.replaceState(null, '', url); }
            })
            .catch(function () { window.location.href = url; });   // fall back to a normal page load
    }

    function searchUrl(form) {
        var value = form.elements.search.value.trim();
        return form.getAttribute('action') + (value ? '?search=' + encodeURIComponent(value) : '');
    }

    function syncClear(form) {
        var clear = document.getElementById('clear-search');
        if (clear) { clear.hidden = form.elements.search.value.trim() === ''; }
    }

    var typingTimer = null;

    document.addEventListener('input', function (e) {
        var input = e.target;
        if (!input.matches || !input.matches('.search-row input[name="search"]')) { return; }
        var form = input.form;
        syncClear(form);
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function () { loadResults(searchUrl(form)); }, 350);
    });

    document.addEventListener('submit', function (e) {
        var form = e.target;

        if (form.matches('.search-row')) {                        // search button / Enter key
            e.preventDefault();
            clearTimeout(typingTimer);
            loadResults(searchUrl(form));
            return;
        }

        // protect against double clicks on Save / Update / Delete
        var submit = form.querySelector('button[type="submit"]');
        if (submit && !e.defaultPrevented) {
            setTimeout(function () { submit.classList.add('is-loading'); submit.disabled = true; }, 0);
        }
    });

    /* ---------------------------------------------------------- clicks ---- */
    document.addEventListener('click', function (e) {
        var t = e.target;

        var opener = t.closest('[data-dialog]');                  // open delete dialog
        if (opener) {
            var dialog = document.getElementById(opener.getAttribute('data-dialog'));
            if (dialog && typeof dialog.showModal === 'function') { dialog.showModal(); }
            return;
        }

        var closer = t.closest('[data-dialog-close]');            // Cancel in the dialog
        if (closer) {
            var parent = closer.closest('dialog');
            if (parent) { parent.close(); }
            return;
        }

        if (t.tagName === 'DIALOG') { t.close(); return; }        // click on the dimmed background

        var page = t.closest('.pg-nav a.pb');                     // pagination without reload
        if (page && resultsBox()) { e.preventDefault(); loadResults(page.href); return; }

        var clear = t.closest('#clear-search');                   // clear the search box
        if (clear) {
            e.preventDefault();
            var form = document.querySelector('.search-row');
            form.elements.search.value = '';
            syncClear(form);
            loadResults(searchUrl(form));
        }
    });

    // coming back with the browser Back button: make the buttons usable again
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) { return; }
        Array.prototype.forEach.call(document.querySelectorAll('.is-loading'), function (b) {
            b.classList.remove('is-loading');
            b.disabled = false;
        });
    });

    /* ------------------------------------------------ page load ----------- */
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.querySelector('.search-row');
        if (form) {                                               // keep the cursor in the search box
            syncClear(form);
            var box = form.elements.search;
            if (box.value) { box.focus(); box.setSelectionRange(box.value.length, box.value.length); }
        }

        var flash = document.querySelector('.flash-ok');          // success message fades out
        if (flash) {
            var hide = setTimeout(function () { flash.classList.add('hide'); }, 7000);
            flash.addEventListener('mouseenter', function () { clearTimeout(hide); });
            flash.addEventListener('click', function () { flash.classList.add('hide'); });
        }
    });
})();
