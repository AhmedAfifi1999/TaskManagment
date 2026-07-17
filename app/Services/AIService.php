<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIService
{
    private ?string $apiKey;
    private string  $primaryModel  = 'gemini-2.5-flash-lite';
    private string  $fallbackModel = 'gemini-2.5-flash';
    private string  $baseUrl       = 'https://generativelanguage.googleapis.com/v1beta/models/';

    // ─────────────────────────────────────────────────────────────
    // INTENT RULES — ordered from MOST specific to MOST general
    // Each rule: [query_id, required_signals[], context_signals[]]
    // ALL required_signals must appear AND at least one context_signal
    // ─────────────────────────────────────────────────────────────
    private array $intentRules = [

        // ── إحصائيات ──────────────────────────────────────────────
        ['my_stats', ['إحصائيات', 'ملخص', 'نظرة عامة', 'داشبورد', 'وضعي', 'تقريري'], []],

        // ── مهام متأخرة ───────────────────────────────────────────
        ['my_tasks_overdue', ['متأخر'],          ['مهام', 'مهمة', 'مهامي']],
        ['my_tasks_overdue', ['فات وقت'],        []],
        ['my_tasks_overdue', ['فاتت'],           ['مهام', 'مهمة', 'مهامي']],
        ['my_tasks_overdue', ['overdue'],        []],

        // ── مهام منتهية / مكتملة ───────────────────────────────────
        ['my_tasks_completed', ['منتهية'],       ['مهام', 'مهمة', 'مهامي']],
        ['my_tasks_completed', ['مكتمل'],        ['مهام', 'مهمة', 'مهامي']],
        ['my_tasks_completed', ['منجز'],         ['مهام', 'مهمة', 'مهامي']],
        ['my_tasks_completed', ['خلصت', 'خلصتها', 'انتهيت منها'], ['مهام', 'مهمة', 'مهامي']],
        ['my_tasks_completed', ['completed'],    ['task']],

        // ── مهام لم تبدأ / معلقة ──────────────────────────────────
        ['my_tasks_pending', ['لم تبدأ', 'لم أبدأ'], []],
        ['my_tasks_pending', ['معلق'],           ['مهام', 'مهمة', 'مهامي']],
        ['my_tasks_pending', ['ما بدأت'],        []],
        ['my_tasks_pending', ['غير مكتمل'],      ['مهام', 'مهمة', 'مهامي']],
        ['my_tasks_pending', ['لسا ما'],         ['مهام', 'مهمة']],
        ['my_tasks_pending', ['pending'],        ['task']],

        // ── مهام جارية / نشطة ─────────────────────────────────────
        ['my_tasks_active', ['جاري', 'جارية'],  ['مهام', 'مهمة', 'مهامي']],
        ['my_tasks_active', ['قيد التنفيذ'],    []],
        ['my_tasks_active', ['نشط', 'نشطة'],    ['مهام', 'مهمة', 'مهامي']],
        ['my_tasks_active', ['in progress'],    []],

        // ── مهام قريبة الانتهاء ────────────────────────────────────
        ['my_tasks_due_soon', ['قريب', 'قريبة'], ['مهام', 'مهمة', 'مهامي', 'انتهاء']],
        ['my_tasks_due_soon', ['هذا الأسبوع'],  ['مهام', 'مهمة']],
        ['my_tasks_due_soon', ['عاجلة'],        ['مهام', 'مهمة', 'مهامي']],
        ['my_tasks_due_soon', ['due soon'],     []],

        // ── عدد المهام ────────────────────────────────────────────
        ['my_tasks_count', ['كم', 'عدد'],       ['مهمة', 'مهام', 'مهامي']],
        ['my_tasks_count', ['إحصاء'],           ['مهمة', 'مهام']],

        // ── أسماء المهام فقط ──────────────────────────────────────
        ['my_tasks', ['اسماء', 'أسماء'],        ['مهمة', 'مهام', 'مهامي']],

        // ── جميع المهام (عام) ─────────────────────────────────────
        ['my_tasks', ['مهامي'],                 []],
        ['my_tasks', ['مهام'],                  ['كل', 'جميع', 'عرض', 'اعرض', 'اظهر']],
        ['my_tasks', ['tasks'],                 []],

        // ══ مشاريع ════════════════════════════════════════════════

        // ── مشاريع مكتملة ─────────────────────────────────────────
        ['my_projects_completed', ['مكتمل'],    ['مشروع', 'مشاريع', 'مشاريعي']],
        ['my_projects_completed', ['خلصت', 'انتهيت'], ['مشروع', 'مشاريع']],
        ['my_projects_completed', ['completed'], ['project']],

        // ── مشاريع نشطة ───────────────────────────────────────────
        ['my_projects_active', ['نشط', 'نشطة'], ['مشروع', 'مشاريع', 'مشاريعي']],
        ['my_projects_active', ['جاري', 'جارية'], ['مشروع', 'مشاريع']],
        ['my_projects_active', ['active'],      ['project']],

        // ── عدد المشاريع ──────────────────────────────────────────
        ['my_projects_count', ['كم', 'عدد'],   ['مشروع', 'مشاريع', 'مشاريعي']],
        ['my_projects_count', ['إحصاء'],        ['مشروع', 'مشاريع']],

        // ── جميع المشاريع (عام) ───────────────────────────────────
        ['my_projects', ['مشاريعي'],            []],
        ['my_projects', ['مشاريع'],             ['كل', 'جميع', 'عرض', 'اعرض', 'اظهر']],
        ['my_projects', ['projects'],           []],
    ];

    private array $queryLibrary = [

        // ─── إحصائيات شاملة ───────────────────────────────────────
        [
            'id'  => 'my_stats',
            'sql' => "
                SELECT
                    (SELECT COUNT(*) FROM tasks WHERE user_id = :uid AND deleted_at IS NULL) as total_tasks,
                    (SELECT COUNT(*) FROM tasks WHERE user_id = :uid AND is_completed = 1 AND deleted_at IS NULL) as completed_tasks,
                    (SELECT COUNT(*) FROM tasks WHERE user_id = :uid AND is_completed = 0 AND deleted_at IS NULL) as pending_tasks,
                    (SELECT COUNT(*) FROM tasks WHERE user_id = :uid AND is_completed = 0 AND end_time < NOW() AND deleted_at IS NULL) as overdue_tasks,
                    (SELECT COUNT(*) FROM tasks WHERE user_id = :uid AND is_completed = 0 AND end_time >= NOW() AND end_time <= DATE_ADD(NOW(), INTERVAL 7 DAY) AND deleted_at IS NULL) as due_this_week,
                    (SELECT COUNT(*) FROM project_user WHERE user_id = :uid) as total_projects,
                    (SELECT COUNT(*) FROM project_user pu JOIN projects p ON p.id = pu.project_id WHERE pu.user_id = :uid AND p.status = 'completed' AND p.deleted_at IS NULL) as completed_projects,
                    (SELECT COUNT(*) FROM project_user pu JOIN projects p ON p.id = pu.project_id WHERE pu.user_id = :uid AND p.status = 'active' AND p.deleted_at IS NULL) as active_projects
            ",
        ],

        // ─── مهام المستخدم ─────────────────────────────────────────
        [
            'id'  => 'my_tasks',
            'sql' => "
                SELECT
                    name        as الاسم,
                    CASE status
                        WHEN 'pending'     THEN '🕐 معلقة'
                        WHEN 'in_progress' THEN '⚙️ جارية'
                        WHEN 'completed'   THEN '✅ مكتملة'
                        ELSE status
                    END          as الحالة,
                    CASE priority
                        WHEN 'high'   THEN '🔴 عالية'
                        WHEN 'medium' THEN '🟡 متوسطة'
                        WHEN 'low'    THEN '🟢 منخفضة'
                        ELSE priority
                    END          as الأولوية,
                    DATE_FORMAT(end_time, '%Y-%m-%d') as تاريخ_الانتهاء,
                    CASE
                        WHEN end_time IS NULL      THEN '—'
                        WHEN is_completed = 1      THEN '✅ منجزة'
                        WHEN end_time < NOW()      THEN CONCAT('⚠️ متأخرة ', DATEDIFF(NOW(), end_time), ' يوم')
                        ELSE CONCAT('⏳ باقي ', DATEDIFF(end_time, NOW()), ' يوم')
                    END          as الوضع
                FROM tasks
                WHERE user_id = :uid AND deleted_at IS NULL
                ORDER BY
                    CASE WHEN end_time < NOW() AND is_completed = 0 THEN 0 ELSE 1 END,
                    end_time ASC
                LIMIT 30
            ",
        ],
        [
            'id'  => 'my_tasks_active',
            'sql' => "
                SELECT name as الاسم,
                    CASE priority WHEN 'high' THEN '🔴 عالية' WHEN 'medium' THEN '🟡 متوسطة' ELSE '🟢 منخفضة' END as الأولوية,
                    DATE_FORMAT(end_time, '%Y-%m-%d') as تاريخ_الانتهاء,
                    CASE WHEN end_time IS NULL THEN '—' WHEN end_time < NOW() THEN CONCAT('⚠️ متأخرة ', DATEDIFF(NOW(), end_time), ' يوم') ELSE CONCAT('⏳ باقي ', DATEDIFF(end_time, NOW()), ' يوم') END as الوضع
                FROM tasks WHERE user_id = :uid AND is_completed = 0 AND deleted_at IS NULL
                ORDER BY end_time ASC LIMIT 30
            ",
        ],
        [
            'id'  => 'my_tasks_pending',
            'sql' => "
                SELECT name as الاسم,
                    CASE priority WHEN 'high' THEN '🔴 عالية' WHEN 'medium' THEN '🟡 متوسطة' ELSE '🟢 منخفضة' END as الأولوية,
                    DATE_FORMAT(end_time, '%Y-%m-%d') as تاريخ_الانتهاء
                FROM tasks WHERE user_id = :uid AND is_completed = 0 AND deleted_at IS NULL
                ORDER BY end_time ASC LIMIT 30
            ",
        ],
        [
            'id'  => 'my_tasks_due_soon',
            'sql' => "
                SELECT name as الاسم,
                    CASE priority WHEN 'high' THEN '🔴 عالية' WHEN 'medium' THEN '🟡 متوسطة' ELSE '🟢 منخفضة' END as الأولوية,
                    DATE_FORMAT(end_time, '%Y-%m-%d') as تاريخ_الانتهاء,
                    CONCAT('⏳ باقي ', DATEDIFF(end_time, NOW()), ' يوم') as الوضع
                FROM tasks
                WHERE user_id = :uid AND is_completed = 0
                  AND end_time IS NOT NULL AND end_time >= NOW()
                  AND deleted_at IS NULL
                ORDER BY end_time ASC LIMIT 15
            ",
        ],
        [
            'id'  => 'my_tasks_overdue',
            'sql' => "
                SELECT name as الاسم,
                    CASE priority WHEN 'high' THEN '🔴 عالية' WHEN 'medium' THEN '🟡 متوسطة' ELSE '🟢 منخفضة' END as الأولوية,
                    DATE_FORMAT(end_time, '%Y-%m-%d') as تاريخ_الانتهاء,
                    CONCAT('⚠️ متأخرة ', DATEDIFF(NOW(), end_time), ' يوم') as الوضع
                FROM tasks
                WHERE user_id = :uid AND is_completed = 0
                  AND end_time IS NOT NULL AND end_time < NOW()
                  AND deleted_at IS NULL
                ORDER BY end_time ASC LIMIT 15
            ",
        ],
        [
            'id'  => 'my_tasks_completed',
            'sql' => "
                SELECT name as الاسم,
                    DATE_FORMAT(updated_at, '%Y-%m-%d') as تاريخ_الإنجاز,
                    CASE priority WHEN 'high' THEN '🔴 عالية' WHEN 'medium' THEN '🟡 متوسطة' ELSE '🟢 منخفضة' END as الأولوية
                FROM tasks WHERE user_id = :uid AND is_completed = 1 AND deleted_at IS NULL
                ORDER BY updated_at DESC LIMIT 100
            ",
        ],

        // ─── مهام المستخدم في مشروع معين (يستخدم :project_name) ──
        [
            'id'  => 'my_tasks_in_project',
            'sql' => "
                SELECT
                    t.name as المهمه,
                    CASE t.status
                        WHEN 'pending'     THEN '🕐 معلقه'
                        WHEN 'in_progress' THEN '⚙️ جاريه'
                        WHEN 'completed'   THEN '✅ مكتمله'
                        ELSE t.status
                    END as الحاله,
                    CASE t.priority
                        WHEN 'high'   THEN '🔴 عاليه'
                        WHEN 'medium' THEN '🟡 متوسطه'
                        WHEN 'low'    THEN '🟢 منخفضه'
                        ELSE t.priority
                    END as الاولويه,
                    DATE_FORMAT(t.end_time, '%Y-%m-%d') as تاريخ_الانتهاء,
                    CASE
                        WHEN t.end_time IS NULL       THEN '—'
                        WHEN t.is_completed = 1       THEN '✅ منجزه'
                        WHEN t.end_time < NOW()       THEN CONCAT('⚠️ متاخره ', DATEDIFF(NOW(), t.end_time), ' يوم')
                        ELSE CONCAT('⏳ باقي ', DATEDIFF(t.end_time, NOW()), ' يوم')
                    END as الوضع,
                    p.name as المشروع
                FROM tasks t
                INNER JOIN projects p ON p.id = t.project
                INNER JOIN project_user pu ON pu.project_id = p.id
                WHERE t.user_id = :uid
                  AND pu.user_id = :uid
                  AND p.name LIKE :project_name
                  AND t.deleted_at IS NULL
                  AND p.deleted_at IS NULL
                ORDER BY
                    CASE WHEN t.end_time < NOW() AND t.is_completed = 0 THEN 0 ELSE 1 END,
                    t.end_time ASC
                LIMIT 50
            ",
        ],
        [
            'id'  => 'my_tasks_count',
            'sql' => "
                SELECT
                    COUNT(*) as إجمالي_المهام,
                    SUM(is_completed) as المكتملة,
                    SUM(CASE WHEN is_completed = 0 THEN 1 ELSE 0 END) as غير_المكتملة,
                    SUM(CASE WHEN is_completed = 0 AND end_time < NOW() AND end_time IS NOT NULL THEN 1 ELSE 0 END) as المتأخرة
                FROM tasks WHERE user_id = :uid AND deleted_at IS NULL
            ",
        ],

        // ─── مشاريع المستخدم ───────────────────────────────────────
        [
            'id'  => 'my_projects',
            'sql' => "
                SELECT p.name as الاسم,
                    CASE p.status WHEN 'active' THEN '🟢 نشط' WHEN 'completed' THEN '✅ مكتمل' WHEN 'on_hold' THEN '⏸️ متوقف' ELSE p.status END as الحالة,
                    DATE_FORMAT(p.start_date, '%Y-%m-%d') as تاريخ_البدء,
                    DATE_FORMAT(p.end_date,   '%Y-%m-%d') as تاريخ_الانتهاء,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project = p.id AND t.deleted_at IS NULL) as إجمالي_المهام,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project = p.id AND t.is_completed = 1 AND t.deleted_at IS NULL) as المهام_المكتملة
                FROM projects p
                INNER JOIN project_user pu ON pu.project_id = p.id
                WHERE pu.user_id = :uid AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC LIMIT 30
            ",
        ],
        [
            'id'  => 'my_projects_active',
            'sql' => "
                SELECT p.name as الاسم,
                    DATE_FORMAT(p.start_date, '%Y-%m-%d') as تاريخ_البدء,
                    DATE_FORMAT(p.end_date,   '%Y-%m-%d') as تاريخ_الانتهاء,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project = p.id AND t.deleted_at IS NULL) as إجمالي_المهام,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project = p.id AND t.is_completed = 1 AND t.deleted_at IS NULL) as المهام_المكتملة,
                    CASE WHEN p.end_date IS NULL THEN '—' WHEN p.end_date < NOW() THEN CONCAT('⚠️ متأخر ', DATEDIFF(NOW(), p.end_date), ' يوم') ELSE CONCAT('⏳ باقي ', DATEDIFF(p.end_date, NOW()), ' يوم') END as الوضع
                FROM projects p
                INNER JOIN project_user pu ON pu.project_id = p.id
                WHERE pu.user_id = :uid AND p.status = 'active' AND p.deleted_at IS NULL
                ORDER BY p.end_date ASC
            ",
        ],
        [
            'id'  => 'my_projects_completed',
            'sql' => "
                SELECT p.name as الاسم,
                    DATE_FORMAT(p.start_date, '%Y-%m-%d') as تاريخ_البدء,
                    DATE_FORMAT(p.end_date,   '%Y-%m-%d') as تاريخ_الانتهاء,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project = p.id AND t.deleted_at IS NULL) as إجمالي_المهام
                FROM projects p
                INNER JOIN project_user pu ON pu.project_id = p.id
                WHERE pu.user_id = :uid AND p.status = 'completed' AND p.deleted_at IS NULL
                ORDER BY p.end_date DESC
            ",
        ],
        [
            'id'  => 'my_projects_count',
            'sql' => "
                SELECT
                    COUNT(*) as إجمالي_المشاريع,
                    SUM(CASE WHEN p.status = 'active'    THEN 1 ELSE 0 END) as النشطة,
                    SUM(CASE WHEN p.status = 'completed' THEN 1 ELSE 0 END) as المكتملة,
                    SUM(CASE WHEN p.status = 'on_hold'   THEN 1 ELSE 0 END) as المتوقفة
                FROM projects p
                INNER JOIN project_user pu ON pu.project_id = p.id
                WHERE pu.user_id = :uid AND p.deleted_at IS NULL
            ",
        ],
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');
    }

    // ─────────────────────────────────────────────────────────────
    // PUBLIC
    // ─────────────────────────────────────────────────────────────

    public function ask(string $userMessage, ?int $userId = null): string
    {
        $userMessage = strip_tags(trim($userMessage));

        if (strlen($userMessage) > 500) {
            return 'الرسالة طويلة جدًا. الحد الأقصى 500 حرف.';
        }

        if (empty($this->apiKey)) {
            return 'خطأ: أضف GEMINI_API_KEY في ملف .env';
        }

        if (!$userId) {
            return 'يجب تسجيل الدخول أولاً.';
        }

        // 1️⃣ الأوامر المباشرة
        if (trim($userMessage) === '__stats__') {
            return $this->executeQuery('my_stats', $userId);
        }

        // 2️⃣ مهام في مشروع معين — يُفحص قبل الـ local matching العام
        $projectName = $this->extractProjectName($userMessage);
        if ($projectName !== null) {
            Log::info("AI tasks_in_project [{$projectName}] for user [{$userId}]");
            return $this->executeTasksInProject($projectName, $userId);
        }

        // 3️⃣ Local keyword matching — سريع وموثوق
        $localMatch = $this->matchLocally($userMessage);
        if ($localMatch) {
            Log::info("AI local match [{$localMatch}] for: {$userMessage}");
            return $this->executeQuery($localMatch, $userId);
        }

        // 3️⃣ Gemini فقط للأسئلة التي لا يغطيها الـ local matching
        if (empty($this->apiKey)) {
            return 'لم أفهم طلبك. جرّب: "مهامي" أو "مشاريعي" أو "إحصائياتي".';
        }

        $prompt = $this->buildPrompt($userMessage);
        $reply  = $this->callGemini($prompt);

        if (!$reply) {
            return 'عذرًا، حدث خطأ في الاتصال.';
        }

        if (str_starts_with($reply, '__ERROR__:')) {
            return substr($reply, 10);
        }

        if (preg_match('/QUERY_ID:\s*(\w+)/i', $reply, $match)) {
            return $this->executeQuery(trim($match[1]), $userId);
        }

        if (preg_match('/NONE:\s*(.+)/is', $reply, $match)) {
            return trim($match[1]);
        }

        return $reply;
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE — Local Matching
    // ─────────────────────────────────────────────────────────────

    /**
     * Intent matching using signal-based rules.
     *
     * كل rule تحتوي على:
     *   [0] query_id
     *   [1] required_signals  — يجب أن يكون واحد على الأقل موجوداً
     *   [2] context_signals   — إذا كانت فارغة = يكفي required فقط
     *                           إذا كانت ممتلئة = يجب واحد منها أيضاً
     *
     * الترتيب في $intentRules من الأكثر تحديداً للأعم.
     */
    // ─────────────────────────────────────────────────────────────
    // PRIVATE — Project Name Extraction
    // ─────────────────────────────────────────────────────────────

    /**
     * يستخرج اسم المشروع من رسائل مثل:
     *   "مهامي في مشروع تطوير الموقع"
     *   "اعرض مهامي الخاصة بمشروع X"
     *   "مهامي في مشروع: تطوير"
     *
     * يُرجع null إذا لم يجد نمط مشروع.
     */
    private function extractProjectName(string $message): ?string
    {
        // أنماط البحث — مرتبة من الأكثر تحديداً للأعم
        $patterns = [
            // "مهامي في مشروع تطوير الموقع" أو "مهامي في مشروع X"
            '/(?:مهام[يه]?|tasks?).*?(?:في|بـ?|ب)\s+مشروع\s*[:\s]\s*(\S+(?:\s+\S+)*)/ui',
            // "مهامي الخاصة بمشروع نظام الإدارة"
            '/(?:مهام[يه]?|tasks?).*?بمشروع\s*[:\s]?\s*(\S+(?:\s+\S+)*)/ui',
            // "مهامي ضمن مشروع التسويق"
            '/(?:مهام[يه]?|tasks?).*?(?:ضمن|داخل)\s+مشروع\s*[:\s]?\s*(\S+(?:\s+\S+)*)/ui',
            // "مشروع X مهامي" — عكس الترتيب
            '/مشروع\s*[:\s]\s*(\S+(?:\s+\S+)?)\s+(?:مهام[يه]?|tasks?)/ui',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $name = trim($matches[1]);
                // حذف كلمات trailing غير مرغوبة
                $name = preg_replace('/\s*(اعرض|عرض|كل|جميع|الخاصه|الخاصة)$/ui', '', $name);
                $name = trim($name);
                if (mb_strlen($name) >= 2) {
                    return $name;
                }
            }
        }

        return null;
    }

    /**
     * تنفيذ استعلام المهام في مشروع معين مع LIKE search على اسم المشروع.
     */
    private function executeTasksInProject(string $projectName, int $userId): string
    {
        $query = collect($this->queryLibrary)->firstWhere('id', 'my_tasks_in_project');

        if (!$query) {
            return 'خطأ داخلي: query غير موجودة.';
        }

        // بناء SQL مع LIKE — آمن لأن القيم تُمرَّر كـ bindings لا string replacement
        $sql = str_replace(':uid', (int) $userId, trim($query['sql']));
        $sql = preg_replace('/\s+/', ' ', $sql);

        Log::info("AI tasks_in_project SQL for [{$projectName}] user [{$userId}]");

        try {
            // استخدام PDO binding لـ :project_name لحماية من SQL injection
            $results = DB::select(
                str_replace(':project_name', '?', $sql),
                ['%' . $projectName . '%']
            );

            if (empty($results)) {
                return "لم أجد مهاماً في مشروع يحتوي على \"**{$projectName}**\".\n"
                     . "تحقق من اسم المشروع أو جرّب جزءاً منه فقط.";
            }

            $count    = count($results);
            $projName = $results[0]->المشروع ?? $projectName;

            $out = "📋 **{$count} مهمة في مشروع «{$projName}»**\n" . str_repeat('─', 40) . "\n";

            foreach ($results as $i => $row) {
                $row  = (array) $row;
                $name = $row['المهمه'] ?? '';
                $out .= "\n" . ($i + 1) . ". **{$name}**\n";
                foreach (['الحاله','الاولويه','تاريخ_الانتهاء','الوضع'] as $col) {
                    $val = $row[$col] ?? null;
                    if ($val && $val !== '—') {
                        $label = str_replace('_', ' ', $col);
                        $out  .= "   • {$label}: {$val}\n";
                    }
                }
            }

            return $out;

        } catch (\Exception $e) {
            Log::error("AI tasks_in_project error: " . $e->getMessage());
            return 'حدث خطأ في جلب المهام. تحقق من اسم المشروع.';
        }
    }

    /**
     * تطبيع النص العربي لتوحيد الكتابة قبل المطابقة.
     */
    private function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));
        // توحيد الهمزات والألف
        $text = str_replace(['أ','إ','آ'], 'ا', $text);
        // توحيد التاء المربوطة
        $text = str_replace('ة', 'ه', $text);
        // توحيد الألف المقصورة
        $text = str_replace('ى', 'ي', $text);
        return $text;
    }

    /**
     * Intent matching — يفحص من الأكثر تحديداً للأعم.
     *
     * المنطق الجديد:
     * 1. نطبّع الرسالة أولاً
     * 2. نفحص الكلمات الحاسمة (specific_signals) أولاً — إذا وُجدت تحدد النوع فوراً
     * 3. ثم نفحص الموضوع (topic: مهام أم مشاريع)
     * 4. أخيراً نعود للـ general query
     */
    private function matchLocally(string $message): ?string
    {
        $msg = $this->normalize($message);

        $hasTasks    = str_contains($msg, 'مهام') || str_contains($msg, 'مهمه')
                    || str_contains($msg, 'task');
        $hasProjects = str_contains($msg, 'مشاريع') || str_contains($msg, 'مشروع')
                    || str_contains($msg, 'project');

        // ── إحصائيات عامة ─────────────────────────────────────────
        foreach (['احصائيات','ملخص','نظره عامه','داشبورد','dashboard','وضعي','تقريري'] as $kw) {
            if (str_contains($msg, $this->normalize($kw))) return 'my_stats';
        }

        // ── كلمات حاسمة تحدد النوع فوراً (بغض النظر عن topic) ──────
        $specificSignals = [
            // متأخرة → overdue
            'متاخر'         => ['tasks' => 'my_tasks_overdue', 'projects' => null],
            'overdue'       => ['tasks' => 'my_tasks_overdue', 'projects' => null],
            'فات وقت'       => ['tasks' => 'my_tasks_overdue', 'projects' => null],
            'فاتت'          => ['tasks' => 'my_tasks_overdue', 'projects' => null],

            // مكتملة / منتهية → completed
            'منتهيه'        => ['tasks' => 'my_tasks_completed', 'projects' => 'my_projects_completed'],
            'مكتمله'        => ['tasks' => 'my_tasks_completed', 'projects' => 'my_projects_completed'],
            'منجزه'         => ['tasks' => 'my_tasks_completed', 'projects' => null],
            'خلصت'          => ['tasks' => 'my_tasks_completed', 'projects' => 'my_projects_completed'],
            'انتهيت منها'   => ['tasks' => 'my_tasks_completed', 'projects' => 'my_projects_completed'],
            'completed'     => ['tasks' => 'my_tasks_completed', 'projects' => 'my_projects_completed'],

            // لم تبدأ / معلقة → pending
            'لم تبدا'       => ['tasks' => 'my_tasks_pending', 'projects' => null],
            'لم ابدا'       => ['tasks' => 'my_tasks_pending', 'projects' => null],
            'ما بدات'       => ['tasks' => 'my_tasks_pending', 'projects' => null],
            'معلقه'         => ['tasks' => 'my_tasks_pending', 'projects' => null],
            'غير مكتمله'   => ['tasks' => 'my_tasks_pending', 'projects' => null],
            'pending'       => ['tasks' => 'my_tasks_pending', 'projects' => null],

            // جارية / نشطة → active
            'جاريه'         => ['tasks' => 'my_tasks_active', 'projects' => 'my_projects_active'],
            'قيد التنفيذ'  => ['tasks' => 'my_tasks_active', 'projects' => null],
            'نشطه'          => ['tasks' => 'my_tasks_active', 'projects' => 'my_projects_active'],
            'الان'          => ['tasks' => 'my_tasks_active', 'projects' => 'my_projects_active'],
            'in progress'   => ['tasks' => 'my_tasks_active', 'projects' => null],
            'active'        => ['tasks' => 'my_tasks_active', 'projects' => 'my_projects_active'],

            // قريبة الانتهاء → due_soon
            'قريبه'         => ['tasks' => 'my_tasks_due_soon', 'projects' => null],
            'هذا الاسبوع'  => ['tasks' => 'my_tasks_due_soon', 'projects' => null],
            'عاجله'         => ['tasks' => 'my_tasks_due_soon', 'projects' => null],
            'due soon'      => ['tasks' => 'my_tasks_due_soon', 'projects' => null],

            // عدد / كم → count
            'كم'            => ['tasks' => 'my_tasks_count', 'projects' => 'my_projects_count'],
            'عدد'           => ['tasks' => 'my_tasks_count', 'projects' => 'my_projects_count'],
            'احصاء'         => ['tasks' => 'my_tasks_count', 'projects' => 'my_projects_count'],
            'how many'      => ['tasks' => 'my_tasks_count', 'projects' => 'my_projects_count'],
        ];

        foreach ($specificSignals as $signal => $map) {
            if (!str_contains($msg, $this->normalize($signal))) continue;

            // حُدّد النوع — الآن نحدد هل مهام أم مشاريع
            if ($hasProjects && $map['projects']) return $map['projects'];
            if ($hasTasks    && $map['tasks'])    return $map['tasks'];

            // إذا لم يُذكر الموضوع لكن الإشارة واضحة للمهام
            if ($map['tasks'] && !$hasProjects) return $map['tasks'];
        }

        // ── General queries (بعد استبعاد جميع الـ specific) ──────────
        if ($hasProjects) return 'my_projects';
        if ($hasTasks)    return 'my_tasks';

        return null;
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE — Query Execution
    // ─────────────────────────────────────────────────────────────

    private function executeQuery(string $queryId, int $userId): string
    {
        $query = collect($this->queryLibrary)->firstWhere('id', $queryId);

        if (!$query) {
            Log::warning("AI: unknown query_id [{$queryId}]");
            return 'لم أفهم طلبك. حاول بصياغة مختلفة.';
        }

        // استبدال :uid بـ ID المستخدم مباشرة (int — آمن من SQL injection)
        $sql = str_replace(':uid', (int) $userId, trim($query['sql']));
        $sql = preg_replace('/\s+/', ' ', $sql);

        Log::info("AI executing [{$queryId}] for user [{$userId}]");

        try {
            $results = DB::select($sql);

            if (empty($results)) {
                return $this->emptyMessage($queryId);
            }

            return $queryId === 'my_stats'
                ? $this->formatStats($results[0])
                : $this->formatResults($results);

        } catch (\Exception $e) {
            Log::error("AI SQL error [{$queryId}]: " . $e->getMessage());
            return 'حدث خطأ في جلب البيانات.';
        }
    }

    private function emptyMessage(string $queryId): string
    {
        return match(true) {
            str_contains($queryId, 'overdue')   => '✅ ممتاز! لا توجد مهام متأخرة.',
            str_contains($queryId, 'due_soon')  => '✅ لا توجد مهام ستنتهي قريبًا.',
            str_contains($queryId, 'completed') => 'لا توجد مهام مكتملة بعد.',
            str_contains($queryId, 'tasks')     => 'لا توجد مهام.',
            str_contains($queryId, 'projects')  => 'لا توجد مشاريع.',
            default                             => 'لا توجد نتائج.',
        };
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE — Gemini Prompt
    // ─────────────────────────────────────────────────────────────

    private function buildPrompt(string $userMessage): string
    {
        $ids = implode(', ', array_column($this->queryLibrary, 'id'));

        return <<<PROMPT
أنت مساعد لإدارة المهام. الاستعلامات المتاحة: {$ids}

قواعد الرد (التزم بها حرفياً):
- إذا ناسب السؤال استعلامًا: أجب فقط بـ QUERY_ID: [id]
- إذا كان السؤال عامًا: أجب فقط بـ NONE: [إجابة قصيرة]
- لا تكتب أي شيء آخر

سؤال: {$userMessage}
PROMPT;
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE — Format
    // ─────────────────────────────────────────────────────────────

    private function formatStats(object $row): string
    {
        $d = (array) $row;

        $total     = $d['total_tasks']        ?? 0;
        $completed = $d['completed_tasks']    ?? 0;
        $pending   = $d['pending_tasks']      ?? 0;
        $overdue   = $d['overdue_tasks']      ?? 0;
        $week      = $d['due_this_week']      ?? 0;
        $projects  = $d['total_projects']     ?? 0;
        $projDone  = $d['completed_projects'] ?? 0;
        $projActive= $d['active_projects']    ?? 0;

        $pct = $total > 0 ? round(($completed / $total) * 100) : 0;

        return "📊 **إحصائياتك**\n\n" .
               "📋 **المهام**\n" .
               "• الإجمالي: {$total}\n" .
               "• ✅ مكتملة: {$completed}\n" .
               "• 🕐 غير مكتملة: {$pending}\n" .
               "• ⚠️ متأخرة: {$overdue}\n" .
               "• ⏳ تنتهي هذا الأسبوع: {$week}\n" .
               "• 📈 نسبة الإنجاز: {$pct}%\n\n" .
               "🗂️ **المشاريع**\n" .
               "• الإجمالي: {$projects}\n" .
               "• 🟢 نشطة: {$projActive}\n" .
               "• ✅ مكتملة: {$projDone}\n" .
               ($overdue > 0 ? "\n⚠️ لديك {$overdue} مهمة متأخرة!" : "\n✅ لا توجد مهام متأخرة.");
    }

    private function formatResults(array $results): string
    {
        $count = count($results);
        $keys  = array_keys((array) $results[0]);
        $sep   = str_repeat('─', 40);

        $out = "📋 **{$count} نتيجة**\n{$sep}\n";

        foreach ($results as $i => $row) {
            $row  = (array) $row;
            $name = $row[$keys[0]] ?? '';
            $out .= "\n" . ($i + 1) . ". **{$name}**\n";

            foreach (array_slice($keys, 1) as $key) {
                $val = $row[$key] ?? null;
                if ($val !== null && $val !== '') {
                    $label = str_replace('_', ' ', $key);
                    $out  .= "   • {$label}: {$val}\n";
                }
            }
        }

        return $out;
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE — Gemini API
    // ─────────────────────────────────────────────────────────────

    private function callGemini(string $prompt, bool $useFallback = false): ?string
    {
        $model = $useFallback ? $this->fallbackModel : $this->primaryModel;
        $url   = $this->baseUrl . $model . ':generateContent?key=' . $this->apiKey;

        try {
            $response = Http::timeout(30)->post($url, [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 30,   // QUERY_ID: my_tasks = ~15 token فقط
                    'temperature'     => 0.0,  // صفر = حتمي تماماً
                ],
            ]);

            if ($response->status() === 429) {
                return $useFallback
                    ? '__ERROR__:تم تجاوز الحد المجاني. حاول بعد دقائق.'
                    : $this->callGemini($prompt, true);
            }

            if (!$response->successful()) {
                if (in_array($response->status(), [502, 503, 529]) && !$useFallback) {
                    sleep(1);
                    return $this->callGemini($prompt, true);
                }
                return '__ERROR__:خدمة Gemini غير متاحة مؤقتًا.';
            }

            $text = $response->json('candidates.0.content.parts.0.text');
            return empty($text) ? null : trim($text);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini connection: ' . $e->getMessage());
            return '__ERROR__:تعذّر الاتصال. تحقق من الإنترنت.';
        } catch (\Exception $e) {
            Log::error('Gemini exception: ' . $e->getMessage());
            return null;
        }
    }
}