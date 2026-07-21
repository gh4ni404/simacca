<script>
function compressImage(file, callback) {
    const MAX_BYTES = 1048576;
    const TARGET_HEADROOM = 0.9;

    createImageBitmap(file).then(function (bitmap) {
        var w0 = bitmap.width, h0 = bitmap.height;
        var format = 'image/jpeg';
        var mimeType = file.type;

        function toBlob(canvas, w, h, q) {
            return new Promise(function (resolve) {
                var c = document.createElement('canvas');
                c.width = w;
                c.height = h;
                var ctx = c.getContext('2d');
                if (format === 'image/jpeg' && mimeType === 'image/png') {
                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, w, h);
                }
                ctx.drawImage(bitmap, 0, 0, w0, h0, 0, 0, w, h);
                c.toBlob(function (blob) {
                    resolve(blob);
                }, format, q);
            });
        }

        function makeFile(blob) {
            blob = blob || file;
            return new File([blob], file.name.replace(/\.\w+$/, '.jpg'), {
                type: format,
                lastModified: Date.now()
            });
        }

        // Estimate initial dimensions: area ratio heuristic
        var areaRatio = Math.sqrt((MAX_BYTES * TARGET_HEADROOM) / file.size);
        var dimW = Math.min(w0, Math.round(1920 * Math.min(1, areaRatio * 1.3)));
        var dimH = Math.round(dimW * (h0 / w0));
        if (dimH > 1920) { dimH = 1920; dimW = Math.round(dimH * (w0 / h0)); }
        dimW = Math.max(320, dimW);
        dimH = Math.max(320, dimH);

        // Binary search over quality at current dimensions
        function searchQuality(w, h, lo, hi, best) {
            if (hi - lo < 0.02 || lo >= hi) return Promise.resolve(best);
            var mid = (lo + hi) / 2;
            return toBlob(w, h, mid).then(function (blob) {
                if (blob.size <= MAX_BYTES) {
                    best = blob;
                    return searchQuality(w, h, mid, hi, best);
                } else {
                    return searchQuality(w, h, lo, mid, best);
                }
            });
        }

        function tryDimensions(w, h) {
            return toBlob(w, h, 0.85).then(function (base) {
                if (base.size <= MAX_BYTES) {
                    return searchQuality(w, h, 0.85, 1.0, base);
                } else {
                    return toBlob(w, h, 0.3).then(function (floor) {
                        if (floor.size > MAX_BYTES && (w <= 320 || h <= 320)) {
                            return base;
                        }
                        if (floor.size <= MAX_BYTES) {
                            return searchQuality(w, h, 0.3, 0.85, floor);
                        }
                        var ratio = Math.sqrt((MAX_BYTES * TARGET_HEADROOM) / floor.size);
                        var nw = Math.max(320, Math.round(w * ratio));
                        var nh = Math.round(nw * (h / w));
                        return tryDimensions(nw, nh);
                    });
                }
            });
        }

        tryDimensions(dimW, dimH).then(function (best) {
            callback(makeFile(best));
            bitmap.close();
        })['catch'](function () {
            callback(makeFile(null));
            bitmap.close();
        });
    })['catch'](function () {
        // Fallback to old Image-based method
        fallbackCompress(file, callback);
    });
}

function fallbackCompress(file, callback) {
    var reader = new FileReader();
    reader.onload = function (e) {
        var img = new Image();
        img.onload = function () {
            var quality = 0.85, tw = 1920, th = 1920;
            var mime = 'image/jpeg';
            function encode(resolve) {
                var w = img.width, h = img.height;
                if (w > tw || h > th) {
                    var r = Math.min(tw / w, th / h);
                    w = Math.round(w * r); h = Math.round(h * r);
                }
                var c = document.createElement('canvas');
                c.width = w; c.height = h;
                var ctx = c.getContext('2d');
                if (mime === 'image/jpeg' && file.type === 'image/png') {
                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, w, h);
                }
                ctx.drawImage(img, 0, 0, w, h);
                c.toBlob(resolve, mime, quality);
            }
            function step() {
                encode(function (blob) {
                    if (blob.size <= 1048576 || (tw <= 640 && quality <= 0.3)) {
                        callback(new File([blob], file.name.replace(/\.\w+$/, '.jpg'), { type: mime, lastModified: Date.now() }));
                    } else if (blob.size > 1048576 && quality > 0.3) {
                        quality = Math.max(0.3, quality - 0.15);
                        step();
                    } else {
                        tw = Math.max(640, Math.round(tw * 0.75));
                        th = Math.max(640, Math.round(th * 0.75));
                        quality = 0.85;
                        step();
                    }
                });
            }
            step();
        };
        img.onerror = function () { callback(file); };
        img.src = e.target.result;
    };
    reader.onerror = function () { callback(file); };
    reader.readAsDataURL(file);
}

/**
 * Handle file input preview with compression.
 * Call from input[type=file] onchange.
 * Input must have a sibling or nearby #preview, #previewContainer, #fileName, #uploadArea.
 */
function previewAndCompress(input, options) {
    const opts = Object.assign({
        maxSizeMB: 5,
        previewId: 'preview',
        containerId: 'previewContainer',
        fileNameId: 'fileName',
        uploadAreaId: 'uploadArea',
    }, options || {});

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
        if (parseFloat(fileSizeMB) > opts.maxSizeMB) {
            alert('Ukuran file terlalu besar! Maksimal ' + opts.maxSizeMB + 'MB');
            input.value = '';
            return;
        }
        compressImage(file, function (compressedFile) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById(opts.previewId);
                const container = document.getElementById(opts.containerId);
                if (preview) preview.src = e.target.result;
                if (container) container.classList.remove('hidden');
            };
            reader.readAsDataURL(compressedFile);
            const dispSize = (compressedFile.size / 1024 / 1024).toFixed(2);
            const fileNameEl = document.getElementById(opts.fileNameId);
            if (fileNameEl) {
                fileNameEl.textContent = file.name + ' (' + dispSize + ' MB' +
                    (compressedFile.size < file.size ? ' | dikompres)' : ')');
            }
            const ua = document.getElementById(opts.uploadAreaId);
            if (ua) {
                ua.style.borderColor = '#22c55e';
                ua.style.background = '#f0fdf4';
            }
            const dt = new DataTransfer();
            dt.items.add(compressedFile);
            input.files = dt.files;
        });
    }
}
</script>
