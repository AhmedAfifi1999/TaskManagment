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

    private array $queryLibrary = [

        // ─── إحصائيات شاملة ──────────────────────────────────────
        [
            'id'          => 'my_stats',
            'description' => 'إحصائياتي / ملخصي / نظرة عامة / dashboard',
            'sql'         => "
                SELECT
                    (SELECT COUNT(*) FROM tasks WHERE user_id = :user_id AND deleted_at IS NULL) as total_tasks,
                    (SELECT COUNT(*) FROM tasks WHERE user_id = :user_id AND is_completed = 1 AND deleted_at IS NULL) as completed_tasks,
                    (SELECT COUNT(*) FROM tasks WHERE user_id = :user_id AND is_completed = 0 AND deleted_at IS NULL) as pending_tasks,
                    (SELECT COUNT(*) FROM tasks WHERE user_id = :user_id AND is_completed = 0 AND end_time < NOW() AND deleted_at IS NULL) as overdue_tasks,
                    (SELECT COUNT(*) FROM tasks WHERE user_id = :user_id AND is_completed = 0 AND end_time >= NOW() AND end_time <= DATE_ADD(NOW(), INTERVAL 7 DAY) AND deleted_at IS NULL) as due_this_week,
                    (SELECT COUNT(*) FROM project_user WHERE user_id = :user_id) as total_projects,
                    (SELECT COUNT(*) FROM project_user pu JOIN projects p ON p.id = pu.project_id WHERE pu.user_id = :user_id AND p.status = 'completed' AND p.deleted_at IS NULL) as completed_projects,
                    (SELECT COUNT(*) FROM project_user pu JOIN projects p ON p.id = pu.project_id WHERE pu.user_id = :user_id AND p.status = 'active' AND p.deleted_at IS NULL) as active_projects
            ",
        ],

        // ─── مهام المستخدم ────────────────────────────────────────
        [
            'id'          => 'my_tasks',
            'description' => 'مهامي / المهام المسندة إليّ / شو المهام الي علي / عرض مهامي / كل مهامي',
            'sql'         => "
                SELECT
                    id,
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
                        WHEN end_time IS NULL           THEN '—'
                        WHEN is_completed = 1           THEN '✅ منجزة'
                        WHEN end_time < NOW()           THEN CONCAT('⚠️ متأخرة ', DATEDIFF(NOW(), end_time), ' يوم')
                        ELSE CONCAT('⏳ باقي ', DATEDIFF(end_time, NOW()), ' يوم')
                    END          as الوضع
                FROM tasks
                WHERE user_id = :user_id
                  AND deleted_at IS NULL
                ORDER BY
                    CASE WHEN end_time < NOW() AND is_completed = 0 THEN 0 ELSE 1 END,
                    end_time ASC
                LIMIT 30
            ",
        ],
        [
            'id'          => 'my_tasks_active',
            'description' => 'مهامي النشطة / الجارية / قيد التنفيذ',
            'sql'         => "
                SELECT
                    id,
                    name        as الاسم,
                    CASE priority
                        WHEN 'high'   THEN '🔴 عالية'
                        WHEN 'medium' THEN '🟡 متوسطة'
                        WHEN 'low'    THEN '🟢 منخفضة'
                        ELSE priority
                    END          as الأولوية,
                    DATE_FORMAT(end_time, '%Y-%m-%d') as تاريخ_الانتهاء,
                    CASE
                        WHEN end_time IS NULL  THEN '—'
                        WHEN end_time < NOW()  THEN CONCAT('⚠️ متأخرة ', DATEDIFF(NOW(), end_time), ' يوم')
                        ELSE CONCAT('⏳ باقي ', DATEDIFF(end_time, NOW()), ' يوم')
                    END          as الوضع
                FROM tasks
                WHERE user_id = :user_id
                  AND is_completed = 0
                  AND deleted_at IS NULL
                ORDER BY end_time ASC
                LIMIT 30
            ",
        ],
        [
            'id'          => 'my_tasks_pending',
            'description' => 'مهامي غير المكتملة / المهام المعلقة',
            'sql'         => "
                SELECT
                    id,
                    name        as الاسم,
                    CASE priority
                        WHEN 'high'   THEN '🔴 عالية'
                        WHEN 'medium' THEN '🟡 متوسطة'
                        WHEN 'low'    THEN '🟢 منخفضة'
                        ELSE priority
                    END          as الأولوية,
                    DATE_FORMAT(end_time, '%Y-%m-%d') as تاريخ_الانتهاء
                FROM tasks
                WHERE user_id = :user_id
                  AND is_completed = 0
                  AND deleted_at IS NULL
                ORDER BY end_time ASC
                LIMIT 30
            ",
        ],
        [
            'id'          => 'my_tasks_due_soon',
            'description' => 'مهامي القريبة الانتهاء / مهام تاريخ انتهائها قريب / المهام العاجلة / هذا الأسبوع',
            'sql'         => "
                SELECT
                    id,
                    name        as الاسم,
                    CASE priority
                        WHEN 'high'   THEN '🔴 عالية'
                        WHEN 'medium' THEN '🟡 متوسطة'
                        WHEN 'low'    THEN '🟢 منخفضة'
                        ELSE priority
                    END          as الأولوية,
                    DATE_FORMAT(end_time, '%Y-%m-%d') as تاريخ_الانتهاء,
                    CONCAT('⏳ باقي ', DATEDIFF(end_time, NOW()), ' يوم') as الوضع
                FROM tasks
                WHERE user_id = :user_id
                  AND is_completed = 0
                  AND end_time IS NOT NULL
                  AND end_time >= NOW()
                  AND deleted_at IS NULL
                ORDER BY end_time ASC
                LIMIT 15
            ",
        ],
        [
            'id'          => 'my_tasks_overdue',
            'description' => 'مهامي المتأخرة / المهام منتهية الصلاحية / المهام المتأخرة',
            'sql'         => "
                SELECT
                    id,
                    name        as الاسم,
                    CASE priority
                        WHEN 'high'   THEN '🔴 عالية'
                        WHEN 'medium' THEN '🟡 متوسطة'
                        WHEN 'low'    THEN '🟢 منخفضة'
                        ELSE priority
                    END          as الأولوية,
                    DATE_FORMAT(end_time, '%Y-%m-%d') as تاريخ_الانتهاء,
                    CONCAT('⚠️ متأخرة ', DATEDIFF(NOW(), end_time), ' يوم') as الوضع
                FROM tasks
                WHERE user_id = :user_id
                  AND is_completed = 0
                  AND end_time IS NOT NULL
                  AND end_time < NOW()
                  AND deleted_at IS NULL
                ORDER BY end_time ASC
                LIMIT 15
            ",
        ],
        [
            'id'          => 'my_tasks_completed',
            'description' => 'مهامي المكتملة / المنجزة',
            'sql'         => "
                SELECT
                    id,
                    name        as الاسم,
                    DATE_FORMAT(updated_at, '%Y-%m-%d') as تاريخ_الإنجاز,
                    CASE priority
                        WHEN 'high'   THEN '🔴 عالية'
                        WHEN 'medium' THEN '🟡 متوسطة'
                        WHEN 'low'    THEN '🟢 منخفضة'
                        ELSE priority
                    END          as الأولوية
                FROM tasks
                WHERE user_id = :user_id
                  AND is_completed = 1
                  AND deleted_at IS NULL
                ORDER BY updated_at DESC
                LIMIT 20
            ",
        ],
        [
            'id'          => 'my_tasks_count',
            'description' => 'عدد مهامي / كم مهمة عندي / إحصاء مهامي',
            'sql'         => "
                SELECT
                    COUNT(*)                                                      as إجمالي_المهام,
                    SUM(is_completed)                                             as المكتملة,
                    SUM(CASE WHEN is_completed = 0 THEN 1 ELSE 0 END)            as غير_المكتملة,
                    SUM(CASE WHEN is_completed = 0 AND end_time < NOW() AND end_time IS NOT NULL THEN 1 ELSE 0 END) as المتأخرة
                FROM tasks
                WHERE user_id = :user_id
                  AND deleted_at IS NULL
            ",
        ],

        // ─── مشاريع المستخدم ──────────────────────────────────────
        [
            'id'          => 'my_projects',
            'description' => 'مشاريعي / المشاريع التي أنا عضو فيها / شو المشاريع الي شغال عليها / كل مشاريعي',
            'sql'         => "
                SELECT
                    p.id,
                    p.name       as الاسم,
                    CASE p.status
                        WHEN 'active'    THEN '🟢 نشط'
                        WHEN 'completed' THEN '✅ مكتمل'
                        WHEN 'on_hold'   THEN '⏸️ متوقف'
                        ELSE p.status
                    END           as الحالة,
                    DATE_FORMAT(p.start_date, '%Y-%m-%d') as تاريخ_البدء,
                    DATE_FORMAT(p.end_date,   '%Y-%m-%d') as تاريخ_الانتهاء,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project = p.id AND t.deleted_at IS NULL)              as إجمالي_المهام,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project = p.id AND t.is_completed = 1 AND t.deleted_at IS NULL) as المهام_المكتملة
                FROM projects p
                INNER JOIN project_user pu ON pu.project_id = p.id
                WHERE pu.user_id = :user_id
                  AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC
                LIMIT 30
            ",
        ],
        [
            'id'          => 'my_projects_active',
            'description' => 'مشاريعي النشطة / المشاريع التي أعمل عليها الآن / المشاريع الجارية',
            'sql'         => "
                SELECT
                    p.id,
                    p.name       as الاسم,
                    DATE_FORMAT(p.start_date, '%Y-%m-%d') as تاريخ_البدء,
                    DATE_FORMAT(p.end_date,   '%Y-%m-%d') as تاريخ_الانتهاء,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project = p.id AND t.deleted_at IS NULL)              as إجمالي_المهام,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project = p.id AND t.is_completed = 1 AND t.deleted_at IS NULL) as المهام_المكتملة,
                    CASE
                        WHEN p.end_date IS NULL   THEN '—'
                        WHEN p.end_date < NOW()   THEN CONCAT('⚠️ متأخر ', DATEDIFF(NOW(), p.end_date), ' يوم')
                        ELSE CONCAT('⏳ باقي ', DATEDIFF(p.end_date, NOW()), ' يوم')
                    END           as الوضع
                FROM projects p
                INNER JOIN project_user pu ON pu.project_id = p.id
                WHERE pu.user_id = :user_id
                  AND p.status = 'active'
                  AND p.deleted_at IS NULL
                ORDER BY p.end_date ASC
            ",
        ],
        [
            'id'          => 'my_projects_completed',
            'description' => 'مشاريعي المكتملة / المشاريع التي انتهيت منها / خلصت',
            'sql'         => "
                SELECT
                    p.id,
                    p.name       as الاسم,
                    DATE_FORMAT(p.start_date, '%Y-%m-%d') as تاريخ_البدء,
                    DATE_FORMAT(p.end_date,   '%Y-%m-%d') as تاريخ_الانتهاء,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project = p.id AND t.deleted_at IS NULL) as إجمالي_المهام
                FROM projects p
                INNER JOIN project_user pu ON pu.project_id = p.id
                WHERE pu.user_id = :user_id
                  AND p.status = 'completed'
                  AND p.deleted_at IS NULL
                ORDER BY p.end_date DESC
            ",
        ],
        [
            'id'          => 'my_projects_count',
            'description' => 'عدد مشاريعي / كم مشروع عندي / إحصاء مشاريعي',
            'sql'         => "
                SELECT
                    COUNT(*)                                                                              as إجمالي_المشاريع,
                    SUM(CASE WHEN p.status = 'active'    THEN 1 ELSE 0 END)                              as النشطة,
                    SUM(CASE WHEN p.status = 'completed' THEN 1 ELSE 0 END)                              as المكتملة,
                    SUM(CASE WHEN p.status = 'on_hold'   THEN 1 ELSE 0 END)                              as المتوقفة
                FROM projects p
                INNER JOIN project_user pu ON pu.project_id = p.id
                WHERE pu.user_id = :user_id
                  AND p.deleted_at IS NULL
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

        // طلب الإحصائيات مباشرة بدون Gemini
        if (in_array(trim($userMessage), ['__stats__'])) {
            return $this->executeQueryById('my_stats', $userId);
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
            return $this->executeQueryById(trim($match[1]), $userId);
        }

        if (preg_match('/NONE:\s*(.+)/is', $reply, $match)) {
            return trim($match[1]);
        }

        return $reply;
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE — Query Logic
    // ─────────────────────────────────────────────────────────────

    private function executeQueryById(string $queryId, int $userId): string
    {
        $query = null;
        foreach ($this->queryLibrary as $q) {
            if ($q['id'] === $queryId) {
                $query = $q;
                break;
            }
        }

        if (!$query) {
            Log::warning("AI: unknown query_id [{$queryId}]");
            return 'لم أفهم طلبك. حاول بصياغة مختلفة.';
        }

        // استبدال كل :user_id بالـ ID الحقيقي
        $sql = str_replace(':user_id', (int) $userId, trim($query['sql']));
        $sql = preg_replace('/\s+/', ' ', $sql);

        Log::info("AI executing [{$queryId}] for user [{$userId}]");

        try {
            $results = DB::select($sql);

            if (empty($results)) {
                return 'لا توجد نتائج.';
            }

            // الإحصائيات تُعرض بشكل مختلف
            if ($queryId === 'my_stats') {
                return $this->formatStats($results[0]);
            }

            return $this->formatResults($results);

        } catch (\Exception $e) {
            Log::error("AI SQL error [{$queryId}]: " . $e->getMessage());
            return 'حدث خطأ في جلب البيانات.';
        }
    }

    private function buildPrompt(string $userMessage): string
    {
        $queryList = '';
        foreach ($this->queryLibrary as $q) {
            $queryList .= "- {$q['id']}: {$q['description']}\n";
        }

        return <<<PROMPT
أنت مساعد لإدارة المهام والمشاريع.

الاستعلامات المتاحة:
{$queryList}

تعليمات الرد (التزم بها حرفياً):
1. إذا ناسب السؤال أحد الاستعلامات، أجب فقط بـ:
   QUERY_ID: [id الاستعلام]

2. إذا كان السؤال عامًا، أجب فقط بـ:
   NONE: [إجابتك]

3. لا تكتب أي شيء آخر

سؤال المستخدم: {$userMessage}
PROMPT;
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE — Format
    // ─────────────────────────────────────────────────────────────

    private function formatStats(object $row): string
    {
        $data = (array) $row;

        $totalTasks     = $data['total_tasks']        ?? 0;
        $completedTasks = $data['completed_tasks']    ?? 0;
        $pendingTasks   = $data['pending_tasks']      ?? 0;
        $overdueTasks   = $data['overdue_tasks']      ?? 0;
        $dueThisWeek    = $data['due_this_week']      ?? 0;
        $totalProjects  = $data['total_projects']     ?? 0;
        $completedProj  = $data['completed_projects'] ?? 0;
        $activeProj     = $data['active_projects']    ?? 0;

        $taskPercent = $totalTasks > 0
            ? round(($completedTasks / $totalTasks) * 100)
            : 0;

        return "📊 **إحصائياتك**\n\n" .
               "━━━━━━━━━━━━━━━━━━\n" .
               "📋 **المهام**\n" .
               "• الإجمالي: {$totalTasks} مهمة\n" .
               "• ✅ المكتملة: {$completedTasks}\n" .
               "• 🕐 غير المكتملة: {$pendingTasks}\n" .
               "• ⚠️ المتأخرة: {$overdueTasks}\n" .
               "• ⏳ تنتهي هذا الأسبوع: {$dueThisWeek}\n" .
               "• 📈 نسبة الإنجاز: {$taskPercent}%\n\n" .
               "━━━━━━━━━━━━━━━━━━\n" .
               "🗂️ **المشاريع**\n" .
               "• الإجمالي: {$totalProjects} مشروع\n" .
               "• 🟢 النشطة: {$activeProj}\n" .
               "• ✅ المكتملة: {$completedProj}\n" .
               ($overdueTasks > 0
                   ? "\n⚠️ تنبيه: لديك {$overdueTasks} مهمة متأخرة!"
                   : "\n✅ أحسنت! لا توجد مهام متأخرة.");
    }

    private function formatResults(array $results): string
    {
        $count  = count($results);
        $first  = (array) $results[0];
        $keys   = array_keys($first);

        // ترويسة الجدول
        $header = implode(' | ', array_map(fn($k) => str_replace('_', ' ', $k), $keys));
        $sep    = str_repeat('─', min(strlen($header), 60));

        $output = "📋 **{$count} نتيجة**\n{$sep}\n";

        foreach ($results as $i => $row) {
            $row   = (array) $row;
            $num   = $i + 1;
            $name  = $row[$keys[0]] ?? '';

            // السطر الأول: الرقم + الاسم بارز
            $output .= "\n{$num}. **{$name}**\n";

            // باقي التفاصيل
            foreach (array_slice($keys, 1) as $key) {
                $val = $row[$key] ?? null;
                if (!is_null($val) && $val !== '') {
                    $label   = str_replace('_', ' ', $key);
                    $output .= "   • {$label}: {$val}\n";
                }
            }
        }

        return $output;
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
                    'maxOutputTokens' => 60,
                    'temperature'     => 0.1,
                ],
            ]);

            Log::info("Gemini [{$model}] status: " . $response->status());

            if ($response->status() === 429) {
                if (!$useFallback) return $this->callGemini($prompt, true);
                return '__ERROR__:تم تجاوز الحد المجاني. حاول لاحقًا.';
            }

            if (!$response->successful()) {
                $code = $response->status();

                // 503 / 502 / 529 → جرّب الموديل الثاني أولاً
                if (in_array($code, [502, 503, 529]) && !$useFallback) {
                    Log::warning("Gemini [{$model}] {$code}, retrying with fallback...");
                    sleep(1);
                    return $this->callGemini($prompt, true);
                }

                $errCode = $response->json('error.code');
                return match($errCode) {
                    403 => '__ERROR__:مفتاح API غير صالح.',
                    default => '__ERROR__:خدمة Gemini غير متاحة مؤقتًا. حاول بعد لحظات.',
                };
            }

            $text = $response->json('candidates.0.content.parts.0.text');

            if (empty($text)) {
                Log::warning("Gemini empty: " . $response->body());
                return null;
            }

            return trim($text);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini connection: ' . $e->getMessage());
            return '__ERROR__:تعذّر الاتصال. تحقق من الإنترنت.';
        } catch (\Exception $e) {
            Log::error('Gemini exception: ' . $e->getMessage());
            return null;
        }
    }
}