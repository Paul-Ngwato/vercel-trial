/*
 * Keeps photo uploads under Vercel's ~4.5 MB request-body limit.
 *
 * Vercel rejects oversized requests before PHP ever runs
 * (413 FUNCTION_PAYLOAD_TOO_LARGE), so images are resized and re-encoded in
 * the browser before the form is submitted. Applies to every image upload on
 * the site: gallery, admin story / hero / logo / popup photos.
 */
(function () {
    'use strict';

    var MAX_DIMENSION = 2400;      // px — phones commonly shoot 4000–6000px
    var PER_FILE_TARGET = 700000;  // bytes — aim for this per photo
    var TOTAL_BUDGET = 3400000;    // bytes — whole request stays under Vercel's 4.5 MB
    var SKIP_BELOW = 250000;       // bytes — already small, leave untouched
    var QUALITY_LADDER = [0.82, 0.72, 0.62, 0.52];
    var SIZE_LADDER = [MAX_DIMENSION, 2000, 1600, 1200];

    /** input element -> Promise for its in-flight optimization */
    var pending = new WeakMap();

    function mb(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
        return Math.max(1, Math.round(bytes / 1024)) + ' KB';
    }

    function isCompressible(file) {
        // GIFs are skipped: re-encoding would destroy animations.
        return /^image\//.test(file.type) && file.type !== 'image/svg+xml' && file.type !== 'image/gif';
    }

    function loadImage(file) {
        // createImageBitmap honours EXIF rotation; the <img> fallback does too
        // in modern browsers via CSS image-orientation defaults.
        if (typeof createImageBitmap === 'function') {
            return createImageBitmap(file, { imageOrientation: 'from-image' }).catch(function () {
                return createImageBitmap(file);
            }).catch(function () { return null; });
        }
        return new Promise(function (resolve) {
            var url = URL.createObjectURL(file);
            var img = new Image();
            img.onload = function () { URL.revokeObjectURL(url); resolve(img); };
            img.onerror = function () { URL.revokeObjectURL(url); resolve(null); };
            img.src = url;
        });
    }

    function toBlob(canvas, type, quality) {
        return new Promise(function (resolve) {
            canvas.toBlob(function (blob) { resolve(blob); }, type, quality);
        });
    }

    /** Returns a smaller File, or the original when nothing is gained. */
    async function encode(image, file, maxDim, quality) {
        var w = image.width, h = image.height;
        if (!w || !h) return file;

        var scale = Math.min(1, maxDim / Math.max(w, h));
        var canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(w * scale));
        canvas.height = Math.max(1, Math.round(h * scale));
        var ctx = canvas.getContext('2d');
        ctx.drawImage(image, 0, 0, canvas.width, canvas.height);

        var blob = await toBlob(canvas, 'image/webp', quality);
        if (!blob || blob.type !== 'image/webp') {
            blob = await toBlob(canvas, 'image/jpeg', quality);
        }
        if (!blob || blob.size >= file.size) return file;

        var base = file.name.replace(/\.[^.]+$/, '');
        var ext = blob.type === 'image/webp' ? '.webp' : '.jpg';
        return new File([blob], base + ext, { type: blob.type, lastModified: Date.now() });
    }

    async function compressFile(file, maxDim, quality) {
        if (!isCompressible(file)) return file;
        var image = await loadImage(file);
        if (!image) return file;
        try {
            return await encode(image, file, maxDim, quality);
        } catch (e) {
            return file;
        } finally {
            if (image.close) image.close();
        }
    }

    async function compressToTarget(file) {
        var out = file;
        var q = 0;
        while (out.size > PER_FILE_TARGET && q < QUALITY_LADDER.length) {
            var next = await compressFile(out, SIZE_LADDER[q], QUALITY_LADDER[q]);
            if (next === out) break; // no further gain
            out = next;
            q++;
        }
        return out;
    }

    function total(files) {
        return files.reduce(function (n, f) { return n + f.size; }, 0);
    }

    /** Re-encode the biggest files harder until the batch fits the budget. */
    async function fitToBudget(files) {
        var out = files.slice();
        var tried = {};
        var guard = 0;
        while (total(out) > TOTAL_BUDGET && guard++ < 20) {
            var idx = -1, biggest = 0;
            for (var i = 0; i < out.length; i++) {
                if (tried[i] || !isCompressible(out[i])) continue;
                if (out[i].size > biggest) { biggest = out[i].size; idx = i; }
            }
            if (idx < 0) break;
            tried[idx] = true;
            var next = await compressFile(out[idx], 1200, 0.52);
            if (next !== out[idx]) out[idx] = next;
        }
        return out;
    }

    function statusFor(input) {
        var el = input.parentNode && input.parentNode.querySelector('.upload-compress-status');
        if (el) return el;
        el = document.createElement('small');
        el.className = 'upload-compress-status';
        el.style.cssText = 'display:block;margin-top:6px;font-size:12px;line-height:1.4;color:#6b7280;';
        input.insertAdjacentElement('afterend', el);
        return el;
    }

    async function processInput(input) {
        var files = Array.prototype.slice.call(input.files || []);
        if (!files.length) return;

        var status = statusFor(input);
        status.textContent = 'Optimizing ' + files.length + ' photo' + (files.length > 1 ? 's' : '') + '…';

        var out = [];
        for (var i = 0; i < files.length; i++) {
            out.push(files[i].size > SKIP_BELOW ? await compressToTarget(files[i]) : files[i]);
        }
        out = await fitToBudget(out);

        var dt = new DataTransfer();
        out.forEach(function (f) { dt.items.add(f); });
        input.files = dt.files;

        var before = total(files), after = total(out);
        if (after <= TOTAL_BUDGET) {
            status.textContent = after < before
                ? '✓ Optimized: ' + mb(before) + ' → ' + mb(after)
                : '✓ ' + mb(after) + ' ready to upload';
            status.style.color = '#047857';
        } else {
            status.textContent = '⚠ These photos are still ' + mb(after) + ' together — please choose fewer or smaller ones.';
            status.style.color = '#b91c1c';
        }
    }

    function track(input, job) {
        var previous = pending.get(input) || Promise.resolve();
        var next = previous.catch(function () {}).then(function () { return job(); });
        pending.set(input, next);
        next.catch(function () {});
        return next;
    }

    function fileInputs(scope) {
        return Array.prototype.filter.call(
            scope.querySelectorAll('input[type="file"]'),
            function (i) { return (i.accept || '').indexOf('image') !== -1; }
        );
    }

    function showFormError(form, message) {
        var existing = form.querySelector('.upload-compress-error');
        if (!existing) {
            existing = document.createElement('div');
            existing.className = 'upload-compress-error';
            existing.style.cssText =
                'background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;' +
                'border-radius:12px;padding:12px 16px;font-size:14px;margin-bottom:16px;';
            form.insertBefore(existing, form.firstChild);
        }
        existing.textContent = message;
        existing.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    document.addEventListener('change', function (e) {
        var input = e.target;
        if (!input || input.tagName !== 'INPUT' || input.type !== 'file') return;
        if (fileInputs({ querySelectorAll: function () { return [input]; } }).length === 0) return;
        track(input, function () { return processInput(input); });
    }, true);

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement)) return;

        var inputs = fileInputs(form);
        if (!inputs.length) return;

        var jobs = inputs.map(function (i) { return pending.get(i); }).filter(Boolean);
        var size = total(inputs.reduce(function (acc, i) {
            return acc.concat(Array.prototype.slice.call(i.files || []));
        }, []));

        if (!jobs.length && size <= TOTAL_BUDGET) return; // nothing to do

        e.preventDefault();
        e.stopImmediatePropagation();

        Promise.all(jobs.map(function (j) { return j.catch(function () {}); })).then(function () {
            var remaining = total(inputs.reduce(function (acc, i) {
                return acc.concat(Array.prototype.slice.call(i.files || []));
            }, []));
            if (remaining > TOTAL_BUDGET) {
                showFormError(
                    form,
                    'Your photos add up to ' + mb(remaining) + ' — the site accepts about 3.4 MB per upload. ' +
                    'Please choose fewer or smaller photos.'
                );
                return;
            }
            HTMLFormElement.prototype.submit.call(form);
        });
    }, true);
})();
