<!-- Chatbot Widget -->
<div class="chatbot" id="chatbot">
    <button class="chatbot__toggle" id="chatbotToggle" aria-label="Ouvrir le chatbot">
        <i class="fa-solid fa-robot"></i>
        <span class="chatbot__badge" id="chatbotBadge" style="display:none;">1</span>
    </button>
    
    <div class="chatbot__window" id="chatbotWindow" hidden>
        <div class="chatbot__header">
            <div class="chatbot__info">
                <i class="fa-solid fa-robot"></i>
                <div>
                    <strong>Assistant InnoGov</strong>
                    <small>En ligne</small>
                </div>
            </div>
            <button class="chatbot__close" id="chatbotClose"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div class="chatbot__messages" id="chatbotMessages">
            <div class="chatbot__msg chatbot__msg--bot">
                Bonjour ! Je suis votre assistant intelligent. Comment puis-je vous aider aujourd'hui ?
            </div>
        </div>
        
        <form class="chatbot__input" id="chatbotForm">
            <input type="text" id="chatbotInput" placeholder="Posez une question..." autocomplete="off">
            <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>
</div>

<script>
(function() {
    const toggle = document.getElementById('chatbotToggle');
    const windowEl = document.getElementById('chatbotWindow');
    const close = document.getElementById('chatbotClose');
    const form = document.getElementById('chatbotForm');
    const input = document.getElementById('chatbotInput');
    const messages = document.getElementById('chatbotMessages');
    const badge = document.getElementById('chatbotBadge');

    toggle.addEventListener('click', () => {
        const isHidden = windowEl.hasAttribute('hidden');
        if (isHidden) {
            windowEl.removeAttribute('hidden');
            input.focus();
            badge.style.display = 'none';
        } else {
            windowEl.setAttribute('hidden', '');
        }
    });

    close.addEventListener('click', () => {
        windowEl.setAttribute('hidden', '');
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

        // User message
        appendMessage(text, 'user');
        input.value = '';

        // Typing indicator
        const typingId = 'typing-' + Date.now();
        const typingEl = document.createElement('div');
        typingEl.className = 'chatbot__msg chatbot__msg--bot chatbot__msg--typing';
        typingEl.id = typingId;
        typingEl.innerHTML = '<span class="dot"></span><span class="dot"></span><span class="dot"></span>';
        messages.appendChild(typingEl);
        messages.scrollTop = messages.scrollHeight;

        try {
            const formData = new FormData();
            formData.append('message', text);

            const response = await fetch('<?= theme_url("CONTROLLER/ChatbotController.php") ?>', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            document.getElementById(typingId).remove();
            appendMessage(data.response, 'bot');
        } catch (err) {
            document.getElementById(typingId).remove();
            appendMessage("Désolé, une erreur est survenue.", 'bot');
        }
    });

    function appendMessage(text, type) {
        const msg = document.createElement('div');
        msg.className = `chatbot__msg chatbot__msg--${type}`;
        msg.textContent = text;
        messages.appendChild(msg);
        messages.scrollTop = messages.scrollHeight;
    }

    // Initial welcome logic
    setTimeout(() => {
        if (windowEl.hasAttribute('hidden')) {
            badge.style.display = 'flex';
        }
    }, 2000);
})();
</script>
