<!-- AI CHAT -->
<div id="ai-chat-bot" class="{{ app()->getLocale() == 'ar' ? 'chat-right' : 'chat-left' }}">

    <button id="chat-toggle" title="AI Assistant">💬</button>

    <div id="chat-box">

        <!-- Header -->
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="chat-avatar">🤖</div>
                <div>
                    <div class="chat-title">AI Assistant</div>
                    <div class="chat-status" id="chat-status-text">متصل</div>
                </div>
            </div>
            <button id="chat-close" title="إغلاق">✕</button>
        </div>

        <!-- Messages -->
        <div class="chat-messages" id="chat-messages">
            <div class="message bot">
                <div class="message-content">
                    مرحبًا 👋 كيف أستطيع مساعدتك؟
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <button class="quick-btn" data-msg="__stats__">📊 إحصائياتي</button>
            <button class="quick-btn" data-msg="مهامي المتأخرة">⚠️ المتأخرة</button>
            <button class="quick-btn" data-msg="مهام تاريخ انتهائها قريب">⏳ القريبة</button>
            <button class="quick-btn" data-msg="مشاريعي النشطة">🗂️ مشاريعي</button>
        </div>

        <!-- Input -->
        <div class="chat-input-wrapper">
            <input type="text"
                   id="chat-input"
                   placeholder="اكتب رسالتك..."
                   maxlength="500"
                   autocomplete="off" />
            <button id="send-message" title="إرسال">➤</button>
        </div>

    </div>
</div>

<style>

#ai-chat-bot {
    position: fixed;
    bottom: 20px;
    z-index: 999999;
    display: flex;
    flex-direction: column;
}
.chat-left  { left: 20px;  align-items: flex-start; }
.chat-right { right: 20px; align-items: flex-end;   }

/* TOGGLE */
#chat-toggle {
    width: 65px; height: 65px;
    border-radius: 50%; border: none;
    background: linear-gradient(135deg, #7367f0, #5b50d6);
    color: white; font-size: 26px; cursor: pointer;
    box-shadow: 0 10px 30px rgba(115,103,240,.35);
    transition: .3s;
}
#chat-toggle:hover { transform: scale(1.08); }

/* BOX */
#chat-box {
    width: 380px;
    height: 560px;
    max-height: calc(100vh - 100px);
    background: #fff;
    border-radius: 24px;
    overflow: hidden;
    display: none;
    flex-direction: column;
    margin-bottom: 15px;
    box-shadow: 0 20px 60px rgba(0,0,0,.18);
    animation: chatFade .25s ease;
}

/* HEADER */
.chat-header {
    padding: 14px 18px;
    background: linear-gradient(135deg, #7367f0, #5b50d6);
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.chat-header-info { display: flex; align-items: center; gap: 12px; }
.chat-avatar {
    width: 42px; height: 42px; border-radius: 50%;
    background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center; font-size: 20px;
}
.chat-title  { font-weight: bold; font-size: 15px; }
.chat-status { font-size: 12px; opacity: .8; }
#chat-close {
    background: rgba(255,255,255,.2); border: none; color: white;
    width: 28px; height: 28px; border-radius: 50%; cursor: pointer;
    font-size: 12px; transition: .2s;
}
#chat-close:hover { background: rgba(255,255,255,.35); }

/* MESSAGES */
.chat-messages {
    flex: 1; padding: 16px;
    overflow-y: auto; background: #f8f8fb;
    display: flex; flex-direction: column; gap: 12px;
}
.message { display: flex; }
.message.bot  { justify-content: flex-start; }
.message.user { justify-content: flex-end;   }

.message-content {
    max-width: 85%;
    padding: 11px 15px;
    border-radius: 18px;
    font-size: 13.5px;
    line-height: 1.7;
    white-space: pre-wrap;
    word-break: break-word;
}
.message.bot .message-content {
    background: white; color: #333;
    border-bottom-left-radius: 5px;
    box-shadow: 0 2px 8px rgba(0,0,0,.07);
}
.message.user .message-content {
    background: #7367f0; color: white;
    border-bottom-right-radius: 5px;
}

/* BOLD في رسائل البوت */
.message.bot .message-content strong,
.message.bot .message-content b {
    font-weight: 700;
    color: #5b50d6;
}

/* QUICK ACTIONS */
.quick-actions {
    display: flex;
    gap: 6px;
    padding: 8px 12px;
    background: #f0f0f9;
    border-top: 1px solid #e8e8f0;
    overflow-x: auto;
    flex-shrink: 0;
}
.quick-actions::-webkit-scrollbar { height: 0; }
.quick-btn {
    white-space: nowrap;
    padding: 5px 12px;
    border-radius: 20px;
    border: 1.5px solid #7367f0;
    background: white;
    color: #7367f0;
    font-size: 12px;
    cursor: pointer;
    transition: .2s;
    flex-shrink: 0;
}
.quick-btn:hover {
    background: #7367f0;
    color: white;
}

/* INPUT */
.chat-input-wrapper {
    display: flex; align-items: center;
    padding: 12px 15px; background: white;
    border-top: 1px solid #eee; gap: 10px;
    flex-shrink: 0;
}
#chat-input {
    flex: 1; border: none; background: #f4f5f8;
    height: 44px; border-radius: 14px;
    padding: 0 16px; font-size: 13.5px; outline: none;
    direction: rtl;
}
#chat-input:focus { background: #eef0ff; }
#send-message {
    width: 44px; height: 44px; border-radius: 14px; border: none;
    background: linear-gradient(135deg, #7367f0, #5b50d6);
    color: white; font-size: 17px; cursor: pointer;
    transition: .25s; flex-shrink: 0;
}
#send-message:hover   { transform: scale(1.05); }
#send-message:disabled { opacity: .5; cursor: not-allowed; transform: none; }

/* TYPING */
.typing-dots { display: flex; gap: 5px; align-items: center; }
.typing-dots span {
    width: 8px; height: 8px; background: #7367f0;
    border-radius: 50%; animation: bounce .9s infinite;
}
.typing-dots span:nth-child(2) { animation-delay: .15s; }
.typing-dots span:nth-child(3) { animation-delay: .3s;  }
@keyframes bounce {
    0%,60%,100% { transform: translateY(0); }
    30%         { transform: translateY(-6px); }
}

/* SCROLL */
.chat-messages::-webkit-scrollbar { width: 5px; }
.chat-messages::-webkit-scrollbar-thumb { background: #d6d6e7; border-radius: 20px; }

@keyframes chatFade {
    from { opacity: 0; transform: translateY(20px) scale(.95); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const toggleBtn  = document.getElementById('chat-toggle');
    const closeBtn   = document.getElementById('chat-close');
    const chatBox    = document.getElementById('chat-box');
    const sendBtn    = document.getElementById('send-message');
    const input      = document.getElementById('chat-input');
    const messages   = document.getElementById('chat-messages');
    const statusText = document.getElementById('chat-status-text');

    if (!toggleBtn) return;

    // ─── Open / Close ─────────────────────────────────────────
    toggleBtn.addEventListener('click', () => toggleChat());
    closeBtn.addEventListener('click',  () => toggleChat(false));

    function toggleChat(force) {
        const isOpen = chatBox.style.display === 'flex';
        const open   = force !== undefined ? force : !isOpen;
        chatBox.style.display = open ? 'flex' : 'none';
        if (open) { input.focus(); scrollBottom(); }
    }

    // ─── Quick Action Buttons ─────────────────────────────────
    document.querySelectorAll('.quick-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const msg = btn.dataset.msg;
            if (msg) sendMessage(msg, btn.dataset.msg === '__stats__' ? null : msg);
        });
    });

    // ─── Send ─────────────────────────────────────────────────
    sendBtn.addEventListener('click', () => sendMessage());
    input.addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });

    function sendMessage(overrideMsg, displayMsg) {
        const message = overrideMsg ?? input.value.trim();
        if (!message || sendBtn.disabled) return;

        // عرض رسالة المستخدم (إلا إذا كانت __stats__)
        if (message !== '__stats__') {
            appendMessage('user', displayMsg ?? message);
        }
        input.value = '';
        setLoading(true);
        const typing = appendTyping();

        fetch('/admin/ai/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
            },
            body: JSON.stringify({ message })
        })
        .then(r => r.json())
        .then(data => {
            typing.remove();
            appendMessage('bot', data.reply ?? 'لم أتلقَّ ردًا.');
        })
        .catch(() => {
            typing.remove();
            appendMessage('bot', 'حدث خطأ في الاتصال. حاول مجددًا.');
        })
        .finally(() => setLoading(false));
    }

    // ─── Helpers ──────────────────────────────────────────────

    function appendMessage(role, text) {
        const wrap    = document.createElement('div');
        wrap.className = `message ${role}`;
        const content = document.createElement('div');
        content.className = 'message-content';

        if (role === 'bot') {
            // ✅ تحويل **نص** إلى bold بشكل آمن
            content.innerHTML = escapeHtml(text)
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        } else {
            content.textContent = text;
        }

        wrap.appendChild(content);
        messages.appendChild(wrap);
        scrollBottom();
    }

    function escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function appendTyping() {
        const wrap = document.createElement('div');
        wrap.className = 'message bot';
        wrap.innerHTML = `<div class="message-content typing-dots"><span></span><span></span><span></span></div>`;
        messages.appendChild(wrap);
        scrollBottom();
        return wrap;
    }

    function setLoading(state) {
        sendBtn.disabled = state;
        input.disabled   = state;
        statusText.textContent = state ? 'يكتب...' : 'متصل';
    }

    function scrollBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

});
</script>