<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المشاريع بكل كفاءة | {{ $setting->site_name }}</title>
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> --}}
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('website/style.css') }}" />

</head>

<body>
    <!-- الشريط العلوي -->
    <header>
        <div class="container">
            <nav>
                <a href="#" class="logo">
                    <i class="fas fa-project-diagram"></i>
                    <span> {{ $setting->site_name }}</span>
                </a>
                <ul class="nav-links">
                    <li><a href="#">الرئيسية</a></li>
                    <li><a href="#features">المميزات</a></li>
                    <li><a href="#how-it-works">كيف يعمل</a></li>
                    <li><a href="#pricing">الأسعار</a></li>
                    <li><a href="#contact">اتصل بنا</a></li>
                </ul>
                <div class="nav-buttons">
                    <a href="{{ route('login.form') }}" class="btn btn-outline">تسجيل الدخول</a>
                    <a href="#contact" class="btn btn-primary">ابدأ مجانًا</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- قسم الهيرو -->
    <section class="hero">
        <div class="container">
            <h1>أدِر مشاريعك بكل كفاءة واحترافية</h1>
            <p>منصة متكاملة لإدارة المشاريع والمهام وفرق العمل، صممت لتبسيط عملك وزيادة إنتاجيتك</p>
            <div class="hero-buttons">
                <a href="#contact" class="btn btn-primary">ابدأ الآن</a>
                <a href="#" class="btn btn-outline"><i class="fas fa-play"></i> شاهد الفيديو</a>
            </div>
        </div>
    </section>

    <!-- قسم المميزات -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>لماذا تختار {{ $setting->site_name }}</h2>
                <p>منصة إدارة المشاريع الأكثر تطورًا وسهولة في الاستخدام</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3>إدارة المهام</h3>
                    <p>نظم مهامك وحدد أولوياتها وتواريخ تسليمها بسهولة</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>فرق العمل</h3>
                    <p>أدر فرق العمل ووزع المهام واتبع تقدم كل عضو</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>تقارير مفصلة</h3>
                    <p>احصل على تقارير أداء دقيقة لمشاريعك وفرقك</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>تقويم متكامل</h3>
                    <p>خطط لمشاريعك وتابع مواعيدك في مكان واحد</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-cloud"></i>
                    </div>
                    <h3>سحابي آمن</h3>
                    <p>بياناتك محفوظة بأمان في السحابة الإلكترونية</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>متاح على جميع الأجهزة</h3>
                    <p>استخدم المنصة من حاسوبك أو هاتفك أينما كنت</p>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم كيف يعمل -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="section-title">
                <h2>كيف تعمل المنصة؟</h2>
                <p>ثلاث خطوات بسيطة لبدء إدارة مشاريعك باحترافية</p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>أنشئ مشروعك</h3>
                        <p>قم بإنشاء مشروع جديد وحدد أهدافه وتواريخه المهمة</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>أضف فريقك</h3>
                        <p>قم بدعوة أعضاء فريقك وتعيين الأدوار والصلاحيات</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>ابدأ الإدارة</h3>
                        <p>وزع المهام، تابع التقدم، واحصل على التقارير</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم العروض -->
    <section class="pricing" id="pricing">
        <div class="container">
            <div class="section-title">
                <h2>خطط الأسعار</h2>
                <p>اختر الخطة التي تناسب احتياجاتك</p>
            </div>
            <div class="pricing-grid">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3>الاساسية</h3>
                        <div class="price">$0 <span>/شهر</span></div>
                    </div>
                    <div class="pricing-body">
                        <ul class="pricing-features">
                            <li><i class="fas fa-check"></i> 5 مشاريع نشطة</li>
                            <li><i class="fas fa-check"></i> 10 أعضاء في الفريق</li>
                            <li><i class="fas fa-check"></i> 1 جيجا مساحة تخزين</li>
                            <li><i class="fas fa-check"></i> تقارير أساسية</li>
                        </ul>
                        <a href="#contact" class="btn btn-outline" style="width: 100%; text-align: center;">ابدأ
                            مجانًا</a>
                    </div>
                </div>
                <div class="pricing-card popular">
                    <div class="popular-badge">الأكثر شيوعًا</div>
                    <div class="pricing-header">
                        <h3>المحترفة</h3>
                        <div class="price">$29 <span>/شهر</span></div>
                    </div>
                    <div class="pricing-body">
                        <ul class="pricing-features">
                            <li><i class="fas fa-check"></i> مشاريع غير محدودة</li>
                            <li><i class="fas fa-check"></i> 50 عضو في الفريق</li>
                            <li><i class="fas fa-check"></i> 10 جيجا مساحة تخزين</li>
                            <li><i class="fas fa-check"></i> تقارير متقدمة</li>
                            <li><i class="fas fa-check"></i> دعم فني متميز</li>
                        </ul>
                        <a href="#contact" class="btn btn-primary" style="width: 100%; text-align: center;">اشترك
                            الآن</a>
                    </div>
                </div>
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3>الشركات</h3>
                        <div class="price">$99 <span>/شهر</span></div>
                    </div>
                    <div class="pricing-body">
                        <ul class="pricing-features">
                            <li><i class="fas fa-check"></i> مشاريع غير محدودة</li>
                            <li><i class="fas fa-check"></i> أعضاء غير محدودين</li>
                            <li><i class="fas fa-check"></i> مساحة تخزين غير محدودة</li>
                            <li><i class="fas fa-check"></i> تقارير مخصصة</li>
                            <li><i class="fas fa-check"></i> دعم فني على مدار الساعة</li>
                            <li><i class="fas fa-check"></i> تدريب مخصص</li>
                        </ul>
                        <a href="#contact" class="btn btn-outline" style="width: 100%; text-align: center;">اتصل
                            بالبيع</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الشهادات -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-title">
                <h2>آراء عملائنا</h2>
                <p>ماذا يقول عملاؤنا عن منصتنا</p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        " {{ $setting->site_name }} غيرت طريقة إدارتنا للمشاريع تمامًا، وفرت لنا الوقت والجهد وزادت من
                        إنتاجية الفريق بشكل
                        ملحوظ."
                    </div>
                    <div class="testimonial-author">
                        <img src="{{ asset('cp/assets/img/avatars/1.png') }}" alt="صورة العميل" class="author-avatar">
                        <div class="author-info">
                            <h4>أحمد محمد</h4>
                            <p>مدير مشاريع، شركة التقنية</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "البساطة والقوة في آن واحد، هذه المنصة ساعدتنا على إنجاز مشاريعنا في الوقت المحدد وبكفاءة
                        عالية."
                    </div>
                    <div class="testimonial-author">
                        <img src="{{ asset('cp/assets/img/avatars/2.png') }}" alt="صورة العميل" class="author-avatar">
                        <div class="author-info">
                            <h4>سارة عبدالله</h4>
                            <p>رئيسة فريق التصميم</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "الدعم الفني ممتاز والمنصة تتطور باستمرار، نستخدمها لإدارة جميع مشاريعنا ولن نستطيع الاستغناء
                        عنها."
                    </div>
                    <div class="testimonial-author">
                        <img src="{{ asset('cp/assets/img/avatars/3.png') }}" alt="صورة العميل" class="author-avatar">
                        <div class="author-info">
                            <h4>خالد سعيد</h4>
                            <p>مدير تنفيذي، مؤسسة الإبداع</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الاتصال -->
    <section class="contact" id="contact">
        <div class="container">
            <div class="section-title">
                <h2>اتصل بنا</h2>
                <p>نحن هنا لمساعدتك في أي استفسار</p>
            </div>
            <div class="contact-container">
                <div class="contact-info">
                    <h3>تواصل مع فريق {{ $setting->site_name }}</h3>
                    <p>لديك استفسار أو تحتاج إلى مساعدة؟ فريق الدعم لدينا متاح على مدار الساعة لمساعدتك في أي شيء
                        تحتاجه.</p>
                    <ul class="contact-details">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $setting->site_address }}</span>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <span>{{ $setting->site_phone }}</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>{{ $setting->site_email }}</span>
                        </li>
                    </ul>
                </div>
                <div class="contact-form">
                    <form>
                        <div class="form-group">
                            <label for="name">الاسم الكامل</label>
                            <input type="text" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">البريد الإلكتروني</label>
                            <input type="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">الموضوع</label>
                            <input type="text" id="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="message">الرسالة</label>
                            <textarea id="message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">إرسال الرسالة</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- الفوتر -->
    <footer>
        <div class="container">
            <div class="footer-container">
                <div class="footer-col">
                    <h3> {{ $setting->site_name }}</h3>
                    <p>منصة متكاملة لإدارة المشاريع والمهام وفرق العمل، صممت لتبسيط عملك وزيادة إنتاجيتك.</p>
                    <div class="social-links">
                        <a href="{{ $setting->twitter_url }}"><i class="fab fa-twitter"></i></a>
                        <a href="{{ $setting->facebook_url }}"><i class="fab fa-facebook-f"></i></a>
                        <a href="{{ $setting->linkedin_url }}"><i class="fab fa-linkedin-in"></i></a>
                        <a href="{{ $setting->instagram_url }}"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3>روابط سريعة</h3>
                    <ul class="footer-links">
                        <li><a href="#">الرئيسية</a></li>
                        <li><a href="#features">المميزات</a></li>
                        <li><a href="#how-it-works">كيف يعمل</a></li>
                        <li><a href="#pricing">الأسعار</a></li>
                        <li><a href="#testimonials">الشهادات</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>الموارد</h3>
                    <ul class="footer-links">
                        <li><a href="#">الأسئلة الشائعة</a></li>
                        <li><a href="#">المساعدة</a></li>
                        <li><a href="#">المدونة</a></li>
                        <li><a href="#">الخصوصية</a></li>
                        <li><a href="#">الشروط والأحكام</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>النشرة البريدية</h3>
                    <p>اشترك في نشرتنا البريدية لتصلك آخر التحديثات والعروض.</p>
                    <form>
                        <input type="email" placeholder="بريدك الإلكتروني" required>
                        <button type="submit" class="btn btn-primary"
                            style="width: 100%; margin-top: 10px;">اشترك</button>
                    </form>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; {{ now()->year }} {{ $setting->site_name }}. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تحويل الأرقام إلى عربية
            function convertToArabicNumbers(str) {
                const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
                return str.replace(/\d/g, function(digit) {
                    return arabicNumbers[parseInt(digit)];
                });
            }

            // تحويل الأسعار والتواريخ
            const prices = document.querySelectorAll('.price');
            prices.forEach(price => {
                price.innerHTML = convertToArabicNumbers(price.innerHTML);
            });

            // التنقل السلس
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();

                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;

                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // إضافة تأثير عند التمرير
            window.addEventListener('scroll', function() {
                const scrollPosition = window.scrollY;
                const header = document.querySelector('header');

                if (scrollPosition > 100) {
                    header.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.1)';
                } else {
                    header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
                }
            });

            // تأثير للصور عند الظهور
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });

            const animatedElements = document.querySelectorAll(
                '.hero-image, .feature-card, .step, .pricing-card, .testimonial-card');
            animatedElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'all 0.6s ease-out';
                observer.observe(el);
            });
        });
    </script>
</body>

</html>
