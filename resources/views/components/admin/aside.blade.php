<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <span class="app-brand-logo demo">
                <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                        fill="#7367F0" />
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                        d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                        d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                        fill="#7367F0" />
                </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bold">لوحة التحكم</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- الرئيسية -->
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} ">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="الرئيسية">الرئيسية</div>
            </a>
        </li>
        <!-- إدارة المشاريع -->
        {{-- <li class="menu-item {{ request()->routeIs('admin.management') ? 'active' : '' }} ">
            <a href="{{ route('admin.management') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-clipboard-list"></i>
                <div data-i18n="إدارة المشاريع">إدارة المشاريع</div>
            </a>
        </li> --}}




        <!-- المشاريع -->
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="المشاريع">المشاريع</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.projects.create') ? 'active' : '' }}">
            <a href="{{ route('admin.projects.create') }}" class="menu-link">
<i class="menu-icon tf-icons ti ti-file-plus"></i>
                <div data-i18n="إضافة مشروع">إضافة مشروع</div>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.projects.index') ? 'active' : '' }}">
            <a href="{{ route('admin.projects.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-layout-kanban"></i>
                <div data-i18n="عرض المشاريع">عرض المشاريع</div>
            </a>
        </li>


        <!-- المستخدمون -->
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="المستخدمون">المستخدمون</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
            <a href="{{ route('admin.users.create') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user-plus"></i>
                <div data-i18n="إضافة مستخدم">إضافة مستخدم</div>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
            <a href="{{ route('admin.users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="عرض المستخدمين">عرض المستخدمين</div>
            </a>
        </li>


        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="الإعدادات">الإعدادات</span>
        </li>

        <li
            class="menu-item {{ request()->routeIs('admin.settings.edit') ||
            request()->routeIs('admin.settings.account') ||
            request()->routeIs('admin.settings.security')
                ? 'active open'
                : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon ti ti-settings"></i> <!-- أيقونة الإعدادات -->
                <div>الإعدادات</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item  {{ request()->routeIs('admin.settings.edit') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.edit') }}" class="menu-link">
                        <div>اعدادات الموقع</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.settings.account') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.account') }}" class="menu-link">
                        <div>اعدادات الحساب</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.settings.security') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.security') }}" class="menu-link">
                        <div>الحماية</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- تسجيل الخروج -->
        <li class="menu-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-link btn btn-link  text-start">
                    <i class="menu-icon tf-icons ti ti-logout"></i>
                    <div data-i18n="تسجيل الخروج">تسجيل الخروج</div>
                </button>
            </form>
        </li>
    </ul>
</aside>
