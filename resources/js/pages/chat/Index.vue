<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import { Send, Bot, User, Loader2, RefreshCw, Trash2, BookOpen, Tag, ExternalLink } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

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
    const response = await fetch('/chat/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',

      },
      credentials: 'same-origin',
      body: JSON.stringify({
        message: currentMessage,
        conversation_history: messages.value.slice(0, -1).map(msg => ({
          role: msg.role,
          content: msg.content,
          uniqueID: msg.uniqueID,
        })),
      }),
    });

    const data = await response.json();

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
        content: data.error || 'Sorry, I encountered an error. Please try again.',
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

    <div class="flex flex-col h-[calc(100vh-120px)] max-w-4xl mx-auto">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b bg-white dark:bg-slate-800 rounded-t-xl">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
            <Bot class="w-6 h-6 text-white" />
          </div>
          <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Catholic AI Chat Assistant</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Powered by CatéGPT - Catholic teachings & guidance</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <Button
            @click="clearChat"
            variant="outline"
            size="sm"
            class="text-gray-500 hover:text-red-500"
          >
            <Trash2 class="w-4 h-4 mr-2" />
            Clear Chat
          </Button>
        </div>
      </div>

      <!-- Messages Container -->
      <div
        ref="messagesContainer"
        class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50 dark:bg-slate-900"
      >
        <div v-if="messages.length === 0" class="flex flex-col items-center justify-center h-full text-center">
          <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4">
            <Bot class="w-8 h-8 text-white" />
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            Welcome to Catholic AI Chat Assistant
          </h3>
          <p class="text-gray-500 dark:text-gray-400 max-w-md">
            I'm here to help you with questions about Catholic teachings, prayers, and spiritual guidance. 
            How can I assist you today?
          </p>
        </div>

        <div v-for="message in messages" :key="message.id" class="flex gap-3">
          <!-- Avatar -->
          <div class="flex-shrink-0">
            <div 
              :class="[
                'w-8 h-8 rounded-full flex items-center justify-center',
                message.role === 'user' 
                  ? 'bg-blue-500 text-white' 
                  : 'bg-gradient-to-br from-purple-500 to-pink-500 text-white'
              ]"
            >
              <User v-if="message.role === 'user'" class="w-4 h-4" />
              <Bot v-else class="w-4 h-4" />
            </div>
          </div>

          <!-- Message Content -->
          <div class="flex-1 min-w-0">
            <div class="bg-white dark:bg-slate-800 rounded-lg p-4 shadow-sm">
              <!-- Message Text -->
              <div class="prose prose-sm max-w-none dark:prose-invert">
                <div v-html="message.content.replace(/\n/g, '<br>')" class="whitespace-pre-wrap"></div>
              </div>

              <!-- References Section -->
              <div v-if="message.references && message.references.length > 0" class="mt-4 pt-4 border-t border-gray-200 dark:border-slate-700">
                <div class="flex items-center gap-2 mb-2">
                  <BookOpen class="w-4 h-4 text-blue-500" />
                  <h4 class="text-sm font-medium text-gray-900 dark:text-white">References</h4>
                </div>
                <div class="space-y-1">
                  <a 
                    v-for="reference in message.references" 
                    :key="reference.url"
                    :href="reference.url" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="block text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 flex items-center gap-1"
                  >
                    <ExternalLink class="w-3 h-3" />
                    {{ reference.description }}
                  </a>
                </div>
              </div>

              <!-- Keywords Section -->
              <div v-if="message.keywords && message.keywords.length > 0" class="mt-3 pt-3 border-t border-gray-200 dark:border-slate-700">
                <div class="flex items-center gap-2 mb-2">
                  <Tag class="w-4 h-4 text-green-500" />
                  <h4 class="text-sm font-medium text-gray-900 dark:text-white">Keywords</h4>
                </div>
                <div class="flex flex-wrap gap-1">
                  <span 
                    v-for="keyword in message.keywords" 
                    :key="keyword"
                    class="inline-block px-2 py-1 text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full"
                  >
                    {{ keyword }}
                  </span>
                </div>
              </div>

              <!-- Related Questions -->
              <div v-if="message.others && message.others.length > 0" class="mt-3 pt-3 border-t border-gray-200 dark:border-slate-700">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Related Questions</h4>
                <div class="space-y-1">
                  <div 
                    v-for="question in message.others" 
                    :key="question"
                    class="text-sm text-gray-600 dark:text-gray-400 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400"
                    @click="newMessage = question; sendMessage()"
                  >
                    • {{ question }}
                  </div>
                </div>
              </div>

              <!-- Token Usage -->
              <div v-if="message.all_tokens" class="mt-3 pt-3 border-t border-gray-200 dark:border-slate-700">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                  Tokens used: {{ message.all_tokens }}
                </div>
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
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
              <Bot class="w-4 h-4 text-white" />
            </div>
          </div>
          <div class="flex-1">
            <div class="bg-white dark:bg-slate-800 rounded-lg p-4 shadow-sm">
              <div class="flex items-center gap-2">
                <Loader2 class="w-4 h-4 animate-spin text-blue-500" />
                <span class="text-sm text-gray-600 dark:text-gray-400">Thinking...</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Input Area -->
      <div class="p-6 border-t bg-white dark:bg-slate-800 rounded-b-xl">
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
            <Send v-if="!isLoading" class="w-4 h-4" />
            <Loader2 v-else class="w-4 h-4 animate-spin" />
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