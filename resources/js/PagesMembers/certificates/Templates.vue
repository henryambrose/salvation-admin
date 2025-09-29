<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Edit, Eye, FileText, Plus, Settings, Trash } from 'lucide-vue-next';
import { ref } from 'vue';

interface TemplateConfig {
  paper: string;
  orientation: string;
  margin: {
    top: number;
    right: number;
    bottom: number;
    left: number;
  };
  show_logo: boolean;
  logo_url: string;
}

interface CertificateTemplate {
  id: number;
  name: string;
  type: string;
  description?: string;
  template_content?: string;
  template_config?: TemplateConfig;
  is_default: boolean;
  is_active: boolean;
  created_by?: {
    id: number;
    name: string;
  };
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  templates: CertificateTemplate[];
  certificateTypes: Array<{ value: string; label: string }>;
  canManageCertificateTemplates: boolean;
  defaultLogoUrl: string;
}>();

// Modal states
const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingTemplate = ref<CertificateTemplate | null>(null);
console.log('Default Logo URL:', props.defaultLogoUrl);
// Form for creating/editing templates
const form = useForm({
  name: '',
  type: '',
  description: '',
  template_content: '',
  template_config: {
    paper: 'A4' as string,
    orientation: 'portrait' as string,
    margin: {
      top: 20,
      right: 20,
      bottom: 20,
      left: 20,
    },
    show_logo: true as boolean,
    logo_url: props.defaultLogoUrl,
  },
  is_active: true as boolean,
});

const breadcrumbs = [
  { title: 'Certificates', href: '/certificates' },
  { title: 'Templates', href: '/certificates/templates' },
];

function openCreateModal() {
  form.reset();
  form.is_active = true;
  showCreateModal.value = true;
}

function openEditModal(template: CertificateTemplate) {
  editingTemplate.value = template;
  form.name = template.name;
  form.type = template.type;
  form.description = template.description || '';
  form.template_content = template.template_content || '';

  // Ensure proper type structure for template_config
  const defaultConfig: TemplateConfig = {
    paper: 'A4',
    orientation: 'portrait',
    margin: { top: 20, right: 20, bottom: 20, left: 20 },
    show_logo: true,
    logo_url: props.defaultLogoUrl,
  };

  form.template_config = {
    paper: template.template_config?.paper || defaultConfig.paper,
    orientation: template.template_config?.orientation || defaultConfig.orientation,
    margin: template.template_config?.margin || defaultConfig.margin,
    show_logo: template.template_config?.show_logo !== undefined ? Boolean(template.template_config.show_logo) : defaultConfig.show_logo,
    logo_url: template.template_config?.logo_url || props.defaultLogoUrl,
  };

  form.is_active = template.is_active;
  showEditModal.value = true;
}

function createTemplate() {
  form.post('/certificates/templates', {
    onSuccess: () => {
      showCreateModal.value = false;
      form.reset();
    },
  });
}

function updateTemplate() {
  if (!editingTemplate.value) return;

  form.put(`/certificates/templates/${editingTemplate.value.id}`, {
    onSuccess: () => {
      showEditModal.value = false;
      editingTemplate.value = null;
      form.reset();
    },
  });
}

function deleteTemplate(template: CertificateTemplate) {
  if (template.is_default) {
    alert('Cannot delete default template');
    return;
  }

  if (confirm(`Are you sure you want to delete the template "${template.name}"?`)) {
    router.delete(`/certificates/templates/${template.id}`, {
      preserveState: true,
    });
  }
}

function toggleTemplateStatus(template: CertificateTemplate) {
  router.patch(
    `/certificates/templates/${template.id}`,
    {
      is_active: !template.is_active,
    },
    {
      preserveState: true,
    },
  );
}

function setAsDefault(template: CertificateTemplate) {
  router.patch(
    `/certificates/templates/${template.id}/set-default`,
    {},
    {
      preserveState: true,
    },
  );
}

function getTypeBadgeClass(type: string): string {
  const classes = {
    baptism: 'bg-blue-100 text-blue-800',
    confirmation: 'bg-green-100 text-green-800',
    marriage: 'bg-pink-100 text-pink-800',
    membership: 'bg-purple-100 text-purple-800',
    death: 'bg-gray-100 text-gray-800',
  };
  return classes[type as keyof typeof classes] || 'bg-gray-100 text-gray-800';
}

function previewTemplate(template: CertificateTemplate) {
  // Open preview in new window/tab
  window.open(`/certificates/templates/${template.id}/preview`, '_blank');
}

const defaultTemplateContent = `<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucfirst($certificate_type) }} Certificate</title>
    <style>
        body { font-family: 'Times New Roman', serif; margin: 0; padding: 20px; }
        .certificate { text-align: center; border: 2px solid #000; padding: 40px; position: relative; }
        .logo { position: absolute; top: 20px; left: 20px; max-width: 100px; max-height: 100px; }
        .header { font-size: 24px; font-weight: bold; margin-bottom: 30px; }
        .title { font-size: 28px; font-weight: bold; margin-bottom: 20px; }
        .content { font-size: 16px; line-height: 1.8; margin-bottom: 30px; }
        .signature { margin-top: 50px; }
    </style>
</head>
<body>
    <div class="certificate">
        @php
            $logoUrl = $template_config['logo_url'] ?? null;

            // Try to convert any local/public logo to base64 so Dompdf doesn't fetch over HTTP.
            if ($logoUrl && !str_starts_with($logoUrl, 'data:')) {
                // If it’s a relative URL like "/church-logo.png", normalize to a file path.
                $path = public_path(ltrim(parse_url($logoUrl, PHP_URL_PATH) ?? $logoUrl, '/'));

                if (is_file($path)) {
                    $mime = \Illuminate\Support\Facades\File::mimeType($path) ?: 'image/png';
                    $b64  = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                    $template_config['logo_url'] = $b64;  // overwrite with base64 for Dompdf
                }
            }
        @endphp
        @if(isset($template_config['show_logo']) && $template_config['show_logo'] && !empty($template_config['logo_url']))
          <img src="{{ $template_config['logo_url'] }}" class="logo" alt="Church Logo" style="display:block;max-width:100px;max-height:100px;">
        @else
            <!-- Debug: Logo config - Show: {{ isset($template_config['show_logo']) ? ($template_config['show_logo'] ? 'true' : 'false') : 'not set' }}, URL: {{ $template_config['logo_url'] ?? 'not set' }} -->
        @endif

        <div class="header">{{ parish_name }} Checking</div>
        <div class="title">CERTIFICATE OF {{ strtoupper($certificate_type) }}</div>

        <div class="content">
            <p>This is to certify that</p>
            <p><strong>{{ member_full_name }}</strong></p>
            <p>Member No: {{ member_member_no }}</p>

            @if($certificate_type == 'baptism')
                <p>was baptized on {{ $baptism_date }}</p>
                <p>Registration No: {{ $baptism_reg_no }}</p>
            @elseif($certificate_type == 'confirmation')
                <p>was confirmed on {{ $confirmation_date }}</p>
                <p>Registration No: {{ $confirmation_reg_no }}</p>
            @elseif($certificate_type == 'marriage')
                <p>was married to {{ $spouse_name }} on {{ $marriage_date }}</p>
                <p>Registration No: {{ $marriage_reg_no }}</p>
            @endif
        </div>

        <div class="signature">
            <p>Issued on: {{ $issued_date }}</p>
            <p>Issued by: {{ $issued_by }}</p>
            <br>
            <p>_________________________</p>
            <p>Pastor's Signature</p>
        </div>
    </div>
</body>
</html>`;
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Certificate Templates" />

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Certificate Templates</h1>
        <p class="mt-1 text-gray-600">Manage certificate templates for different types</p>
      </div>

      <Button
        v-if="canManageCertificateTemplates"
        @click="openCreateModal"
        class="flex items-center gap-2 bg-green-600 text-white hover:bg-green-700"
      >
        <Plus class="h-4 w-4" />
        Create Template
      </Button>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
      <Card v-for="template in templates" :key="template.id">
        <CardHeader class="pb-3">
          <div class="flex items-center justify-between">
            <CardTitle class="text-lg">{{ template.name }}</CardTitle>
            <div class="flex items-center gap-2">
              <Badge v-if="template.is_default" class="bg-yellow-100 text-yellow-800"> Default </Badge>
              <Badge :class="template.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                {{ template.is_active ? 'Active' : 'Inactive' }}
              </Badge>
            </div>
          </div>
          <Badge :class="getTypeBadgeClass(template.type)" class="w-fit capitalize">
            {{ template.type }}
          </Badge>
        </CardHeader>

        <CardContent class="space-y-4">
          <p v-if="template.description" class="text-sm text-gray-600">
            {{ template.description }}
          </p>

          <div class="text-xs text-gray-500">
            <p>Created: {{ new Date(template.created_at).toLocaleDateString() }}</p>
            <p v-if="template.created_by">By: {{ template.created_by.name }}</p>
            <p>Paper: {{ template.template_config?.paper || 'A4' }}</p>
            <p>Orientation: {{ template.template_config?.orientation || 'Portrait' }}</p>
          </div>

          <div class="flex flex-wrap gap-2">
            <Button @click="previewTemplate(template)" variant="outline" size="sm" class="flex items-center gap-1">
              <Eye class="h-4 w-4" />
              Preview
            </Button>

            <Button v-if="canManageCertificateTemplates" @click="openEditModal(template)" variant="outline" size="sm" class="flex items-center gap-1">
              <Edit class="h-4 w-4" />
              Edit
            </Button>

            <Button
              v-if="canManageCertificateTemplates && !template.is_default"
              @click="setAsDefault(template)"
              variant="outline"
              size="sm"
              class="flex items-center gap-1"
            >
              <Settings class="h-4 w-4" />
              Set Default
            </Button>

            <Button
              v-if="canManageCertificateTemplates"
              @click="toggleTemplateStatus(template)"
              variant="outline"
              size="sm"
              :class="template.is_active ? 'text-red-600 hover:text-red-700' : 'text-green-600 hover:text-green-700'"
            >
              {{ template.is_active ? 'Deactivate' : 'Activate' }}
            </Button>

            <Button
              v-if="canManageCertificateTemplates && !template.is_default"
              @click="deleteTemplate(template)"
              variant="outline"
              size="sm"
              class="flex items-center gap-1 text-red-600 hover:text-red-700"
            >
              <Trash class="h-4 w-4" />
              Delete
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Empty State -->
    <div v-if="templates.length === 0" class="py-12 text-center">
      <FileText class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">No templates found</h3>
      <p class="mt-1 text-sm text-gray-500">Get started by creating your first certificate template.</p>
      <div class="mt-6">
        <Button
          v-if="canManageCertificateTemplates"
          @click="openCreateModal"
          class="flex items-center gap-2 bg-green-600 text-white hover:bg-green-700"
        >
          <Plus class="h-4 w-4" />
          Create Template
        </Button>
      </div>
    </div>

    <!-- Create Template Modal -->
    <Dialog v-model:open="showCreateModal">
      <DialogContent class="max-h-[90vh] w-[95vw] max-w-[95vw] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>Create Certificate Template</DialogTitle>
        </DialogHeader>

        <form @submit.prevent="createTemplate" class="space-y-6">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <Label for="name">Template Name *</Label>
              <Input v-model="form.name" id="name" type="text" required placeholder="e.g., Modern Baptism Certificate" />
              <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
              <Label for="type">Certificate Type *</Label>
              <select v-model="form.type" id="type" required class="w-full rounded-md border border-gray-300 px-3 py-2">
                <option value="">Select certificate type</option>
                <option v-for="type in certificateTypes" :key="type.value" :value="type.value">
                  {{ type.label }}
                </option>
              </select>
              <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
            </div>
          </div>

          <div>
            <Label for="description">Description</Label>
            <Input v-model="form.description" id="description" type="text" placeholder="Brief description of this template" />
          </div>

          <!-- Template Configuration -->
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
              <Label for="paper">Paper Size</Label>
              <select v-model="form.template_config.paper" id="paper" class="w-full rounded-md border border-gray-300 px-3 py-2">
                <option value="A4">A4</option>
                <option value="A3">A3</option>
                <option value="Letter">Letter</option>
                <option value="Legal">Legal</option>
              </select>
            </div>

            <div>
              <Label for="orientation">Orientation</Label>
              <select v-model="form.template_config.orientation" id="orientation" class="w-full rounded-md border border-gray-300 px-3 py-2">
                <option value="portrait">Portrait</option>
                <option value="landscape">Landscape</option>
              </select>
            </div>

            <div>
              <Label for="logo">Logo Options</Label>
              <div class="space-y-2">
                <div class="flex items-center space-x-2">
                  <input v-model="form.template_config.show_logo" type="checkbox" id="show_logo" class="rounded border-gray-300" />
                  <Label for="show_logo">Show Logo</Label>
                </div>
                <div v-if="form.template_config.show_logo">
                  <Label for="logo_url">Logo URL</Label>
                  <input
                    v-model="form.template_config.logo_url"
                    type="url"
                    id="logo_url"
                    placeholder="https://example.com/logo.png"
                    class="w-full rounded-md border border-gray-300 px-3 py-2"
                  />
                  <p class="mt-1 text-xs text-gray-500">Enter the URL of your logo image</p>
                </div>
              </div>
            </div>

            <div class="flex items-center space-x-2 pt-6">
              <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded border-gray-300" />
              <Label for="is_active">Active</Label>
            </div>
          </div>

          <!-- Template Content -->
          <div>
            <Label for="template_content">HTML Template Content</Label>
            <Textarea
              v-model="form.template_content"
              id="template_content"
              :rows="15"
              :placeholder="defaultTemplateContent"
              class="font-mono text-sm"
            />
            <p class="mt-1 text-sm text-gray-600">
              Use Blade syntax for dynamic content. Available variables: member_full_name, certificate_type, issued_date, etc.
            </p>
          </div>

          <div class="flex justify-end gap-4">
            <Button type="button" variant="outline" @click="showCreateModal = false"> Cancel </Button>
            <Button type="submit" :disabled="form.processing" class="bg-green-600 text-white hover:bg-green-700">
              {{ form.processing ? 'Creating...' : 'Create Template' }}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Edit Template Modal -->
    <Dialog v-model:open="showEditModal">
      <DialogContent class="max-h-[90vh] w-[95vw] max-w-[95vw] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>Edit Certificate Template</DialogTitle>
        </DialogHeader>

        <form @submit.prevent="updateTemplate" class="space-y-6">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <Label for="edit_name">Template Name *</Label>
              <Input v-model="form.name" id="edit_name" type="text" required />
              <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
              <Label for="edit_type">Certificate Type *</Label>
              <select v-model="form.type" id="edit_type" required class="w-full rounded-md border border-gray-300 px-3 py-2">
                <option value="">Select certificate type</option>
                <option v-for="type in certificateTypes" :key="type.value" :value="type.value">
                  {{ type.label }}
                </option>
              </select>
            </div>
          </div>

          <div>
            <Label for="edit_description">Description</Label>
            <Input v-model="form.description" id="edit_description" type="text" />
          </div>

          <!-- Template Configuration -->
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
              <Label for="edit_paper">Paper Size</Label>
              <select v-model="form.template_config.paper" id="edit_paper" class="w-full rounded-md border border-gray-300 px-3 py-2">
                <option value="A4">A4</option>
                <option value="A3">A3</option>
                <option value="Letter">Letter</option>
                <option value="Legal">Legal</option>
              </select>
            </div>

            <div>
              <Label for="edit_orientation">Orientation</Label>
              <select v-model="form.template_config.orientation" id="edit_orientation" class="w-full rounded-md border border-gray-300 px-3 py-2">
                <option value="portrait">Portrait</option>
                <option value="landscape">Landscape</option>
              </select>
            </div>

            <div>
              <Label for="edit_logo">Logo Options</Label>
              <div class="space-y-2">
                <div class="flex items-center space-x-2">
                  <input v-model="form.template_config.show_logo" type="checkbox" id="edit_show_logo" class="rounded border-gray-300" />
                  <Label for="edit_show_logo">Show Logo</Label>
                </div>
                <div v-if="form.template_config.show_logo">
                  <Label for="edit_logo_url">Logo URL</Label>
                  <input
                    v-model="form.template_config.logo_url"
                    type="url"
                    id="edit_logo_url"
                    placeholder="https://example.com/logo.png"
                    class="w-full rounded-md border border-gray-300 px-3 py-2"
                  />
                  <p class="mt-1 text-xs text-gray-500">Enter the URL of your logo image</p>
                </div>
              </div>
            </div>

            <div class="flex items-center space-x-2 pt-6">
              <input v-model="form.is_active" type="checkbox" id="edit_is_active" class="rounded border-gray-300" />
              <Label for="edit_is_active">Active</Label>
            </div>
          </div>

          <!-- Template Content -->
          <div>
            <Label for="edit_template_content">HTML Template Content</Label>
            <Textarea v-model="form.template_content" id="edit_template_content" :rows="15" class="font-mono text-sm" />
          </div>

          <div class="flex justify-end gap-4">
            <Button type="button" variant="outline" @click="showEditModal = false"> Cancel </Button>
            <Button type="submit" :disabled="form.processing" class="bg-blue-600 text-white hover:bg-blue-700">
              {{ form.processing ? 'Updating...' : 'Update Template' }}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
