(function (global) {
    'use strict';

    var finePointer = typeof window.matchMedia === 'function' &&
        window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    var reducedMotion = typeof window.matchMedia === 'function' &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var DEFAULTS = {
        travel: 140,
        trigger: 70,
        triggerBoost: 40,
        padding: 10,
        stiffness: 0.12,
        friction: 0.68,
        reaction: 0.16,
        jitter: 12,
        kick: 90
    };

    var records = [];
    var running = false;
    var busy = false;

    function clamp(v, lo, hi) {
        return Math.min(Math.max(v, lo), hi);
    }

    function earningInputs(form) {
        var nodes = form.querySelectorAll('input, select, textarea');
        var out = [];
        for (var i = 0; i < nodes.length; i++) {
            var el = nodes[i];
            var t = (el.type || '').toLowerCase();
            if (t === 'hidden' || t === 'submit' || t === 'button' || t === 'reset' || t === 'image') continue;
            out.push(el);
        }
        return out;
    }

    function isFilled(el) {
        var t = (el.type || '').toLowerCase();
        return (t === 'checkbox' || t === 'radio') ? el.checked : String(el.value || '').trim() !== '';
    }

    function ratioOf(rec) {
        var filled = 0;
        for (var i = 0; i < rec.inputs.length; i++) {
            if (isFilled(rec.inputs[i])) filled++;
        }
        return rec.inputs.length ? filled / rec.inputs.length : 1;
    }

    function measure(rec) {
        var r = rec.button.getBoundingClientRect();
        if (!r.width || !r.height) return;
        var b = rec.boundary.getBoundingClientRect();
        rec.maxX = Math.max(0, (b.width - r.width) / 2);
        rec.maxY = Math.max(0, (b.height - r.height) / 2);
        rec.measured = true;
    }

    function setTarget(rec, tx, ty) {
        rec.tx = tx;
        rec.ty = ty;
    }

    function plan(rec, px, py) {
        var ratio = ratioOf(rec);
        if (!rec.on || rec.won || rec.keyboardCaught || ratio >= 1) {
            rec.threat = false;
            setTarget(rec, 0, 0);
            return;
        }
        var r = rec.button.getBoundingClientRect();
        var cx = r.left + r.width / 2;
        var cy = r.top + r.height / 2;
        var dx = cx - px;
        var dy = cy - py;
        var dist = Math.sqrt(dx * dx + dy * dy);
        var trigger = rec.config.trigger + (1 - ratio) * rec.config.triggerBoost;
        if (dist > trigger) {
            rec.threat = false;
            setTarget(rec, 0, 0);
            return;
        }
        if (!rec.threat) {
            rec.threat = true;
            rec.boost = 1;
            rec.jx = (Math.random() * 2 - 1) * rec.config.jitter;
            rec.jy = (Math.random() * 2 - 1) * rec.config.jitter;
        }
        var travel = rec.config.travel * (1 - ratio);
        if (travel < 1) {
            setTarget(rec, 0, 0);
            return;
        }
        var nx = dist ? dx / dist : 1;
        var ny = dist ? dy / dist : 0;
        var tx = clamp(nx * travel + rec.jx, -rec.maxX, rec.maxX);
        var ty = clamp(ny * travel + rec.jy, -rec.maxY, rec.maxY);
        if (Math.abs(tx) < 4 && Math.abs(ty) < 4) {
            tx = clamp(-travel, -rec.maxX, rec.maxX);
            ty = 0;
        }
        setTarget(rec, tx, ty);
    }

    function kick(rec) {
        var ang = Math.random() * Math.PI * 2;
        var kx = Math.cos(ang) * rec.config.kick;
        var ky = Math.sin(ang) * rec.config.kick;
        setTarget(rec, clamp(rec.x + kx, -rec.maxX, rec.maxX), clamp(rec.y + ky, -rec.maxY, rec.maxY));
        rec.vx += kx * 0.55;
        rec.vy += ky * 0.55;
        rec.boost = 1;
        rec.threat = true;
    }

    function updateTether(rec) {
        if (!rec.line || !rec.svg) return;
        var r = rec.button.getBoundingClientRect();
        if (!r.width || !r.height) {
            rec.svg.style.display = 'none';
            return;
        }
        var card = rec.card.getBoundingClientRect();
        var hx = r.left - rec.x + r.width / 2 - card.left;
        var hy = r.top - rec.y + r.height / 2 - card.top;
        var cx = r.left + r.width / 2 - card.left;
        var cy = r.top + r.height / 2 - card.top;
        var visible = Math.abs(rec.x) > 1.5 || Math.abs(rec.y) > 1.5;
        rec.svg.style.display = visible ? 'block' : 'none';
        if (!visible) return;
        rec.anchor.setAttribute('cx', hx);
        rec.anchor.setAttribute('cy', hy);
        rec.line.setAttribute('x1', hx);
        rec.line.setAttribute('y1', hy);
        rec.line.setAttribute('x2', cx);
        rec.line.setAttribute('y2', cy);
    }

    function animate() {
        for (var i = 0; i < records.length; i++) {
            var rec = records[i];
            var st = rec.config.stiffness + rec.boost * rec.config.reaction;
            rec.vx += (rec.tx - rec.x) * st;
            rec.vx *= rec.config.friction;
            rec.vy += (rec.ty - rec.y) * st;
            rec.vy *= rec.config.friction;
            rec.x += rec.vx;
            rec.y += rec.vy;
            rec.boost *= 0.88;
            if (!rec.measured) measure(rec);
            if (rec.measured) {
                if (rec.x < -rec.maxX) rec.x = -rec.maxX;
                else if (rec.x > rec.maxX) rec.x = rec.maxX;
                if (rec.y < -rec.maxY) rec.y = -rec.maxY;
                else if (rec.y > rec.maxY) rec.y = rec.maxY;
            }
            rec.button.style.transform =
                'translate(' + rec.x.toFixed(1) + 'px, ' + rec.y.toFixed(1) + 'px)';
            updateTether(rec);
        }
        running = false;
        ensureLoop();
    }

    function ensureLoop() {
        if (running) return;
        running = true;
        requestAnimationFrame(animate);
    }

    function onDocMove(e) {
        if (busy || !records.length) return;
        busy = true;
        requestAnimationFrame(function () {
            busy = false;
            for (var i = 0; i < records.length; i++) plan(records[i], e.clientX, e.clientY);
        });
    }

    function wire(rec) {
        function onInput() {
            if (ratioOf(rec) >= 1) {
                rec.won = true;
                setTarget(rec, 0, 0);
            }
        }
        function onFocus() {
            rec.keyboardCaught = true;
            rec.won = true;
            setTarget(rec, 0, 0);
        }
        function onResize() {
            measure(rec);
            setTarget(rec, 0, 0);
        }
        function onMouseDown(e) {
            if (!rec.on || ratioOf(rec) >= 1) return;
            e.preventDefault();
            e.stopPropagation();
            rec.guardClick = true;
        }
        function onClick(e) {
            if (rec.guardClick && ratioOf(rec) < 1) {
                e.preventDefault();
                e.stopPropagation();
                kick(rec);
            }
            rec.guardClick = false;
        }
        for (var i = 0; i < rec.inputs.length; i++) {
            rec.inputs[i].addEventListener('input', onInput);
        }
        rec.button.addEventListener('focus', onFocus);
        rec.button.addEventListener('mousedown', onMouseDown);
        rec.button.addEventListener('click', onClick);
        window.addEventListener('resize', onResize);
    }

    function init(button, opts) {
        if (!button || typeof button.closest !== 'function') return;
        var form = button.closest('form');
        if (!form) return;
        var inputs = earningInputs(form);
        if (!inputs.length) return;
        var config = {};
        for (var k in DEFAULTS) config[k] = DEFAULTS[k];
        if (opts) {
            for (var j in opts) config[j] = opts[j];
        }
        var rec = {
            button: button,
            inputs: inputs,
            card: button.closest('.form-card') || form.closest('.form-card') || form.parentElement || form,
            boundary: button.closest('.button-boundary') || button.parentElement || form,
            config: config,
            on: finePointer && !reducedMotion,
            won: false,
            keyboardCaught: false,
            threat: false,
            guardClick: false,
            boost: 0,
            measured: false,
            maxX: 0,
            maxY: 0,
            x: 0,
            y: 0,
            tx: 0,
            ty: 0,
            vx: 0,
            vy: 0,
            jx: 0,
            jy: 0
        };
        button.style.transition = 'none';
        if (ratioOf(rec) >= 1) rec.won = true;
        measure(rec);
        records.push(rec);

        var ns = 'http://www.w3.org/2000/svg';
        var svg = document.createElementNS(ns, 'svg');
        svg.setAttribute('class', 'runaway-tether');
        svg.style.cssText = 'position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none; overflow:visible; z-index:0;';
        var line = document.createElementNS(ns, 'line');
        line.setAttribute('stroke', 'rgba(37,99,235,0.55)');
        line.setAttribute('stroke-width', '2');
        line.setAttribute('stroke-dasharray', '5 4');
        line.setAttribute('stroke-linecap', 'round');
        var anchor = document.createElementNS(ns, 'circle');
        anchor.setAttribute('r', '4');
        anchor.setAttribute('fill', 'rgba(37,99,235,0.9)');
        svg.appendChild(line);
        svg.appendChild(anchor);
        rec.svg = svg;
        rec.line = line;
        rec.anchor = anchor;
        if (typeof rec.card.style !== 'undefined') rec.card.style.position = 'relative';
        button.style.position = 'relative';
        button.style.zIndex = '1';
        rec.card.appendChild(svg);

        wire(rec);
        ensureLoop();
        return rec;
    }

    function refresh() {
        for (var i = 0; i < records.length; i++) {
            var rec = records[i];
            measure(rec);
            if (ratioOf(rec) >= 1) {
                rec.won = true;
            } else if (!rec.keyboardCaught) {
                rec.won = false;
            }
            setTarget(rec, 0, 0);
        }
    }

    function boot() {
        var list = document.querySelectorAll('[data-runaway]');
        for (var i = 0; i < list.length; i++) {
            var el = list[i];
            var opts = {};
            if (el.hasAttribute('data-runaway-travel')) opts.travel = Number(el.getAttribute('data-runaway-travel'));
            if (el.hasAttribute('data-runaway-trigger')) opts.trigger = Number(el.getAttribute('data-runaway-trigger'));
            if (el.hasAttribute('data-runaway-padding')) opts.padding = Number(el.getAttribute('data-runaway-padding'));
            init(el, opts);
        }
    }

    document.addEventListener('mousemove', onDocMove);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    global.RunawayButton = { init: init, refresh: refresh };
})(window);