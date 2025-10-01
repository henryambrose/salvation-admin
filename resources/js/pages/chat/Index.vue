<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { BookOpen, Bot, ExternalLink, Loader2, Send, Tag, Trash2, User } from 'lucide-vue-next';
import { nextTick, onMounted, ref } from 'vue';

interface Message {
  id: string;
  role: 'user' | 'assistant';
  content: string;
  timestamp: Date;
  uniqueID?: string;
  references?: Array<{ description: string; url: string }>;
  keywords?: string[];
  others?: string[];
  all_tokens?: string;
}

const messages = ref<Message[]>([]);
const newMessage = ref('');
const isLoading = ref(false);
const messagesContainer = ref<HTMLElement>();

const sendMessage = async () => {
  if (!newMessage.value.trim() || isLoading.value) return;

  const userMessage: Message = {
    id: Date.now().toString(),
    role: 'user',
    content: newMessage.value.trim(),
    timestamp: new Date(),
  };

  messages.value.push(userMessage);
  const currentMessage = newMessage.value;
  newMessage.value = '';
  isLoading.value = true;

  try {
    // Get fresh CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const response = await fetch('/chat/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        message: currentMessage,
        conversation_history: messages.value.slice(0, -1).map((msg) => ({
          role: msg.role,
          content: msg.content,
          uniqueID: msg.uniqueID,
        })),
      }),
    });

    const data = await response.json();

    // Handle CSRF token mismatch
    if (response.status === 419) {
      const errorMessage: Message = {
        id: (Date.now() + 1).toString(),
        role: 'assistant',
        content: 'Your session has expired. Please refresh the page and try again.',
        timestamp: new Date(),
      };
      messages.value.push(errorMessage);
      return;
    }

    if (response.ok && data.success) {
      const assistantMessage: Message = {
        id: (Date.now() + 1).toString(),
        role: 'assistant',
        content: data.message,
        timestamp: new Date(),
        uniqueID: data.uniqueID,
        references: data.references,
        keywords: data.keywords,
        others: data.others,
        all_tokens: data.all_tokens,
      };
      messages.value.push(assistantMessage);
    } else {
      // Handle error response
      const errorMessage: Message = {
        id: (Date.now() + 1).toString(),
        role: 'assistant',
        content: data.error || data.message || 'Sorry, I encountered an error. Please try again.',
        timestamp: new Date(),
      };
      messages.value.push(errorMessage);
    }
  } catch (error) {
    console.error('Error sending message:', error);
    const errorMessage: Message = {
      id: (Date.now() + 1).toString(),
      role: 'assistant',
      content: 'Sorry, I encountered an error. Please try again.',
      timestamp: new Date(),
    };
    messages.value.push(errorMessage);
  } finally {
    isLoading.value = false;
    await nextTick();
    scrollToBottom();
  }
};

const clearChat = () => {
  messages.value = [];
};

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
};

const handleKeyPress = (event: KeyboardEvent) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault();
    sendMessage();
  }
};

onMounted(() => {
  scrollToBottom();
});
</script>

<template>
  <AppLayout>
    <Head title="Catholic AI Chat Assistant" />

    <div class="mx-auto flex h-[calc(100vh-120px)] max-w-4xl flex-col">
      <!-- Header -->
      <div class="flex items-center justify-between rounded-t-xl border-b bg-[#ffffff] p-6 dark:bg-slate-800">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600">
            <Bot class="h-[1.5rem] w-[1.5rem] text-white" />
          </div>
          <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Catholic AI Chat Assistant</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Powered by CatéGPT - Catholic teachings & guidance</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <Button @click="clearChat" variant="outline" size="sm" class="text-gray-500 hover:text-red-500">
            <Trash2 class="mr-2 h-[1rem] w-[1rem]" />
            Clear Chat
          </Button>
        </div>
      </div>

      <!-- Messages Container -->
      <div ref="messagesContainer" class="flex-1 space-y-4 overflow-y-auto bg-gray-50 p-6 dark:bg-slate-900">
        <div v-if="messages.length === 0" class="flex h-full flex-col items-center justify-center text-center">
          <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600">
            <Bot class="h-8 w-8 text-white" />
          </div>
          <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Welcome to Catholic AI Chat Assistant</h3>
          <p class="max-w-[448px] text-gray-500 dark:text-gray-400">
            I'm here to help you with questions about Catholic teachings, prayers, and spiritual guidance. How can I assist you today?
          </p>
        </div>

        <div v-for="message in messages" :key="message.id" class="flex gap-3">
          <!-- Avatar -->
          <div class="flex-shrink-0">
            <div
              :class="[
                'flex h-8 w-8 items-center justify-center rounded-full',
                message.role === 'user' ? 'bg-blue-500 text-white' : 'bg-gradient-to-br from-purple-500 to-pink-500 text-white',
              ]"
            >
              <User v-if="message.role === 'user'" class="h-[1rem] w-[1rem]" />
              <Bot v-else class="h-[1rem] w-[1rem]" />
            </div>
          </div>

          <!-- Message Content -->
          <div class="min-w-0 flex-1">
            <div class="rounded-lg bg-[#ffffff] p-4 shadow-sm dark:bg-slate-800">
              <!-- Message Text -->
              <div class="prose prose-sm dark:prose-invert max-w-none">
                <div v-html="message.content.replace(/\n/g, '<br>')" class="whitespace-pre-wrap"></div>
              </div>

              <!-- References Section -->
              <div v-if="message.references && message.references.length > 0" class="mt-4 border-t border-gray-200 pt-4 dark:border-slate-700">
                <div class="mb-2 flex items-center gap-2">
                  <BookOpen class="h-[1rem] w-[1rem] text-blue-500" />
                  <h4 class="text-sm font-medium text-gray-900 dark:text-white">References</h4>
                </div>
                <div class="space-y-1">
                  <a
                    v-for="reference in message.references"
                    :key="reference.url"
                    :href="reference.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                  >
                    <ExternalLink class="h-3 w-3" />
                    {{ reference.description }}
                  </a>
                </div>
              </div>

              <!-- Keywords Section -->
              <div v-if="message.keywords && message.keywords.length > 0" class="mt-3 border-t border-gray-200 pt-3 dark:border-slate-700">
                <div class="mb-2 flex items-center gap-2">
                  <Tag class="h-[1rem] w-[1rem] text-green-500" />
                  <h4 class="text-sm font-medium text-gray-900 dark:text-white">Keywords</h4>
                </div>
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="keyword in message.keywords"
                    :key="keyword"
                    class="inline-block rounded-full bg-green-100 px-2 py-1 text-xs text-green-800 dark:bg-green-900 dark:text-green-200"
                  >
                    {{ keyword }}
                  </span>
                </div>
              </div>

              <!-- Related Questions -->
              <div v-if="message.others && message.others.length > 0" class="mt-3 border-t border-gray-200 pt-3 dark:border-slate-700">
                <h4 class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Related Questions</h4>
                <div class="space-y-1">
                  <div
                    v-for="question in message.others"
                    :key="question"
                    class="cursor-pointer text-sm text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400"
                    @click="
                      newMessage = question;
                      sendMessage();
                    "
                  >
                    • {{ question }}
                  </div>
                </div>
              </div>

              <!-- Token Usage -->
              <div v-if="message.all_tokens" class="mt-3 border-t border-gray-200 pt-3 dark:border-slate-700">
                <div class="text-xs text-gray-500 dark:text-gray-400">Tokens used: {{ message.all_tokens }}</div>
              </div>

              <!-- Timestamp -->
              <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ new Date(message.timestamp).toLocaleTimeString() }}
              </div>
            </div>
          </div>
        </div>

        <!-- Loading Indicator -->
        <div v-if="isLoading" class="flex gap-3">
          <div class="flex-shrink-0">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-pink-500">
              <Bot class="h-[1rem] w-[1rem] text-white" />
            </div>
          </div>
          <div class="flex-1">
            <div class="rounded-lg bg-[#ffffff] p-4 shadow-sm dark:bg-slate-800">
              <div class="flex items-center gap-2">
                <Loader2 class="h-[1rem] w-[1rem] animate-spin text-blue-500" />
                <span class="text-sm text-gray-600 dark:text-gray-400">Thinking...</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Input Area -->
      <div class="rounded-b-xl border-t bg-[#ffffff] p-6 dark:bg-slate-800">
        <div class="flex gap-3">
          <div class="flex-1">
            <Input
              v-model="newMessage"
              placeholder="Ask about Catholic teachings, prayers, or spiritual guidance..."
              @keypress="handleKeyPress"
              :disabled="isLoading"
              class="w-full"
            />
          </div>
          <Button
            @click="sendMessage"
            :disabled="!newMessage.trim() || isLoading"
            class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700"
          >
            <Send v-if="!isLoading" class="h-[1rem] w-[1rem]" />
            <Loader2 v-else class="h-[1rem] w-[1rem] animate-spin" />
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.prose {
  max-width: none;
}

.prose p {
  margin: 0;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: #475569;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #64748b;
}
</style>
