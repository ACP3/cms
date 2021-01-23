/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

((document) => {
    document.getElementById('search-advanced-toggle').addEventListener('click', () => {
        document.getElementById('search-advanced-wrapper').classList.toggle('hidden');
    });
})(document);
