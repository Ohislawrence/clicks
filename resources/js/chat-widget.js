const createChatWidget = () => {
    const rootId = 'ai-chat-widget-root';

    // If it already exists and has content, don't re-initialize
    let root = document.getElementById(rootId);
    if (root && root.querySelector('.ai-chat-widget')) {
        console.log('AI Chat Widget already exists, skipping init.');
        return;
    }

    if (!root) {
        root = document.createElement('div');
        root.id = rootId;
        document.body.appendChild(root);
    }

    // Add CSS for animations
    const styleId = 'ai-chat-widget-styles';
    if (!document.getElementById(styleId)) {
        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            @keyframes ai-slide-up {
                from { opacity: 0; transform: translateY(20px) scale(0.95); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }
            @keyframes ai-fade-in {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes ai-bounce-dots {
                0%, 80%, 100% { transform: translateY(0); }
                40% { transform: translateY(-5px); }
            }
            .ai-animate-slide-up { animation: ai-slide-up 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
            .ai-animate-fade-in { animation: ai-fade-in 0.2s ease-out; }
            .ai-dot { display: inline-block; width: 4px; height: 4px; border-radius: 50%; background: currentColor; margin: 0 1px; animation: ai-bounce-dots 1.4s infinite ease-in-out both; }
            .ai-dot:nth-child(1) { animation-delay: -0.32s; }
            .ai-dot:nth-child(2) { animation-delay: -0.16s; }
        `;
        document.head.appendChild(style);
    }

    root.className = 'fixed bottom-6 right-6 z-[9999] font-sans text-sm block';
    root.style.cssText = 'position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 9999 !important; display: block !important; visibility: visible !important; opacity: 1 !important; pointer-events: auto !important; width: auto !important; height: auto !important;';

    console.log('AI Chat Widget Initializing...');

    const svgIcons = {
        chat: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>',
        close: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>',
        send: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>',
        whatsapp: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.06 3.973L0 16l4.104-1.076a7.86 7.86 0 0 0 3.886 1.02h.004c4.368 0 7.926-3.558 7.93-7.93a7.9 7.9 0 0 0-2.323-5.688M7.994 14.521a6.57 6.57 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/></svg>',
    };

    root.innerHTML = `
        <div class="ai-chat-widget relative flex flex-col items-end">
            <!-- Toggle Button -->
            <button type="button" class="ai-chat-toggle group flex items-center justify-center w-14 h-14 rounded-2xl bg-slate-900 overflow-hidden text-white shadow-2xl transition-all duration-300 hover:scale-110 active:scale-95 ring-4 ring-slate-900/10"
                style="background-color: #0f172a; width: 56px; height: 56px; border-radius: 16px; border: none; cursor: pointer; display: flex !important; align-items: center; justify-content: center; color: white;"
                aria-label="Open chat">
                <div class="absolute inset-0 bg-gradient-to-tr from-blue-600 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <span class="relative z-10 transition-transform duration-300 group-hover:rotate-12">${svgIcons.chat}</span>
            </button>

            <!-- Chat Panel -->
            <div class="ai-chat-panel hidden absolute bottom-16 right-0 w-[22rem] max-w-[calc(100vw-2rem)] flex flex-col rounded-3xl overflow-hidden bg-white/95 backdrop-blur-xl border border-slate-200/50 shadow-[0_20px_50px_rgba(0,0,0,0.15)] ai-animate-slide-up">
                <!-- Header -->
                <div class="relative px-6 py-5 bg-slate-900 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-lg shadow-lg shadow-blue-500/20">
                                🤖
                            </div>
                            <div>
                                <h3 class="font-bold text-sm tracking-tight">ClicksIntel AI</h3>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Online Support</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="ai-chat-close-btn p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors">
                            ${svgIcons.close}
                        </button>
                    </div>
                </div>

                <!-- Messages Body -->
                <div class="ai-chat-body flex-1 min-h-[320px] max-h-[420px] overflow-y-auto p-5 space-y-4 bg-slate-50/50">
                    <div class="flex justify-start ai-animate-fade-in">
                        <div class="max-w-[85%] rounded-2xl px-4 py-3 bg-white border border-slate-200 text-slate-700 shadow-sm text-[13px] leading-relaxed">
                            Hello! I'm your ClicksIntel assistant. How can I help you today?
                        </div>
                    </div>
                </div>

                <!-- Footer / Form -->
                <div class="p-4 bg-white border-t border-slate-100">
                    <form class="ai-chat-form relative" novalidate>
                        <textarea id="ai-chat-input"
                            class="ai-chat-input w-full min-h-[50px] max-h-[120px] rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-[13px] placeholder-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none pr-12"
                            placeholder="Type your message..." rows="1"></textarea>
                        <button type="submit"
                            class="ai-chat-send absolute right-2 bottom-2 w-9 h-9 flex items-center justify-center rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition-all shadow-md shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                            ${svgIcons.send}
                        </button>
                    </form>

                    <a id="ai-chat-whatsapp-link"
                        class="hidden mt-3 flex items-center justify-center gap-2 w-full py-2.5 rounded-xl border border-emerald-100 bg-emerald-50 text-[11px] font-bold text-emerald-700 hover:bg-emerald-100 transition-all"
                        target="_blank" rel="noopener noreferrer">
                        ${svgIcons.whatsapp}
                        <span>CONTINUE ON WHATSAPP</span>
                    </a>
                </div>
            </div>
        </div>
    `;

    const panel = root.querySelector('.ai-chat-panel');
    const toggleBtn = root.querySelector('.ai-chat-toggle');
    const closeBtn = root.querySelector('.ai-chat-close-btn');
    const body = root.querySelector('.ai-chat-body');
    const form = root.querySelector('.ai-chat-form');
    const input = root.querySelector('.ai-chat-input');
    const whatsappLink = root.querySelector('#ai-chat-whatsapp-link');
    const sendBtn = root.querySelector('.ai-chat-send');

    if (window.whatsappSupportPhone) {
        whatsappLink.href = `https://wa.me/${window.whatsappSupportPhone}`;
        whatsappLink.classList.remove('hidden');
    }

    const scrollToBottom = () => {
        body.scrollTo({ top: body.scrollHeight, behavior: 'smooth' });
    };

    const createMessage = (text, type = 'assistant') => {
        const wrapper = document.createElement('div');
        wrapper.className = `flex ${type === 'user' ? 'justify-end' : 'justify-start'} ai-animate-fade-in`;

        const bubble = document.createElement('div');
        const userClasses = 'bg-blue-600 text-white shadow-lg shadow-blue-500/10';
        const assistantClasses = 'bg-white border border-slate-200 text-slate-700 shadow-sm';

        bubble.className = `max-w-[85%] break-words rounded-2xl px-4 py-3 text-[13px] leading-relaxed ${type === 'user' ? userClasses : assistantClasses}`;

        bubble.textContent = text;
        wrapper.appendChild(bubble);
        body.appendChild(wrapper);
        scrollToBottom();
    };

    const showTypingIndicator = () => {
        const id = 'ai-typing-indicator';
        if (document.getElementById(id)) return;

        const wrapper = document.createElement('div');
        wrapper.id = id;
        wrapper.className = 'flex justify-start ai-animate-fade-in';
        wrapper.innerHTML = `
            <div class="bg-white border border-slate-200 rounded-2xl px-4 py-2.5 text-slate-400">
                <span class="ai-dot"></span>
                <span class="ai-dot"></span>
                <span class="ai-dot"></span>
            </div>
        `;
        body.appendChild(wrapper);
        scrollToBottom();
    };

    const hideTypingIndicator = () => {
        const indicator = document.getElementById('ai-typing-indicator');
        if (indicator) indicator.remove();
    };

    const sendQuestion = async (question) => {
        createMessage(question, 'user');
        input.value = '';
        input.style.height = 'auto';
        sendBtn.disabled = true;

        showTypingIndicator();

        try {
            const response = await fetch((window.route && route('ai.chat')) || '/ai/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ question }),
            });

            const data = await response.json();
            hideTypingIndicator();

            if (!response.ok || !data.success) {
                createMessage(data.message || 'Unable to get an answer right now.', 'assistant');
                return;
            }

            createMessage(data.message || 'I could not generate a response.', 'assistant');
        } catch (error) {
            hideTypingIndicator();
            createMessage('There was a connection issue. Please try again.', 'assistant');
        } finally {
            sendBtn.disabled = false;
        }
    };

    // Auto-resize textarea
    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = (input.scrollHeight) + 'px';
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const question = input.value.trim();
        if (!question || sendBtn.disabled) return;
        sendQuestion(question);
    });

    toggleBtn.addEventListener('click', () => {
        const isHidden = panel.classList.contains('hidden');
        if (isHidden) {
            panel.classList.remove('hidden');
        } else {
            panel.classList.add('hidden');
        }
    });

    closeBtn.addEventListener('click', () => {
        panel.classList.add('hidden');
    });

    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !panel.classList.contains('hidden')) {
            panel.classList.add('hidden');
        }
    });
};


// Initialize on load with a small delay to ensure DOM is ready for mounting
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => setTimeout(createChatWidget, 500));
} else {
    setTimeout(createChatWidget, 500);
}
