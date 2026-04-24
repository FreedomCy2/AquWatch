<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat - AquWatch Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 text-slate-900">
    <main class="px-4 py-6 md:px-8">
        <div class="mx-auto max-w-5xl space-y-6">
            <section class="rounded-3xl border border-white/60 bg-white/75 p-6 shadow-xl backdrop-blur-md">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="inline-flex items-center gap-2 rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-800">
                            <i class="fas fa-crown text-amber-500"></i>
                            Pro Feature
                        </p>
                        <h1 class="mt-3 text-3xl font-extrabold text-cyan-950 md:text-4xl">AI Website Assistant</h1>
                        <p class="mt-1 text-cyan-900/80">Ask about current flood, rain, flow, risk, and sensor activity.</p>
                    </div>
                    <div class="flex gap-2">
                        <button id="chat-clear" type="button" class="rounded-xl bg-white/80 px-4 py-2 text-cyan-900 transition hover:bg-white">Clear Chat</button>
                        <a href="{{ route('contents.ai-insights') }}" class="rounded-xl bg-white/80 px-4 py-2 text-cyan-900 transition hover:bg-white">AI Insights</a>
                        <a href="{{ route('dashboard') }}" class="rounded-xl bg-cyan-700 px-4 py-2 text-white transition hover:bg-cyan-800">Dashboard</a>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-white/60 bg-white/75 p-4 shadow-xl backdrop-blur-md md:p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <aside class="rounded-2xl bg-white/70 p-3 md:col-span-1">
                        <h3 class="text-sm font-semibold text-cyan-900">Quick prompts</h3>
                        <p class="mt-1 text-xs text-cyan-800/80">Tap a prompt to ask instantly.</p>
                        <div class="mt-3 space-y-2" id="quick-prompt-list">
                            <button type="button" data-prompt="Give me a quick summary" class="quick-prompt w-full rounded-lg bg-cyan-50 px-3 py-2 text-left text-xs text-cyan-900 hover:bg-cyan-100">Give me a quick summary</button>
                            <button type="button" data-prompt="What should I do in this environment now?" class="quick-prompt w-full rounded-lg bg-cyan-50 px-3 py-2 text-left text-xs text-cyan-900 hover:bg-cyan-100">What should I do in this environment now?</button>
                            <button type="button" data-prompt="Is risk high right now?" class="quick-prompt w-full rounded-lg bg-cyan-50 px-3 py-2 text-left text-xs text-cyan-900 hover:bg-cyan-100">Is risk high right now?</button>
                            <button type="button" data-prompt="How many sensors are active?" class="quick-prompt w-full rounded-lg bg-cyan-50 px-3 py-2 text-left text-xs text-cyan-900 hover:bg-cyan-100">How many sensors are active?</button>
                            <button type="button" data-prompt="Any flood warning currently?" class="quick-prompt w-full rounded-lg bg-cyan-50 px-3 py-2 text-left text-xs text-cyan-900 hover:bg-cyan-100">Any flood warning currently?</button>
                            <button type="button" data-prompt="What to prepare if heavy rain continues?" class="quick-prompt w-full rounded-lg bg-cyan-50 px-3 py-2 text-left text-xs text-cyan-900 hover:bg-cyan-100">What to prepare if heavy rain continues?</button>
                        </div>
                    </aside>

                    <div class="md:col-span-3">
                        <div id="chat-log" class="h-[420px] overflow-y-auto space-y-3 rounded-2xl bg-white/70 p-4"></div>

                        <form id="chat-form" class="mt-4 flex gap-2">
                            <input
                                id="chat-input"
                                type="text"
                                maxlength="1000"
                                placeholder="Ask about flood, rain, flow, sensors, risk..."
                                class="flex-1 rounded-xl border border-cyan-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-cyan-400"
                                required
                            >
                            <button
                                id="chat-send"
                                type="submit"
                                class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-semibold text-white hover:bg-cyan-800"
                            >
                                Send
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        const askUrl = @json(route('contents.ai-chat.ask'));
        const clearUrl = @json(route('contents.ai-chat.clear'));
        const csrfToken = @json(csrf_token());
        const initialHistory = @json($chatHistory ?? []);

        const chatLog = document.getElementById('chat-log');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatSend = document.getElementById('chat-send');
        const chatClear = document.getElementById('chat-clear');
        const quickPromptButtons = document.querySelectorAll('.quick-prompt');

        function renderEmptyHint() {
            const node = document.createElement('div');
            node.className = 'max-w-[85%] rounded-2xl bg-cyan-100 px-4 py-3 text-sm text-cyan-900';
            node.textContent = 'Ask me about live conditions. Example: What is the flood status now?';
            chatLog.appendChild(node);
        }

        function addMessage(role, text) {
            const node = document.createElement('div');
            node.className = role === 'user'
                ? 'ml-auto max-w-[85%] rounded-2xl bg-slate-900 px-4 py-3 text-sm text-white'
                : 'max-w-[85%] rounded-2xl bg-cyan-100 px-4 py-3 text-sm text-cyan-900';

            node.textContent = text;
            chatLog.appendChild(node);
            chatLog.scrollTop = chatLog.scrollHeight;
        }

        function renderHistory() {
            chatLog.innerHTML = '';

            if (!Array.isArray(initialHistory) || initialHistory.length === 0) {
                renderEmptyHint();
                return;
            }

            for (const item of initialHistory) {
                if (!item || typeof item !== 'object') continue;
                if (item.role !== 'user' && item.role !== 'assistant') continue;
                if (typeof item.content !== 'string' || item.content.trim() === '') continue;
                addMessage(item.role, item.content.trim());
            }

            if (chatLog.children.length === 0) {
                renderEmptyHint();
            }
        }

        renderHistory();

        if (chatClear) {
            chatClear.addEventListener('click', async function () {
                chatClear.disabled = true;

                try {
                    const response = await fetch(clearUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });

                    if (response.ok) {
                        chatLog.innerHTML = '';
                        renderEmptyHint();
                    }
                } catch {
                    // Ignore clear errors and keep existing messages.
                } finally {
                    chatClear.disabled = false;
                }
            });
        }

        async function submitMessage(message) {
            if (!message) return;

            addMessage('user', message);
            chatInput.value = '';
            chatInput.focus();
            chatSend.disabled = true;

            try {
                const response = await fetch(askUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ message }),
                });

                const payload = await response.json();

                if (!response.ok || !payload.ok) {
                    addMessage('assistant', payload?.message ?? 'Unable to answer right now.');
                } else {
                    addMessage('assistant', payload.answer ?? 'No answer available.');
                }
            } catch {
                addMessage('assistant', 'Network issue while contacting AI chat endpoint.');
            } finally {
                chatSend.disabled = false;
            }
        }

        chatForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            const message = chatInput.value.trim();
            await submitMessage(message);
        });

        quickPromptButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                const prompt = String(button.getAttribute('data-prompt') || '').trim();
                if (!prompt) return;
                await submitMessage(prompt);
            });
        });
    </script>
</body>
</html>
