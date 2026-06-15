document.addEventListener('DOMContentLoaded', function () {

    const sendBtn = document.getElementById('send-message');
    const input = document.getElementById('chat-input');
    const messages = document.querySelector('.chat-messages');

    if (!sendBtn || !input || !messages) {
        return;
    }

    sendBtn.addEventListener('click', sendMessage);

    input.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    function sendMessage() {

        let message = input.value.trim();

        if (message === '') return;

        // USER MESSAGE
        messages.innerHTML += `
            <div class="message user">
                <div class="message-content">
                    ${message}
                </div>
            </div>
        `;

        input.value = '';

        scrollBottom();

        fetch('/admin/ai/chat', {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },

            body: JSON.stringify({
                message: message
            })

        })
        .then(response => response.json())

        .then(data => {

            messages.innerHTML += `
                <div class="message bot">
                    <div class="message-content">
                        ${data.reply}
                    </div>
                </div>
            `;

            scrollBottom();

        })

        .catch(error => {

            console.error(error);

            messages.innerHTML += `
                <div class="message bot">
                    <div class="message-content">
                        حدث خطأ أثناء الاتصال
                    </div>
                </div>
            `;

        });

    }

    function scrollBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

});