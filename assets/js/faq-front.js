/**
 * Modern SaaS FAQ — Front-End Logic v4.0.0
 * Vanilla JS · No Dependencies
 */
;(function () {
    'use strict';

    /* ─── مقداردهی اولیه ─── */
    function initFAQ() {
        var wraps = document.querySelectorAll('.msaas-faq-wrap');
        if (!wraps.length) return;

        wraps.forEach(function (wrap) {
            // اگر lazy است و هنوز load نشده، رد شو
            if (wrap.classList.contains('msaas-faq-lazy') && !wrap.classList.contains('msaas-faq-loaded')) {
                return;
            }

            var allowMulti = wrap.getAttribute('data-multi') === 'yes';
            var triggers   = wrap.querySelectorAll('.msaas-faq-trigger');

            triggers.forEach(function (trigger) {
                // جلوگیری از bind مجدد
                if (trigger.dataset.bound === 'true') return;
                trigger.dataset.bound = 'true';

                trigger.addEventListener('click', function () {
                    var item = this.closest('.msaas-faq-item');

                    if (!allowMulti) {
                        wrap.querySelectorAll('.msaas-faq-item.is-open').forEach(function (openItem) {
                            if (openItem !== item) {
                                closeItem(openItem);
                            }
                        });
                    }

                    if (item.classList.contains('is-open')) {
                        closeItem(item);
                    } else {
                        openItem(item);
                    }
                });

                /* ─── دسترسی‌پذیری: کلید Enter و Space ─── */
                trigger.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            /* آیتم‌هایی که از ابتدا باز هستند */
            wrap.querySelectorAll('.msaas-faq-item.is-open').forEach(function (item) {
                item.querySelector('.msaas-faq-trigger').setAttribute('aria-expanded', 'true');
            });
        });
    }

    function openItem(item) {
        item.classList.add('is-open');
        var trigger = item.querySelector('.msaas-faq-trigger');
        if (trigger) trigger.setAttribute('aria-expanded', 'true');
    }

    function closeItem(item) {
        item.classList.remove('is-open');
        var trigger = item.querySelector('.msaas-faq-trigger');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
    }

    /* ─── Lazy Load با IntersectionObserver ─── */
    function initLazyLoad() {
        var lazyWraps = document.querySelectorAll('.msaas-faq-lazy:not(.msaas-faq-loaded)');
        if (!lazyWraps.length) return;

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var wrap = entry.target;
                        wrap.classList.remove('msaas-faq-lazy');
                        wrap.classList.add('msaas-faq-loaded');
                        observer.unobserve(wrap);
                        initFAQ(); // مقداردهی مجدد برای wrapهای جدید
                    }
                });
            }, {
                rootMargin: '200px 0px', // شروع بارگذاری 200px قبل از ورود به viewport
                threshold: 0.01
            });

            lazyWraps.forEach(function (wrap) {
                observer.observe(wrap);
            });
        } else {
            // Fallback برای مرورگرهای قدیمی: همه را فوراً فعال کن
            lazyWraps.forEach(function (wrap) {
                wrap.classList.remove('msaas-faq-lazy');
                wrap.classList.add('msaas-faq-loaded');
            });
            initFAQ();
        }
    }

    /* ─── اجرای بلافاصله بعد از DOM Ready ─── */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initFAQ();
            initLazyLoad();
        });
    } else {
        initFAQ();
        initLazyLoad();
    }

    /* ─── پشتیبانی از المنتور (ویرایشگر زنده) ─── */
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('elementor/popup/show', function () {
            initFAQ();
            initLazyLoad();
        });
    }

    /* ─── سازگاری با Elementor Frontend Init ─── */
    document.addEventListener('elementor/frontend/init', function () {
        if (
            typeof window.elementorFrontend !== 'undefined' &&
            typeof window.elementorFrontend.hooks !== 'undefined'
        ) {
            window.elementorFrontend.hooks.addAction(
                'frontend/element_ready/shortcode.default',
                function () {
                    initFAQ();
                    initLazyLoad();
                }
            );
        }
    });
})();
